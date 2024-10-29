<?php

namespace EloquentCsv;

use EloquentCsv\Facades\CSV;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class EloquentCsvServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('csv', fn () => new CsvBuilder);
    }

    public function boot(): void
    {
        Config::set('database.connections.eloquent_csv', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        Collection::macro('toCsv', fn ($filename) => CSV::write($filename, $this));
    }
}
