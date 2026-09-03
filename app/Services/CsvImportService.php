<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvImportService
{
    /**
     * Download CSV template with UTF-8 BOM so Excel opens it with proper encoding and columns.
     */
    public function downloadTemplate(string $filename, array $headers, array $sampleRows = []): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $sampleRows) {
            $handle = fopen('php://output', 'w');

            // Write UTF-8 BOM for Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");

            // Write header
            fputcsv($handle, $headers, ',');

            // Write sample rows
            foreach ($sampleRows as $row) {
                fputcsv($handle, $row, ',');
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }

    /**
     * Parse uploaded CSV file into an array of associative rows with normalized header keys.
     */
    public function parseCsv(string $filePath): array
    {
        if (! file_exists($filePath) || ! is_readable($filePath)) {
            return [];
        }

        $handle = fopen($filePath, 'r');
        if (! $handle) {
            return [];
        }

        // Read first chunk to detect delimiter and check BOM
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return [];
        }

        // Strip UTF-8 BOM if present
        $bom = "\xEF\xBB\xBF";
        if (str_starts_with($firstLine, $bom)) {
            $firstLine = substr($firstLine, 3);
        }

        // Detect delimiter (comma, semicolon, or tab)
        $delimiter = $this->detectDelimiter($firstLine);

        // Parse header line
        $headers = str_getcsv($firstLine, $delimiter);
        $headers = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $h ?? '')));
        }, $headers);

        $rows = [];
        $rowNumber = 1; // 1 is header

        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $rowNumber++;

            // Skip empty rows
            if (empty($data) || (count($data) === 1 && trim($data[0] ?? '') === '')) {
                continue;
            }

            $row = [];
            foreach ($headers as $index => $key) {
                if ($key !== '') {
                    $row[$key] = isset($data[$index]) ? trim($data[$index]) : '';
                }
            }

            $row['_row_number'] = $rowNumber;
            $rows[] = $row;
        }

        fclose($handle);

        return $rows;
    }

    /**
     * Detect CSV delimiter based on count of delimiter candidates in the line.
     */
    private function detectDelimiter(string $line): string
    {
        $delimiters = [',', ';', "\t"];
        $counts = [];

        foreach ($delimiters as $delim) {
            $counts[$delim] = substr_count($line, $delim);
        }

        arsort($counts);
        $best = array_key_first($counts);

        return ($counts[$best] > 0) ? $best : ',';
    }
}
