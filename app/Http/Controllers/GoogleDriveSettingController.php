<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GoogleDriveSettingController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $jsonPath = storage_path('app/google-drive/service-account.json');

        $status = [
            'enabled' => filter_var(env('GOOGLE_DRIVE_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
            'folder_id' => env('GOOGLE_DRIVE_FOLDER_ID', ''),
            'client_email' => env('GOOGLE_DRIVE_CLIENT_EMAIL', ''),
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

        $enabled = $request->has('enabled') ? 'true' : 'false';
        $folderId = trim($validated['folder_id'] ?? '');
        $clientEmail = trim($validated['client_email'] ?? '');

        // Tangani file JSON jika diunggah
        if ($request->hasFile('credentials_json')) {
            $dir = storage_path('app/google-drive');
            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }
            $request->file('credentials_json')->move($dir, 'service-account.json');
        } elseif (! empty($validated['credentials_text'])) {
            $dir = storage_path('app/google-drive');
            if (! File::exists($dir)) {
                File::makeDirectory($dir, 0755, true, true);
            }
            File::put($dir.'/service-account.json', $validated['credentials_text']);
        }

        $this->updateEnv([
            'GOOGLE_DRIVE_ENABLED' => $enabled,
            'GOOGLE_DRIVE_FOLDER_ID' => $folderId,
            'GOOGLE_DRIVE_CLIENT_EMAIL' => $clientEmail,
        ]);

        return back()->with('toast_success', 'Pengaturan Google Drive berhasil diperbarui.');
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
