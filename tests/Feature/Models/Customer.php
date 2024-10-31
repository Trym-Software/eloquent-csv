<?php

namespace EloquentCsv\Tests\Feature\Models;

use EloquentCsv\Traits\Csv;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class Customer extends Model
{
    use Csv;

    public $csvFile = 'customers-10.csv';

    protected function afterMigrate(Blueprint $table)
    {
        $table->string('Test Column')->nullable();
    }
}