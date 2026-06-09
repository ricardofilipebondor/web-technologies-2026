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

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) === count($headers)) {
                $rows[] = array_combine($headers, $data);
            }
        }

        fclose($handle);
        return $rows;
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
