<?php

class DataImporter
{
    public static function fromCsv(string $filepath): array
    {
        $rows = [];
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            return [];
        }

        $headers = fgetcsv($handle);
        if (!$headers) {
            fclose($handle);
            return [];
        }

        $headers = self::normalizeHeaders($headers);

        while (($data = fgetcsv($handle)) !== false) {
            $data = self::normalizeRow($data, $headers);
            if ($data !== null) {
                $rows[] = array_combine($headers, $data);
            }
        }

        fclose($handle);
        return $rows;
    }

    private static function normalizeHeaders(array $headers): array
    {
        if ($headers !== [] && isset($headers[0])) {
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $headers[0]) ?? $headers[0];
        }

        return array_map(static fn($header) => trim((string) $header), $headers);
    }

    /** Repara randuri cu virgule neescapate in campuri text (ex. adresa). */
    private static function normalizeRow(array $data, array $headers): ?array
    {
        $expected = count($headers);
        $actual = count($data);

        if ($actual === $expected) {
            return $data;
        }

        if ($actual < $expected) {
            return null;
        }

        $adresaIdx = array_search('adresa', $headers, true);
        if ($adresaIdx === false) {
            return null;
        }

        $tailCount = $expected - $adresaIdx - 1;
        if ($tailCount < 0) {
            return null;
        }

        $adresaParts = array_slice($data, $adresaIdx, $actual - $adresaIdx - $tailCount);
        $adresa = implode(', ', $adresaParts);

        return array_merge(
            array_slice($data, 0, $adresaIdx),
            [$adresa],
            array_slice($data, -$tailCount)
        );
    }

    public static function fromJson(string $filepath): array
    {
        $content = file_get_contents($filepath);
        $data = json_decode($content, true);
        return is_array($data) ? $data : [];
    }

    public static function fromXml(string $filepath, string $itemName): array
    {
        $xml = simplexml_load_file($filepath);
        if (!$xml) {
            return [];
        }

        $rows = [];
        foreach ($xml->{$itemName} as $item) {
            $row = [];
            foreach ($item as $key => $value) {
                $row[$key] = (string) $value;
            }
            $rows[] = $row;
        }

        return $rows;
    }
}
