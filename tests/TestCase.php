<?php

namespace EloquentCsv\Tests;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [
            'EloquentCsv\EloquentCsvServiceProvider',
        ];
    }

    protected function defineEnvironment($app)
    {
        $app->useStoragePath(realpath(__DIR__.'/storage'));
    }
}
