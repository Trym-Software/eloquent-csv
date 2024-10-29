<?php

namespace EloquentCsv;

interface CsvModelInterface
{
    public function __construct(array $attributes = []);

    public static function fromCsv(string $filename): CsvModelInterface;
}
