<?php

namespace App\Support;

use ZipArchive;

/**
 * Penulis file .xlsx sederhana tanpa dependensi eksternal (butuh ext-zip).
 */
class XlsxWriter
{
    /** @var array<int, array{name:string, rows:array}> */
    protected array $sheets = [];

    /** Tambah sheet. $rows = array baris; baris pertama dianggap header (bold). */
    public function addSheet(string $name, array $rows): static
    {
        $name = preg_replace('/[\\\\\/\?\*\[\]:]/', '', $name);
        $this->sheets[] = ['name' => mb_substr($name, 0, 31) ?: 'Sheet' . (count($this->sheets) + 1), 'rows' => $rows];
        return $this;
    }

    public function save(string $path): void
    {
        $zip = new ZipArchive();
        $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $ct = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/>';
        $wb = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets>';
        $rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">';

        foreach ($this->sheets as $i => $sheet) {
            $n = $i + 1;
            $ct .= "<Override PartName=\"/xl/worksheets/sheet{$n}.xml\" ContentType=\"application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml\"/>";
            $wb .= '<sheet name="' . $this->esc($sheet['name']) . "\" sheetId=\"{$n}\" r:id=\"rId{$n}\"/>";
            $rels .= "<Relationship Id=\"rId{$n}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet\" Target=\"worksheets/sheet{$n}.xml\"/>";
            $zip->addFromString("xl/worksheets/sheet{$n}.xml", $this->sheetXml($sheet['rows']));
        }
        $s = count($this->sheets) + 1;
        $rels .= "<Relationship Id=\"rId{$s}\" Type=\"http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles\" Target=\"styles.xml\"/></Relationships>";

        $zip->addFromString('[Content_Types].xml', $ct . '</Types>');
        $zip->addFromString('_rels/.rels', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', $wb . '</sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', $rels);
        $zip->addFromString('xl/styles.xml', '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts><fills count="3"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill><fill><patternFill patternType="solid"><fgColor rgb="FFDDE3F5"/></patternFill></fill></fills><borders count="1"><border><left/><right/><top/><bottom/><diagonal/></border></borders><cellStyleXfs count="1"><xf numFmtId="0" fontId="0" fillId="0" borderId="0"/></cellStyleXfs><cellXfs count="3"><xf numFmtId="0" fontId="0" fillId="0" borderId="0" xfId="0"/><xf numFmtId="0" fontId="1" fillId="2" borderId="0" xfId="0" applyFont="1" applyFill="1"/><xf numFmtId="3" fontId="0" fillId="0" borderId="0" xfId="0" applyNumberFormat="1"/></cellXfs></styleSheet>');
        $zip->close();
    }

    protected function sheetXml(array $rows): string
    {
        $maxCols = 0;
        foreach ($rows as $r) {
            $maxCols = max($maxCols, count($r));
        }
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetViews><sheetView workbookViewId="0"><pane ySplit="1" topLeftCell="A2" activePane="bottomLeft" state="frozen"/></sheetView></sheetViews><cols>';
        for ($c = 1; $c <= $maxCols; $c++) {
            $xml .= "<col min=\"{$c}\" max=\"{$c}\" width=\"22\" customWidth=\"1\"/>";
        }
        $xml .= '</cols><sheetData>';
        foreach (array_values($rows) as $ri => $row) {
            $rn = $ri + 1;
            $xml .= "<row r=\"{$rn}\">";
            foreach (array_values($row) as $ci => $val) {
                $ref = $this->col($ci) . $rn;
                if ($val === null || $val === '') {
                    continue;
                }
                if ($ri === 0) {
                    $xml .= "<c r=\"{$ref}\" s=\"1\" t=\"inlineStr\"><is><t>" . $this->esc((string) $val) . '</t></is></c>';
                } elseif (is_int($val) || is_float($val)) {
                    $xml .= "<c r=\"{$ref}\" s=\"2\"><v>{$val}</v></c>";
                } else {
                    $xml .= "<c r=\"{$ref}\" t=\"inlineStr\"><is><t xml:space=\"preserve\">" . $this->esc((string) $val) . '</t></is></c>';
                }
            }
            $xml .= '</row>';
        }
        return $xml . '</sheetData></worksheet>';
    }

    protected function col(int $i): string
    {
        $s = '';
        for ($i++; $i > 0; $i = intdiv($i - 1, 26)) {
            $s = chr(65 + ($i - 1) % 26) . $s;
        }
        return $s;
    }

    protected function esc(string $v): string
    {
        $v = preg_replace('/[^\x09\x0A\x0D\x20-\x{D7FF}\x{E000}-\x{FFFD}]/u', '', $v) ?? '';
        return htmlspecialchars($v, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }
}
