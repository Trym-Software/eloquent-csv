<?php

namespace EloquentCsv;

use Illuminate\Support\Collection;

class CsvFile
{
    public function read(
        string $filename,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '',
    ): Collection {
        $rows = collect();
        if (($file = fopen($filename, 'r')) !== false) {
            while (($data = fgetcsv($file, null, $separator, $enclosure, $escape)) !== false) {
                $rows[] = $data;
            }
            fclose($file);
        }

        $header = collect($rows->shift());

        return $rows->filter()->map(fn ($row) => $header->combine($row)->all());
    }

    public function write(
        string $filename,
        Collection $rows,
        string $separator = ',',
        string $enclosure = '"',
        string $escape = '',
        string $eol = "\n"
    ): void {
        $file = fopen($filename, 'w');

        fputcsv($file, collect($rows->first())->keys()->all());
        $rows->each(fn ($row) => fputcsv($file, collect($row)->all(), $separator, $enclosure, $escape, $eol));

        fclose($file);
    }
}
