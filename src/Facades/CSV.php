<?php

namespace EloquentCsv\Facades;

/**
 * @method static \Illuminate\Support\Collection read(string $filename)
 * @method static void write(string $filename, \Illuminate\Support\Collection $rows)
 */
class CSV extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'csv';
    }
}
