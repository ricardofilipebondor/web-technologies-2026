<?php

class DataExporter
{
    public static function toCsv(array $rows, array $headers, string $filename): void
    {
        self::sendDownload('text/csv; charset=utf-8', self::safeFilename($filename));

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
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            Response::problem('Nu s-a putut genera exportul JSON.', 500);
        }

        self::sendDownload('application/json; charset=utf-8', self::safeFilename($filename));
        echo $json;
        exit;
    }

    public static function toXml(array $data, string $rootName, string $itemName, string $filename): void
    {
        self::sendDownload('application/xml; charset=utf-8', self::safeFilename($filename));

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><' . $rootName . '/>');

        foreach ($data as $item) {
            $node = $xml->addChild($itemName);
            foreach ($item as $key => $value) {
                $node->addChild((string) $key, htmlspecialchars((string) $value));
            }
        }

        echo $xml->asXML();
        exit;
    }

    private static function sendDownload(string $contentType, string $filename): void
    {
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
    }

    public static function safeFilename(string $filename): string
    {
        $filename = trim($filename);
        if ($filename === '') {
            return 'export.dat';
        }

        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename);
        if ($ascii !== false && $ascii !== '') {
            $filename = $ascii;
        }

        $filename = preg_replace('/[^\w\-\.]+/', '_', $filename) ?? 'export.dat';
        $filename = trim($filename, '._');

        return $filename !== '' ? $filename : 'export.dat';
    }
}
