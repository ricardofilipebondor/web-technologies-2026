<?php

class DataExporter
{
    public static function toCsv(array $rows, array $headers, string $filename): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, $headers);

        foreach ($rows as $row) {
            fputcsv($out, $row);
        }

        fclose($out);
        exit;
    }

    public static function toJson(array $data, string $filename): void
    {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function toXml(array $data, string $rootName, string $itemName, string $filename): void
    {
        header('Content-Type: application/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $rootName . '/>');

        foreach ($data as $item) {
            $node = $xml->addChild($itemName);
            foreach ($item as $key => $value) {
                $node->addChild($key, htmlspecialchars((string) $value));
            }
        }

        echo $xml->asXML();
        exit;
    }
}
