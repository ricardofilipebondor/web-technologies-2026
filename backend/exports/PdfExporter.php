<?php

class PdfExporter
{
    public static function generateReport(string $title, array $lines, string $filename = 'raport.pdf'): void
    {
        $content = self::buildContent($title, $lines);
        $pdf = self::createSimplePdf($content);
        $filename = DataExporter::safeFilename($filename);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Pragma: no-cache');
        echo $pdf;
        exit;
    }

    private static function buildContent(string $title, array $lines): string
    {
        $text = $title . "\n\n";
        foreach ($lines as $line) {
            $text .= $line . "\n";
        }
        return $text;
    }

    private static function createSimplePdf(string $text): string
    {
        $text = self::escapePdfText($text);
        $lines = explode("\n", $text);
        $y = 800;
        $stream = "BT\n/F1 12 Tf\n";

        foreach ($lines as $line) {
            if ($y < 50) {
                break;
            }
            if ($line === '') {
                $y -= 14;
                continue;
            }
            $stream .= sprintf("1 0 0 1 50 %d Tm\n(%s) Tj\n", $y, $line);
            $y -= 16;
        }

        $stream .= "ET";

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";
        $objects[] = "4 0 obj\n<< /Length " . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream\nendobj\n";
        $objects[] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj;
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . (count($offsets)) . "\n";
        $pdf .= "0000000000 65535 f \n";

        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . count($offsets) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPos . "\n%%EOF";

        return $pdf;
    }

    private static function escapePdfText(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }
}
