<?php

namespace EloquentCsv\Facades;

class CSV extends \Illuminate\Support\Facades\Facade
{
    protected static function getFacadeAccessor()
    {
        return 'csv';
    }
}
