<?php

if (! function_exists('linkify')) {
    /**
     * Ubah URL http(s) di dalam teks menjadi link yang dapat diklik.
     * Aman: setiap bagian teks di-escape; hanya protokol http/https yang di-link.
     */
    function linkify(?string $text): string
    {
        if ($text === null || $text === '') {
            return '';
        }

        $parts = preg_split(
            '#(https?://[^\s<>"\']+)#i',
            $text,
            -1,
            PREG_SPLIT_DELIM_CAPTURE
        );

        if ($parts === false) {
            return e($text);
        }

        $result = '';

        foreach ($parts as $part) {
            if (preg_match('#^https?://#i', $part)) {
                $clean = rtrim($part, '.,;:!?');

                if (substr($clean, -1) === ')'
                    && substr_count($clean, ')') > substr_count($clean, '(')) {
                    $clean = substr($clean, 0, -1);
                }

                $result .= '<a href="'.e($clean).'" target="_blank" rel="noopener noreferrer">'.e($clean).'</a>';
            } else {
                $result .= e($part);
            }
        }

        return $result;
    }
}
