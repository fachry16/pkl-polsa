<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class LmsFileMime implements ValidationRule
{
    public const ALLOWED = [
        'pdf' => ['application/pdf'],
        'doc' => ['application/msword'],
        'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        'xls' => ['application/vnd.ms-excel'],
        'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        'ppt' => ['application/vnd.ms-powerpoint'],
        'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation'],
        'txt' => ['text/plain'],
        'rtf' => ['application/rtf'],
        'zip' => ['application/zip', 'application/x-zip-compressed'],
        'rar' => ['application/vnd.rar', 'application/x-rar-compressed'],
        'jpg' => ['image/jpeg'],
        'jpeg' => ['image/jpeg'],
        'png' => ['image/png'],
        'gif' => ['image/gif'],
        'webp' => ['image/webp'],
        'mp4' => ['video/mp4'],
        'mp3' => ['audio/mpeg'],
        'csv' => ['text/csv', 'text/plain'],
    ];

    /**
     * Tipe MIME berbahaya yang ditolak apa pun ekstensinya
     * (mencegah rename file berbahaya menjadi ekstensi diizinkan).
     */
    private const DANGEROUS = [
        'text/html',
        'text/x-php',
        'application/x-httpd-php',
        'application/x-httpd-php-source',
        'application/x-php',
        'application/x-msdownload',
        'application/x-msdos-program',
        'application/x-dosexec',
        'application/x-executable',
        'application/vnd.microsoft.portable-executable',
        'application/x-sh',
        'application/x-csh',
        'application/x-python-code',
        'application/x-javascript',
        'image/svg+xml',
    ];

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $value instanceof UploadedFile) {
            return;
        }

        $ext = strtolower($value->getClientOriginalExtension());

        if (! array_key_exists($ext, self::ALLOWED)) {
            $fail('Tipe file tidak diizinkan. Gunakan: '.implode(', ', array_keys(self::ALLOWED)).'.');

            return;
        }

        $serverMime = $this->detectMime($value);
        $clientMime = strtolower((string) $value->getMimeType());

        if (in_array($serverMime, self::DANGEROUS, true)
            || in_array($clientMime, self::DANGEROUS, true)) {
            $fail('Tipe file tidak diizinkan.');

            return;
        }

        $allowed = collect(self::ALLOWED)->flatten()->all();

        if (in_array($serverMime, $allowed, true) || in_array($clientMime, $allowed, true)) {
            return;
        }

        if (in_array($serverMime, ['', 'application/octet-stream', 'application/zip'], true)) {
            return;
        }

        $fail('Tipe file tidak diizinkan. Gunakan: '.implode(', ', array_keys(self::ALLOWED)).'.');
    }

    private function detectMime(UploadedFile $file): string
    {
        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);

            if ($finfo !== false) {
                $mime = finfo_file($finfo, $file->getRealPath());
                finfo_close($finfo);

                if (is_string($mime) && $mime !== '') {
                    return strtolower($mime);
                }
            }
        }

        if (function_exists('mime_content_type')) {
            $mime = mime_content_type($file->getRealPath());

            if (is_string($mime) && $mime !== '') {
                return strtolower($mime);
            }
        }

        return strtolower((string) $file->getMimeType());
    }
}
