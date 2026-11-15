<?php

namespace App\Support;

use RuntimeException;
use SimpleXMLElement;
use ZipArchive;

/**
 * Tiny, dependency-free reader for .xlsx workbooks.
 *
 * The portal ships without PhpSpreadsheet, so this class unpacks the
 * OpenXML package and turns every worksheet into a simple matrix of strings:
 *
 *   [ 'TPS KP' => [ ['NOREG', 'NAMA', ...], ['09405913', 'FITRIADI', ...] ] ]
 *
 * The `zip` extension is used when available; otherwise a pure-PHP reader
 * (App\Support\ZipReader) takes over, so the import keeps working on a web
 * server whose php.ini does not load the extension.
 *
 * Column gaps are preserved as empty strings so a cell index always maps
 * back to the same column as the header row.
 */
class XlsxReader
{
    private ZipArchive|ZipReader|null $archive = null;

    /**
     * @param  bool  $preferNative  Force the pure-PHP reader when false.
     */
    public function __construct(private bool $preferNative = true)
    {
    }

    /**
     * @return array<string, array<int, array<int, string>>>
     */
    public function sheets(string $path): array
    {
        $this->archive = $this->openArchive($path);

        try {
            $shared = $this->sharedStrings();
            $result = [];

            foreach ($this->sheetMap() as $name => $target) {
                $xml = $this->read($target);

                if ($xml === null || $xml === '') {
                    continue;
                }

                $result[$name] = $this->parseSheet($xml, $shared);
            }

            return $result;
        } finally {
            if ($this->archive instanceof ZipArchive) {
                $this->archive->close();
            }

            $this->archive = null;
        }
    }

    private function openArchive(string $path): ZipArchive|ZipReader
    {
        if ($this->preferNative && class_exists(ZipArchive::class)) {
            $zip = new ZipArchive();

            if ($zip->open($path) === true) {
                return $zip;
            }
        }

        return new ZipReader($path);
    }

    private function read(string $name): ?string
    {
        if ($this->archive instanceof ZipArchive) {
            $content = $this->archive->getFromName($name);

            return $content === false ? null : $content;
        }

        return $this->archive?->get($name);
    }

    /**
     * @return array<int, string>
     */
    private function sharedStrings(): array
    {
        $xml = $this->read('xl/sharedStrings.xml');

        if ($xml === null || $xml === '') {
            return [];
        }

        $root = $this->loadXml($xml);

        if ($root === null) {
            return [];
        }

        $strings = [];

        foreach ($root->si as $item) {
            $strings[] = $this->itemText($item);
        }

        return $strings;
    }

    /**
     * Map every worksheet name to its path inside the archive.
     *
     * @return array<string, string>
     */
    private function sheetMap(): array
    {
        $workbook = $this->read('xl/workbook.xml');
        $rels = $this->read('xl/_rels/workbook.xml.rels');

        if ($workbook === null || $rels === null) {
            throw new RuntimeException('Struktur workbook tidak dikenali. Pastikan file adalah .xlsx yang valid.');
        }

        $workbookXml = $this->loadXml($workbook);
        $relsXml = $this->loadXml($rels);

        if ($workbookXml === null || $relsXml === null) {
            throw new RuntimeException('Struktur workbook tidak dapat dibaca.');
        }

        $targets = [];

        foreach ($relsXml->Relationship as $relationship) {
            $targets[(string) $relationship['Id']] = (string) $relationship['Target'];
        }

        $map = [];
        $relationshipNamespace = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';

        foreach ($workbookXml->sheets->sheet as $sheet) {
            $relationshipId = (string) $sheet->attributes($relationshipNamespace)['id'];
            $target = $targets[$relationshipId] ?? null;

            if ($target === null || $target === '') {
                continue;
            }

            $target = str_replace('\\', '/', $target);
            $target = ltrim($target, '/');

            if (! str_starts_with($target, 'xl/')) {
                $target = 'xl/' . $target;
            }

            $map[(string) $sheet['name']] = $target;
        }

        return $map;
    }

    /**
     * @param  array<int, string>  $shared
     * @return array<int, array<int, string>>
     */
    private function parseSheet(string $xml, array $shared): array
    {
        $root = $this->loadXml($xml);

        if ($root === null) {
            return [];
        }

        $rows = [];

        foreach ($root->sheetData->row as $row) {
            $cells = [];

            foreach ($row->c as $cell) {
                $reference = (string) $cell['r'];
                $column = $reference !== '' ? $this->columnIndex($reference) : count($cells);
                $type = (string) $cell['t'];

                $cells[$column] = match ($type) {
                    's' => $shared[(int) $cell->v] ?? '',
                    'inlineStr' => $this->itemText($cell->is),
                    'b' => ((string) $cell->v) === '1' ? 'TRUE' : 'FALSE',
                    default => (string) $cell->v,
                };
            }

            if ($cells === []) {
                continue;
            }

            ksort($cells);
            $max = max(array_keys($cells));
            $line = [];

            for ($column = 0; $column <= $max; $column++) {
                $line[] = $cells[$column] ?? '';
            }

            $rows[] = $line;
        }

        return $rows;
    }

    private function itemText(?SimpleXMLElement $item): string
    {
        if ($item === null) {
            return '';
        }

        // Plain value: <t>text</t>
        if (isset($item->t)) {
            return (string) $item->t;
        }

        // Rich text runs: <r><t>a</t></r><r><t>b</t></r>
        $text = '';

        if (isset($item->r)) {
            foreach ($item->r as $run) {
                $text .= (string) $run->t;
            }
        }

        return $text;
    }

    private function columnIndex(string $reference): int
    {
        preg_match('/^([A-Za-z]+)/', $reference, $matches);
        $letters = strtoupper($matches[1] ?? 'A');
        $index = 0;

        for ($i = 0, $length = strlen($letters); $i < $length; $i++) {
            $index = $index * 26 + (ord($letters[$i]) - 64);
        }

        return $index - 1;
    }

    private function loadXml(string $xml): ?SimpleXMLElement
    {
        $previous = libxml_use_internal_errors(true);
        $root = simplexml_load_string($xml);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        return $root === false ? null : $root;
    }
}
