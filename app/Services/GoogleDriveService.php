<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    /**
     * Upload a file with its original name. If GDrive is enabled and credentials exist, upload to GDrive.
     * Otherwise, fallback to local disk storage with original file name.
     */
    public function storeFile(UploadedFile $file, string $folder = 'lms/tugas'): string
    {
        $originalName = $file->getClientOriginalName();
        $safeName = $this->sanitizeFileName($originalName);

        if ($this->isDriveEnabled()) {
            $drivePath = $this->uploadToDrive($file, $folder, $safeName);
            if ($drivePath) {
                return $drivePath;
            }
        }

        $timestamp = time();
        $finalLocalName = $timestamp.'_'.$safeName;
        $file->storeAs($folder, $finalLocalName, 'public');

        return $folder.'/'.$finalLocalName;
    }

    private function sanitizeFileName(string $name): string
    {
        $name = preg_replace('/[^\w\.\-\s]/u', '', $name);

        return trim($name) ?: 'file_'.time().'.pdf';
    }

    public function isDriveEnabled(): bool
    {
        $configPath = storage_path('app/google-drive/config.json');
        $jsonPath = storage_path('app/google-drive/service-account.json');

        if (! File::exists($jsonPath)) {
            return false;
        }

        if (File::exists($configPath)) {
            $config = json_decode(File::get($configPath), true) ?? [];
            if (isset($config['enabled'])) {
                return filter_var($config['enabled'], FILTER_VALIDATE_BOOLEAN) && ! empty($config['folder_id']);
            }
        }

        return filter_var(env('GOOGLE_DRIVE_ENABLED', false), FILTER_VALIDATE_BOOLEAN) && ! empty(env('GOOGLE_DRIVE_FOLDER_ID'));
    }

    private function uploadToDrive(UploadedFile $file, string $folder, string $fileName): ?string
    {
        try {
            $jsonPath = storage_path('app/google-drive/service-account.json');
            $configPath = storage_path('app/google-drive/config.json');

            $folderId = env('GOOGLE_DRIVE_FOLDER_ID', '');
            if (File::exists($configPath)) {
                $config = json_decode(File::get($configPath), true) ?? [];
                if (! empty($config['folder_id'])) {
                    $folderId = $config['folder_id'];
                }
            }

            if (! $folderId) {
                return null;
            }

            $accessToken = $this->getAccessTokenFromServiceAccount($jsonPath);
            if (! $accessToken) {
                return null;
            }

            $mimeType = $file->getClientMimeType() ?: 'application/octet-stream';
            $fileContent = File::get($file->getRealPath());

            $metadata = [
                'name' => $fileName,
                'parents' => [$folderId],
            ];

            $response = Http::withHeaders([
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
                    Http::withHeaders([
                        'Authorization' => 'Bearer '.$accessToken,
                    ])->post("https://www.googleapis.com/drive/v3/files/{$driveFileId}/permissions?supportsAllDrives=true", [
                        'role' => 'reader',
                        'type' => 'anyone',
                    ]);
                }
            } else {
                Log::error('Google Drive Upload Error: '.$response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Google Drive Upload Exception: '.$e->getMessage());
        }

        $timestamp = time();
        $finalLocalName = $timestamp.'_'.$fileName;
        $file->storeAs($folder, $finalLocalName, 'public');

        return $folder.'/'.$finalLocalName;
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

            $configPath = storage_path('app/google-drive/config.json');
            $folderId = env('GOOGLE_DRIVE_FOLDER_ID', '');
            if (File::exists($configPath)) {
                $config = json_decode(File::get($configPath), true) ?? [];
                if (! empty($config['folder_id'])) {
                    $folderId = $config['folder_id'];
                }
            }

            if (! $folderId) {
                return [
                    'success' => false,
                    'message' => 'Folder ID Google Drive belum diisi.',
                ];
            }

            $accessToken = $this->getAccessTokenFromServiceAccount($jsonPath);
            if (! $accessToken) {
                return [
                    'success' => false,
                    'message' => 'Gagal mendapatkan Access Token OAuth2 dari Google. Pastikan file JSON kredensial valid.',
                ];
            }

            $response = Http::withHeaders([
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

            $uploadResponse = Http::withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->attach(
                'metadata', json_encode($metadata), 'metadata.json', ['Content-Type' => 'application/json; charset=UTF-8']
            )->attach(
                'file', 'EDUVA LMS GOOGLE DRIVE CONNECTION TEST OK', 'test.txt', ['Content-Type' => 'text/plain']
            )->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&supportsAllDrives=true');

            if (! $uploadResponse->successful()) {
                $err = $uploadResponse->json()['error']['message'] ?? $uploadResponse->body();

                return [
                    'success' => false,
                    'message' => "Gagal mengunggah file uji coba ke folder: {$err}. Pastikan Service Account memiliki izin 'Editor' pada folder GDrive.",
                ];
            }

            $fileData = $uploadResponse->json();
            $testFileId = $fileData['id'] ?? null;

            if ($testFileId) {
                Http::withHeaders([
                    'Authorization' => 'Bearer '.$accessToken,
                ])->delete("https://www.googleapis.com/drive/v3/files/{$testFileId}?supportsAllDrives=true");
            }

            return [
                'success' => true,
                'message' => 'Koneksi Google Drive BERHASIL 100%! Service Account memiliki hak akses Editor dan berhasil menulis ke Folder ID.',
            ];
        } catch (\Throwable $e) {
            Log::error('Google Drive Test Connection Exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Gagal terhubung ke Google Drive: '.$e->getMessage(),
            ];
        }
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
            $payload = $this->base64UrlEncode(json_encode([
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/drive',
                'aud' => 'https://oauth2.googleapis.com/token',
                'exp' => $now + 3600,
                'iat' => $now,
            ]));

            $signatureInput = $header.'.'.$payload;
            $privateKey = $credentials['private_key'];

            $signed = @openssl_sign($signatureInput, $signature, $privateKey, 'SHA256');
            if (! $signed) {
                Log::error('Google OAuth Token Sign Failed: RSA private key invalid');

                return null;
            }

            $jwt = $signatureInput.'.'.$this->base64UrlEncode($signature);

            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
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
