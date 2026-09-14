<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GoogleDriveSettingController extends Controller
{
    private function getConfigPath(): string
    {
        return storage_path('app/google-drive/config.json');
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

        $clientEmail = ! empty($persistent['client_email'])
            ? $persistent['client_email']
            : (string) env('GOOGLE_DRIVE_CLIENT_EMAIL', '');

        $status = [
            'enabled' => $enabled,
            'folder_id' => $folderId,
            'client_email' => $clientEmail,
            'has_json' => File::exists($jsonPath),
        ];

        return view('admin.setting.gdrive', compact('status'));
    }

    public function update(Request $request)
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $request->validate([
            'folder_id' => 'nullable|string',
            'client_email' => 'nullable|email',
            'credentials_json' => 'nullable|file|mimes:json,txt',
            'credentials_text' => 'nullable|string',
            'enabled' => 'nullable|boolean',
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

        $clientEmail = ! empty($validated['client_email'])
            ? trim($validated['client_email'])
            : ($existing['client_email'] ?? trim((string) env('GOOGLE_DRIVE_CLIENT_EMAIL', '')));

        // Tangani file JSON jika diunggah
        if ($request->hasFile('credentials_json')) {
            $request->file('credentials_json')->move($dir, 'service-account.json');
        } elseif (! empty($validated['credentials_text'])) {
            File::put($dir.'/service-account.json', $validated['credentials_text']);
        }

        $configData = [
            'enabled' => $enabledBool,
            'folder_id' => $folderId,
            'client_email' => $clientEmail,
            'updated_at' => now()->toIso8601String(),
        ];

        File::put($this->getConfigPath(), json_encode($configData, JSON_PRETTY_PRINT));

        $this->updateEnv([
            'GOOGLE_DRIVE_ENABLED' => $enabledStr,
            'GOOGLE_DRIVE_FOLDER_ID' => $folderId,
            'GOOGLE_DRIVE_CLIENT_EMAIL' => $clientEmail,
        ]);

        return back()->with('toast_success', 'Pengaturan Google Drive berhasil diperbarui dan tersimpan secara permanen.');
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
}
