<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\Response;

class GoogleDriveService
{
    /**
     * Store a file. If GDrive is enabled, upload to structured GDrive folders and return 'gdrive/{fileId}/{fileName}'.
     * Otherwise, fallback to local disk storage with original file name.
     */
    public function storeFile(
        UploadedFile $file,
        string $folder = 'lms/tugas',
        array $hierarchy = [],
        ?string $customFileName = null
    ): string {
        $rawName = $customFileName ?: $file->getClientOriginalName();
        $safeName = $this->sanitizeFileName($rawName);

        if ($this->isDriveEnabled()) {
            $drivePath = $this->uploadToDrive($file, $safeName, $hierarchy);
            if ($drivePath) {
                return $drivePath;
            }
        }

        $timestamp = time();
        $finalLocalName = $timestamp.'_'.$safeName;
        $file->storeAs($folder, $finalLocalName, 'public');

        return $folder.'/'.$finalLocalName;
    }

    public function sanitizeFileName(string $name): string
    {
        $name = preg_replace('/[^\w\.\-\s]/u', '', $name);

        return trim($name) ?: 'file_'.time().'.pdf';
    }

    public function getConfigPath(): string
    {
        return app()->runningUnitTests()
            ? storage_path('app/google-drive/test_config.json')
            : storage_path('app/google-drive/config.json');
    }

    public function getBackupConfigPath(): string
    {
        $path = $this->getConfigPath();
        $base = basename($path, '.json');

        return dirname($path).'/.'.$base.'.backup.json';
    }

    public function getDriveConfig(): array
    {
        $configPath = $this->getConfigPath();
        $backupPath = $this->getBackupConfigPath();

        if (File::exists($configPath)) {
            $config = json_decode(File::get($configPath), true);
            if (is_array($config) && ! empty($config)) {
                return $config;
            }
        }

        if (File::exists($backupPath)) {
            $config = json_decode(File::get($backupPath), true);
            if (is_array($config) && ! empty($config)) {
                // Pulihkan file utama jika hilang
                File::put($configPath, json_encode($config, JSON_PRETTY_PRINT));

                return $config;
            }
        }

        return [];
    }

    public function isDriveEnabled(): bool
    {
        $config = $this->getDriveConfig();
        $enabled = isset($config['enabled'])
            ? filter_var($config['enabled'], FILTER_VALIDATE_BOOLEAN)
            : filter_var(env('GOOGLE_DRIVE_ENABLED', false), FILTER_VALIDATE_BOOLEAN);

        if (! $enabled) {
            return false;
        }

        $folderId = $this->getRootFolderId();
        if (empty($folderId)) {
            return false;
        }

        $hasOAuth = ! empty($config['oauth_refresh_token'] ?? env('GOOGLE_DRIVE_REFRESH_TOKEN'));
        $hasServiceAccount = File::exists(storage_path('app/google-drive/service-account.json'));

        return $hasOAuth || $hasServiceAccount;
    }

    public function getRootFolderId(): ?string
    {
        $config = $this->getDriveConfig();
        $folderId = ! empty($config['folder_id'])
            ? trim($config['folder_id'])
            : trim((string) env('GOOGLE_DRIVE_FOLDER_ID', ''));

        if (empty($folderId)) {
            return null;
        }

        if (preg_match('/folders\/([a-zA-Z0-9_\-]+)/', $folderId, $matches)) {
            return $matches[1];
        }

        return trim($folderId, ' /\\');
    }

    public function getAccessToken(): ?string
    {
        $oauthToken = $this->getAccessTokenFromOAuth();
        if ($oauthToken) {
            return $oauthToken;
        }

        $jsonPath = storage_path('app/google-drive/service-account.json');

        return $this->getAccessTokenFromServiceAccount($jsonPath);
    }

    public function getAccessTokenFromOAuth(): ?string
    {
        $config = $this->getDriveConfig();
        $refreshToken = $config['oauth_refresh_token'] ?? env('GOOGLE_DRIVE_REFRESH_TOKEN');
        $clientId = $config['oauth_client_id'] ?? env('GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = $config['oauth_client_secret'] ?? env('GOOGLE_DRIVE_CLIENT_SECRET');

        if (empty($refreshToken) || empty($clientId) || empty($clientSecret)) {
            return null;
        }

        $cacheKey = 'gdrive_oauth_token_'.md5($refreshToken);

        return Cache::remember($cacheKey, 3000, function () use ($clientId, $clientSecret, $refreshToken) {
            try {
                $response = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'refresh_token' => $refreshToken,
                    'grant_type' => 'refresh_token',
                ]);

                if ($response->successful()) {
                    return $response->json('access_token');
                }

                Log::error('Google OAuth Refresh Token Failed: '.$response->body());

                return null;
            } catch (\Throwable $e) {
                Log::error('Google OAuth Refresh Exception: '.$e->getMessage());

                return null;
            }
        });
    }

    public function getConnectedEmail(): ?string
    {
        $config = $this->getDriveConfig();

        return $config['oauth_connected_email'] ?? env('GOOGLE_DRIVE_CONNECTED_EMAIL') ?: null;
    }

    public function getOrCreateFolder(string $folderName, string $parentFolderId, string $accessToken): ?string
    {
        $cleanName = str_replace(["'", '\\', '"', '/'], '', trim($folderName));
        if ($cleanName === '') {
            return $parentFolderId;
        }

        $cacheKey = 'gdrive_f_'.md5($parentFolderId.'_'.$cleanName);
        if ($cached = Cache::get($cacheKey)) {
            return $cached;
        }

        try {
            $query = sprintf(
                "mimeType='application/vnd.google-apps.folder' and name='%s' and '%s' in parents and trashed=false",
                $cleanName,
                $parentFolderId
            );

            $searchResponse = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get('https://www.googleapis.com/drive/v3/files', [
                'q' => $query,
                'fields' => 'files(id, name)',
                'supportsAllDrives' => 'true',
                'includeItemsFromAllDrives' => 'true',
            ]);

            if ($searchResponse->successful()) {
                $files = $searchResponse->json('files', []);
                if (! empty($files[0]['id'])) {
                    $folderId = $files[0]['id'];
                    Cache::put($cacheKey, $folderId, now()->addDay());

                    return $folderId;
                }
            }

            $createResponse = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->post('https://www.googleapis.com/drive/v3/files?supportsAllDrives=true', [
                'name' => $cleanName,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => [$parentFolderId],
            ]);

            if ($createResponse->successful()) {
                $folderId = $createResponse->json('id');
                if ($folderId) {
                    Cache::put($cacheKey, $folderId, now()->addDay());

                    return $folderId;
                }
            } else {
                Log::error('Google Drive Create Folder Error: '.$createResponse->body());
            }
        } catch (\Throwable $e) {
            Log::error('Google Drive Create Folder Exception: '.$e->getMessage());
        }

        return null;
    }

    private function uploadToDrive(UploadedFile $file, string $fileName, array $hierarchy = []): ?string
    {
        try {
            $rootFolderId = $this->getRootFolderId();
            if (! $rootFolderId) {
                return null;
            }

            $accessToken = $this->getAccessToken();
            if (! $accessToken) {
                return null;
            }

            $targetFolderId = $rootFolderId;
            foreach ($hierarchy as $folderSegment) {
                $folderSegment = trim((string) $folderSegment);
                if ($folderSegment !== '') {
                    $nextFolderId = $this->getOrCreateFolder($folderSegment, $targetFolderId, $accessToken);
                    if ($nextFolderId) {
                        $targetFolderId = $nextFolderId;
                    }
                }
            }

            $mimeType = $file->getClientMimeType() ?: 'application/octet-stream';
            $fileContent = File::get($file->getRealPath());

            $metadata = [
                'name' => $fileName,
                'parents' => [$targetFolderId],
            ];

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->attach(
                'metadata', json_encode($metadata), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8']
            )->attach(
                'file', $fileContent, $fileName, ['Content-Type' => $mimeType]
            )->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true');

            if ($response->successful()) {
                $fileData = $response->json();
                $driveFileId = $fileData['id'] ?? null;
                if ($driveFileId) {
                    return 'gdrive/'.$driveFileId.'/'.$fileName;
                }
            } else {
                Log::error('Google Drive Upload Error: '.$response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Google Drive Upload Exception: '.$e->getMessage());
        }

        return null;
    }

    public function streamFileResponse(string $fileId, string $fileName): Response
    {
        $accessToken = $this->getAccessToken();
        abort_unless($accessToken, 500, 'Gagal mengautentikasi ke Google Drive.');

        $metaResponse = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
        ])->get("https://www.googleapis.com/drive/v3/files/{$fileId}?fields=id,name,mimeType&supportsAllDrives=true");

        $mimeType = 'application/octet-stream';
        if ($metaResponse->successful()) {
            $mimeType = $metaResponse->json('mimeType') ?: $mimeType;
        }

        $response = Http::withoutVerifying()->withHeaders([
            'Authorization' => 'Bearer '.$accessToken,
        ])->get("https://www.googleapis.com/drive/v3/files/{$fileId}?alt=media&supportsAllDrives=true");

        abort_unless($response->successful(), 404, 'Berkas di Google Drive tidak ditemukan atau gagal diunduh.');

        return response($response->body(), 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_INLINE,
                $fileName
            ),
        ]);
    }

    public function deleteFile(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        if (str_starts_with($path, 'gdrive/')) {
            $parts = explode('/', $path);
            $driveFileId = $parts[1] ?? null;
            if ($driveFileId) {
                return $this->deleteFromDrive($driveFileId);
            }

            return false;
        }

        return Storage::disk('public')->delete($path);
    }

    public function deleteFromDrive(string $fileId): bool
    {
        try {
            $accessToken = $this->getAccessToken();
            if (! $accessToken) {
                return false;
            }

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->delete("https://www.googleapis.com/drive/v3/files/{$fileId}?supportsAllDrives=true");

            return $response->successful();
        } catch (\Throwable $e) {
            Log::error('Google Drive Delete Exception: '.$e->getMessage());

            return false;
        }
    }

    public function testConnection(): array
    {
        try {
            $jsonPath = storage_path('app/google-drive/service-account.json');
            if (! File::exists($jsonPath)) {
                return [
                    'success' => false,
                    'message' => 'File kredensial service-account.json belum diunggah.',
                ];
            }

            $folderId = $this->getRootFolderId();
            if (! $folderId) {
                return [
                    'success' => false,
                    'message' => 'Folder ID Google Drive belum diisi.',
                ];
            }

            $accessToken = $this->getAccessToken();
            if (! $accessToken) {
                return [
                    'success' => false,
                    'message' => 'Gagal mendapatkan Access Token OAuth2 dari Google. Pastikan file JSON kredensial valid.',
                ];
            }

            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get("https://www.googleapis.com/drive/v3/files/{$folderId}?fields=id,name,mimeType&supportsAllDrives=true");

            if (! $response->successful()) {
                $err = $response->json()['error']['message'] ?? $response->body();

                return [
                    'success' => false,
                    'message' => "Folder ID tidak dapat diakses ({$response->status()}): {$err}. Pastikan Email Service Account telah ditambahkan sebagai Editor di folder GDrive.",
                ];
            }

            $metadata = [
                'name' => 'eduva_connection_test_'.time().'.txt',
                'parents' => [$folderId],
            ];

            $uploadResponse = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->attach(
                'metadata', json_encode($metadata), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8']
            )->attach(
                'file', 'EDUVA LMS GOOGLE DRIVE CONNECTION TEST OK', 'test.txt', ['Content-Type' => 'text/plain']
            )->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true');

            if (! $uploadResponse->successful()) {
                $err = $uploadResponse->json()['error']['message'] ?? $uploadResponse->body();

                if (str_contains($err, 'Service Accounts do not have storage quota')) {
                    return [
                        'success' => false,
                        'message' => 'Service Account tidak memiliki kuota (0 MB) untuk mengunggah ke folder "Drive Saya". Solusi: Hubungkan Akun Google Kampus (OAuth 2.0) di form pengaturan, atau gunakan Drive Bersama (Shared Drive).',
                    ];
                }

                return [
                    'success' => false,
                    'message' => "Gagal mengunggah file uji coba ke folder: {$err}. Pastikan akun yang digunakan memiliki izin akses ke folder GDrive.",
                ];
            }

            $fileData = $uploadResponse->json();
            $testFileId = $fileData['id'] ?? null;

            if ($testFileId) {
                Http::withoutVerifying()->withHeaders([
                    'Authorization' => 'Bearer '.$accessToken,
                ])->delete("https://www.googleapis.com/drive/v3/files/{$testFileId}?supportsAllDrives=true");
            }

            $connectedEmail = $this->getConnectedEmail();
            $hasOAuth = ! empty($this->getDriveConfig()['oauth_refresh_token'] ?? env('GOOGLE_DRIVE_REFRESH_TOKEN'));
            $authInfo = $hasOAuth
                ? 'OAuth 2.0 Akun Google'.($connectedEmail ? " ({$connectedEmail})" : '')
                : 'Service Account';

            return [
                'success' => true,
                'message' => "Koneksi Google Drive BERHASIL 100%! Berhasil menguji unggah dan hapus berkas menggunakan {$authInfo}.",
            ];
        } catch (\Throwable $e) {
            Log::error('Google Drive Test Connection Exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Google Drive: '.$e->getMessage(),
            ];
        }
    }

    private function getImpersonateEmail(): ?string
    {
        $configPath = $this->getConfigPath();
        if (File::exists($configPath)) {
            $config = json_decode(File::get($configPath), true) ?? [];
            if (! empty($config['impersonate_email'])) {
                return trim($config['impersonate_email']);
            }
        }

        $envEmail = env('GOOGLE_DRIVE_IMPERSONATE_EMAIL');

        return ! empty($envEmail) ? trim($envEmail) : null;
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function getAccessTokenFromServiceAccount(string $jsonPath): ?string
    {
        try {
            if (! File::exists($jsonPath)) {
                return null;
            }

            $credentials = json_decode(File::get($jsonPath), true);
            if (! $credentials || empty($credentials['client_email']) || empty($credentials['private_key'])) {
                return null;
            }

            $header = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $now = time();

            $payloadData = [
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/drive',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ];

            $impersonateEmail = $this->getImpersonateEmail();
            if ($impersonateEmail) {
                $payloadData['sub'] = $impersonateEmail;
            }

            $payload = $this->base64UrlEncode(json_encode($payloadData));

            $signatureInput = $header.'.'.$payload;
            $privateKey = $credentials['private_key'];

            $signed = @openssl_sign($signatureInput, $signature, $privateKey, 'SHA256');
            if (! $signed) {
                Log::error('Google OAuth Token Sign Failed: RSA private key invalid');

                return null;
            }

            $jwt = $signatureInput.'.'.$this->base64UrlEncode($signature);

            $response = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'] ?? null;
            }

            Log::error('Google OAuth Token Request Error: '.$response->body());

            return null;
        } catch (\Throwable $e) {
            Log::error('Google OAuth Token Exception: '.$e->getMessage());

            return null;
        }
    }
}
