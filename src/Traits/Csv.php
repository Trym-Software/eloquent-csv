<?php

namespace EloquentCsv\Traits;

use EloquentCsv\Facades\CSV as Facade;
use Sushi\Sushi;

trait Csv
{
    use Sushi;

    public $csvFile;

    public function csvFile()
    {
        return storage_path($this->csvFile);
    }

    public function getRows()
    {
        return Facade::read($this->csvFile())->toArray() ?? [];
    }

    protected static function booted(): void
    {
        static::addGlobalScope('remove-id', function (Builder $builder) {
            $builder->setHidden('id');
        });
    }
}