<?php

namespace EloquentCsv;

use Illuminate\Support\Collection;

class CsvFile
{
    public function read(string $filename): Collection
    {
        $csv = file_get_contents($filename);
        $lines = explode(PHP_EOL, $csv);
        $header = collect(str_getcsv(array_shift($lines)));
        $rows = collect($lines)->filter();

        return $rows->map(fn ($row) => $header->combine(str_getcsv($row)));
    }

    public function write(string $filename, Collection $rows): void
    {
        $file = fopen($filename, 'w');

        fputcsv($file, collect($rows->first())->keys()->all());
        $rows->each(fn ($row) => fputcsv($file, collect($row)->all()));

        fclose($file);
    }
}
