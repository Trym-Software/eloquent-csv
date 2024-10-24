<?php

namespace EloquentCsv;

use EloquentCsv\CsvModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class EloquentCsvServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Config::set('database.connections.eloquent_csv', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        Collection::macro('toCsv', fn($filename) => CsvModel::toCsv($filename, $this));
    }
}
