<?php

namespace EloquentCsv;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

class EloquentCsvServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind('csv', fn () => new CsvFile);
    }

    public function boot(): void
    {
        Collection::macro('toCsv', fn ($filename) => app()->make('csv')->write($filename, $this));
    }
}
