<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class GoogleDriveSettingController extends Controller
{
    public function getConfigPath(): string
    {
        return app()->runningUnitTests()
            ? storage_path('app/google-drive/test_config.json')
            : storage_path('app/google-drive/config.json');
    }

    private function getPersistentConfig(): array
    {
        $path = $this->getConfigPath();
        if (File::exists($path)) {
            $content = File::get($path);
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return [];
    }

    public function index()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $jsonPath = storage_path('app/google-drive/service-account.json');
        $persistent = $this->getPersistentConfig();

        $enabled = isset($persistent['enabled'])
            ? filter_var($persistent['enabled'], FILTER_VALIDATE_BOOLEAN)
            : filter_var(env('GOOGLE_DRIVE_ENABLED', false), FILTER_VALIDATE_BOOLEAN);

        $folderId = ! empty($persistent['folder_id'])
            ? $persistent['folder_id']
            : (string) env('GOOGLE_DRIVE_FOLDER_ID', '');

        $authMode = ! empty($persistent['auth_mode'])
            ? $persistent['auth_mode']
            : (string) env('GOOGLE_DRIVE_AUTH_MODE', 'oauth');

        $oauthClientId = ! empty($persistent['oauth_client_id'])
            ? $persistent['oauth_client_id']
            : (string) env('GOOGLE_DRIVE_CLIENT_ID', '');

        $oauthClientSecret = ! empty($persistent['oauth_client_secret'])
            ? $persistent['oauth_client_secret']
            : (string) env('GOOGLE_DRIVE_CLIENT_SECRET', '');

        $oauthConnectedEmail = ! empty($persistent['oauth_connected_email'])
            ? $persistent['oauth_connected_email']
            : (string) env('GOOGLE_DRIVE_CONNECTED_EMAIL', '');

        $hasOAuthConnected = ! empty($persistent['oauth_refresh_token'] ?? env('GOOGLE_DRIVE_REFRESH_TOKEN', ''));

        $clientEmail = ! empty($persistent['client_email'])
            ? $persistent['client_email']
            : (string) env('GOOGLE_DRIVE_CLIENT_EMAIL', '');

        $impersonateEmail = ! empty($persistent['impersonate_email'])
            ? $persistent['impersonate_email']
            : (string) env('GOOGLE_DRIVE_IMPERSONATE_EMAIL', '');

        $status = [
            'enabled' => $enabled,
            'folder_id' => $folderId,
            'auth_mode' => $authMode,
            'oauth_client_id' => $oauthClientId,
            'oauth_client_secret' => $oauthClientSecret,
            'oauth_connected_email' => $oauthConnectedEmail,
            'has_oauth_connected' => $hasOAuthConnected,
            'client_email' => $clientEmail,
            'impersonate_email' => $impersonateEmail,
            'has_json' => File::exists($jsonPath),
        ];

        $redirectUri = route('admin.setting.gdrive.oauth.callback');

        return view('admin.setting.gdrive', compact('status', 'redirectUri'));
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'enabled' => 'nullable|boolean',
            'folder_id' => 'nullable|string',
            'auth_mode' => 'nullable|in:oauth,service_account',
            'oauth_client_id' => 'nullable|string',
            'oauth_client_secret' => 'nullable|string',
            'client_email' => 'nullable|email',
            'impersonate_email' => 'nullable|email',
            'credentials_json' => 'nullable|file|mimes:json,txt',
            'credentials_text' => 'nullable|string',
        ]);

        $dir = storage_path('app/google-drive');
        if (! File::exists($dir)) {
            File::makeDirectory($dir, 0755, true, true);
        }

        $existing = $this->getPersistentConfig();

        $enabledBool = $request->has('enabled') && (bool) $request->input('enabled');
        $enabledStr = $enabledBool ? 'true' : 'false';

        $folderId = ! empty($validated['folder_id'])
            ? trim($validated['folder_id'])
            : ($existing['folder_id'] ?? trim((string) env('GOOGLE_DRIVE_FOLDER_ID', '')));

        $authMode = ! empty($validated['auth_mode'])
            ? trim($validated['auth_mode'])
            : ($existing['auth_mode'] ?? trim((string) env('GOOGLE_DRIVE_AUTH_MODE', 'oauth')));

        $oauthClientId = ! empty($validated['oauth_client_id'])
            ? trim($validated['oauth_client_id'])
            : ($existing['oauth_client_id'] ?? trim((string) env('GOOGLE_DRIVE_CLIENT_ID', '')));

        $oauthClientSecret = ! empty($validated['oauth_client_secret'])
            ? trim($validated['oauth_client_secret'])
            : ($existing['oauth_client_secret'] ?? trim((string) env('GOOGLE_DRIVE_CLIENT_SECRET', '')));

        $clientEmail = ! empty($validated['client_email'])
            ? trim($validated['client_email'])
            : ($existing['client_email'] ?? trim((string) env('GOOGLE_DRIVE_CLIENT_EMAIL', '')));

        $impersonateEmail = ! empty($validated['impersonate_email'])
            ? trim($validated['impersonate_email'])
            : ($existing['impersonate_email'] ?? trim((string) env('GOOGLE_DRIVE_IMPERSONATE_EMAIL', '')));

        // Tangani file JSON jika diunggah
        if ($request->hasFile('credentials_json')) {
            $request->file('credentials_json')->move($dir, 'service-account.json');
        } elseif (! empty($validated['credentials_text'])) {
            File::put($dir.'/service-account.json', $validated['credentials_text']);
        }

        $configData = array_merge($existing, [
            'enabled' => $enabledBool,
            'folder_id' => $folderId,
            'auth_mode' => $authMode,
            'oauth_client_id' => $oauthClientId,
            'oauth_client_secret' => $oauthClientSecret,
            'client_email' => $clientEmail,
            'impersonate_email' => $impersonateEmail,
            'updated_at' => now()->toIso8601String(),
        ]);

        File::put($this->getConfigPath(), json_encode($configData, JSON_PRETTY_PRINT));

        $this->updateEnv([
            'GOOGLE_DRIVE_ENABLED' => $enabledStr,
            'GOOGLE_DRIVE_FOLDER_ID' => $folderId,
            'GOOGLE_DRIVE_AUTH_MODE' => $authMode,
            'GOOGLE_DRIVE_CLIENT_ID' => $oauthClientId,
            'GOOGLE_DRIVE_CLIENT_SECRET' => $oauthClientSecret,
            'GOOGLE_DRIVE_CLIENT_EMAIL' => $clientEmail,
            'GOOGLE_DRIVE_IMPERSONATE_EMAIL' => $impersonateEmail,
        ]);

        return back()->with('toast_success', 'Pengaturan Google Drive berhasil diperbarui dan tersimpan.');
    }

    public function oauthConnect(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $config = $this->getPersistentConfig();
        $clientId = $config['oauth_client_id'] ?? env('GOOGLE_DRIVE_CLIENT_ID');

        if (empty($clientId)) {
            return back()->with('toast_error', 'Isi Client ID dan Client Secret terlebih dahulu lalu klik Simpan Pengaturan sebelum menghubungkan akun.');
        }

        $state = bin2hex(random_bytes(16));
        session(['gdrive_oauth_state' => $state]);

        $redirectUri = route('admin.setting.gdrive.oauth.callback');
        $params = http_build_query([
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/userinfo.email',
            'access_type' => 'offline',
            'prompt' => 'consent',
            'state' => $state,
        ]);

        return redirect('https://accounts.google.com/o/oauth2/v2/auth?'.$params);
    }

    public function oauthCallback(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        if ($request->has('error')) {
            return redirect()->route('admin.setting.gdrive')
                ->with('toast_error', 'Autentikasi Google dibatalkan: '.$request->input('error'));
        }

        $code = $request->input('code');
        $state = $request->input('state');
        $expectedState = session('gdrive_oauth_state');

        if (empty($code) || empty($state) || $state !== $expectedState) {
            return redirect()->route('admin.setting.gdrive')
                ->with('toast_error', 'Validasi sesi OAuth gagal atau kedaluwarsa. Silakan ulangi tombol Hubungkan Akun Google.');
        }

        $config = $this->getPersistentConfig();
        $clientId = $config['oauth_client_id'] ?? env('GOOGLE_DRIVE_CLIENT_ID');
        $clientSecret = $config['oauth_client_secret'] ?? env('GOOGLE_DRIVE_CLIENT_SECRET');
        $redirectUri = route('admin.setting.gdrive.oauth.callback');

        $response = Http::withoutVerifying()->asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $redirectUri,
        ]);

        if (! $response->successful()) {
            $err = $response->json()['error_description'] ?? $response->body();

            return redirect()->route('admin.setting.gdrive')
                ->with('toast_error', 'Gagal menukarkan token Google: '.$err);
        }

        $data = $response->json();
        $refreshToken = $data['refresh_token'] ?? ($config['oauth_refresh_token'] ?? null);
        $accessToken = $data['access_token'] ?? null;

        if (empty($refreshToken)) {
            return redirect()->route('admin.setting.gdrive')
                ->with('toast_error', 'Google tidak mengembalikan Refresh Token. Pastikan izin akses disetujui saat konfirmasi login.');
        }

        $connectedEmail = null;
        if ($accessToken) {
            $userinfoResponse = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer '.$accessToken,
            ])->get('https://www.googleapis.com/oauth2/v2/userinfo');

            if ($userinfoResponse->successful()) {
                $connectedEmail = $userinfoResponse->json('email');
            }
        }

        $config['oauth_refresh_token'] = $refreshToken;
        $config['oauth_connected_email'] = $connectedEmail ?: 'Connected';
        $config['auth_mode'] = 'oauth';
        $config['enabled'] = true;
        $config['updated_at'] = now()->toIso8601String();

        File::put($this->getConfigPath(), json_encode($config, JSON_PRETTY_PRINT));

        $this->updateEnv([
            'GOOGLE_DRIVE_ENABLED' => 'true',
            'GOOGLE_DRIVE_AUTH_MODE' => 'oauth',
            'GOOGLE_DRIVE_REFRESH_TOKEN' => $refreshToken,
            'GOOGLE_DRIVE_CONNECTED_EMAIL' => $connectedEmail ?: '',
        ]);

        return redirect()->route('admin.setting.gdrive')
            ->with('toast_success', "Berhasil! Akun Google ({$connectedEmail}) berhasil terhubung sebagai penyimpanan Eduva.");
    }

    public function oauthDisconnect()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $config = $this->getPersistentConfig();
        unset($config['oauth_refresh_token'], $config['oauth_connected_email']);
        $config['updated_at'] = now()->toIso8601String();

        File::put($this->getConfigPath(), json_encode($config, JSON_PRETTY_PRINT));

        $this->updateEnv([
            'GOOGLE_DRIVE_REFRESH_TOKEN' => '',
            'GOOGLE_DRIVE_CONNECTED_EMAIL' => '',
        ]);

        return redirect()->route('admin.setting.gdrive')
            ->with('toast_success', 'Koneksi akun Google berhasil diputuskan.');
    }

    private function updateEnv(array $data): void
    {
        $envFile = base_path('.env');
        if (! File::exists($envFile)) {
            return;
        }

        $content = File::get($envFile);

        foreach ($data as $key => $value) {
            $keyPattern = "/^{$key}=.*/m";
            $valueFormatted = str_contains($value, ' ') ? '"'.$value.'"' : $value;

            if (preg_match($keyPattern, $content)) {
                $content = preg_replace($keyPattern, "{$key}={$valueFormatted}", $content);
            } else {
                $content .= "\n{$key}={$valueFormatted}";
            }
        }

        File::put($envFile, $content);
    }

    public function testConnection(GoogleDriveService $driveService)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $result = $driveService->testConnection();

        if ($result['success']) {
            return back()->with('toast_success', $result['message']);
        }

        return back()->with('toast_error', $result['message']);
    }
}
