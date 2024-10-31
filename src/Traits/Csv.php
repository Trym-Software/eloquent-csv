<?php

namespace EloquentCsv\Traits;

use EloquentCsv\Facades\CSV as Facade;
use Sushi\Sushi;

trait Csv
{
    use Sushi;

    public function csvFile()
    {
        return storage_path($this->csvFile);
    }

    public function getRows()
    {
        return Facade::read($this->csvFile())->toArray() ?? [];
    }

    protected function initializeCsv()
    {
        $this->makeHidden('id');
    }
}
