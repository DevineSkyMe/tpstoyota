<?php

namespace App\Support;

use RuntimeException;

/**
 * Minimal, dependency-free ZIP archive reader.
 *
 * PHP's `zip` extension is not always enabled (for example a web server
 * whose php.ini was changed after the process started). This reader parses
 * the ZIP central directory by hand and inflates entries with `gzinflate`,
 * which ships with the almost always-present `zlib` extension.
 *
 * It only supports what an .xlsx package needs: stored (0) and deflated (8)
 * entries without encryption or ZIP64.
 */
class ZipReader
{
    private string $data;

    /**
     * @var array<string, array{offset: int, method: int, size: int}>
     */
    private array $entries = [];

    public function __construct(string $path)
    {
        $data = @file_get_contents($path);

        if ($data === false) {
            throw new RuntimeException('File tidak dapat dibaca.');
        }

        $this->data = $data;
        $this->readCentralDirectory();
    }

    /**
     * @return array<int, string>
     */
    public function names(): array
    {
        return array_keys($this->entries);
    }

    public function get(string $name): ?string
    {
        if (! isset($this->entries[$name])) {
            return null;
        }

        $entry = $this->entries[$name];
        $offset = $entry['offset'];

        if (substr($this->data, $offset, 4) !== "PK\x03\x04") {
            throw new RuntimeException('Header lokal tidak valid pada "' . $name . '".');
        }

        $nameLength = $this->u16($offset + 26);
        $extraLength = $this->u16($offset + 28);
        $dataStart = $offset + 30 + $nameLength + $extraLength;
        $compressed = substr($this->data, $dataStart, $entry['size']);

        $content = match ($entry['method']) {
            0 => $compressed,
            8 => gzinflate($compressed),
            default => throw new RuntimeException(
                'Metode kompresi ZIP tidak didukung (' . $entry['method'] . ') pada "' . $name . '".'
            ),
        };

        if ($content === false) {
            throw new RuntimeException('Dekompresi gagal pada "' . $name . '".');
        }

        return $content;
    }

    private function readCentralDirectory(): void
    {
        $end = $this->findEndOfCentralDirectory();
        $count = $this->u16($end + 10);
        $position = $this->u32($end + 16);

        for ($i = 0; $i < $count; $i++) {
            if (substr($this->data, $position, 4) !== "PK\x01\x02") {
                break;
            }

            $method = $this->u16($position + 10);
            $compressedSize = $this->u32($position + 20);
            $nameLength = $this->u16($position + 28);
            $extraLength = $this->u16($position + 30);
            $commentLength = $this->u16($position + 32);
            $localOffset = $this->u32($position + 42);
            $name = substr($this->data, $position + 46, $nameLength);

            $this->entries[$name] = [
                'offset' => $localOffset,
                'method' => $method,
                'size' => $compressedSize,
            ];

            $position += 46 + $nameLength + $extraLength + $commentLength;
        }
    }

    private function findEndOfCentralDirectory(): int
    {
        $length = strlen($this->data);

        if ($length < 22) {
            throw new RuntimeException('File terlalu kecil untuk menjadi arsip ZIP.');
        }

        $signature = "PK\x05\x06";
        $minimum = max(0, $length - 22 - 65535);

        for ($i = $length - 22; $i >= $minimum; $i--) {
            if (substr($this->data, $i, 4) === $signature) {
                return $i;
            }
        }

        throw new RuntimeException('Struktur ZIP tidak ditemukan. Pastikan file berformat .xlsx.');
    }

    private function u16(int $offset): int
    {
        $chunk = substr($this->data, $offset, 2);

        if (strlen($chunk) < 2) {
            throw new RuntimeException('Struktur ZIP rusak (dibaca di luar batas).');
        }

        return unpack('v', $chunk)[1];
    }

    private function u32(int $offset): int
    {
        $chunk = substr($this->data, $offset, 4);

        if (strlen($chunk) < 4) {
            throw new RuntimeException('Struktur ZIP rusak (dibaca di luar batas).');
        }

        return unpack('V', $chunk)[1];
    }
}
