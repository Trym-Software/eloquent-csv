<?php

namespace EloquentCsv\Model;

use EloquentCsv\Facades\CSV;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** @phpstan-consistent-constructor */
abstract class CsvModel extends Model implements CsvModelInterface
{
    protected $connection = 'eloquent_csv';

    protected $hidden = ['id'];

    public $timestamps = false;

    public static function fromCsv(string $file): CsvModelInterface
    {
        $rows = CSV::read($file);
        $headers = $rows->first()->keys();

        $model = new static;
        Schema::connection($model->getConnectionName())->dropIfExists($model->getTable())
            ->create($model->getTable(), function (Blueprint $table) use ($headers) {
                $table->id();
                $headers->each(fn ($header) => $table->string($header)->nullable());
            });

        foreach ($rows as $row) {
            DB::connection($model->getConnectionName())
                ->table($model->getTable())
                ->insert($row->toArray());
        }

        return $model;
    }

    // public static function toCsv(string $filename, EloquentCollection $collection): EloquentCollection
    // {
    //     $file = fopen($filename, 'w');

    //     $model = $collection->first();
    //     $keyName = $model->getKeyName();
    //     fputcsv($file, collect($model->toArray())->except($keyName)->keys()->all());

    //     foreach ($collection as $model) {
    //         fputcsv($file, collect($model->toArray())->except($keyName)->all());
    //     }

    //     fclose($file);

    //     return $collection;
    // }

    public static function getColumns(): Collection
    {
        $model = new static;

        $columns = collect(Schema::connection($model->getConnectionName())->getColumnListing($model->getTable()));
        $columns->shift();

        return $columns;
    }

    public static function addColumn(string $columnName): CsvModel
    {
        $model = new static;

        Schema::connection($model->getConnectionName())
            ->table($model->getTable(), function (Blueprint $table) use ($columnName) {
                $table->string($columnName)->nullable();
            });

        return $model;
    }

    public static function dropColumn(array|string $columnNames): CsvModel
    {
        $model = new static;

        if (! is_array($columnNames)) {
            $columnNames = [$columnNames];
        }

        Schema::connection($model->getConnectionName())
            ->table($model->getTable(), function (Blueprint $table) use ($columnNames) {
                foreach ($columnNames as $columnName) {
                    $table->dropColumn($columnName);
                }
            });

        return $model;
    }
}
