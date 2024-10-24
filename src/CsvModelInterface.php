<?php

namespace EloquentCsv;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;

interface CsvModelInterface
{
    public function __construct(array $attributes = []);

    public static function fromCsv(string $filePath): CsvModelInterface;

    public static function toCsv(string $filename, EloquentCollection $collection): EloquentCollection;
}
