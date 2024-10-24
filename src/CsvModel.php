<?php

namespace EloquentCsv;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class CsvModel extends Model
{
    protected $connection = 'eloquent_csv';

    public $timestamps = false;

    public static function fromCsv(string $filePath): CsvModel
    {
        $model = new static;

        $csv = file_get_contents($filePath);
        $lines = explode(PHP_EOL, $csv);
        $header = collect(str_getcsv(array_shift($lines)));

        Schema::connection($model->getConnectionName())->dropIfExists($model->getTable());
        Schema::connection($model->getConnectionName())
            ->create($model->getTable(), function (Blueprint $table) use ($header) {
                $table->id();
                foreach ($header as $column) {
                    $table->string($column)->nullable();
                }
            });

        $rows = collect($lines)->filter();
        $data = $rows->map(fn ($row) => $header->combine(str_getcsv($row)));

        foreach ($data as $row) {
            DB::connection($model->getConnectionName())
                ->table($model->getTable())
                ->insert($row->toArray());
        }

        return $model;
    }

    public static function toCsv(string $filename, EloquentCollection $collection): EloquentCollection
    {
        $file = fopen($filename, 'w');

        $model = $collection->first();
        $keyName = $model->getKeyName();
        fputcsv($file, collect($model->toArray())->except($keyName)->keys()->all());

        foreach ($collection as $model) {
            fputcsv($file, collect($model->toArray())->except($keyName)->all());
        }

        fclose($file);

        return $collection;
    }

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
