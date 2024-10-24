<?php

namespace EloquentCsv;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

abstract class CsvModel extends Model
{
    protected $connection = 'eloquent_csv';
    public $timestamps = false;

    public static function fromCsv(string $filePath)
    {
        $model = new static;

        $csv = file_get_contents($filePath);
        $lines = explode(PHP_EOL, $csv);
        $header = collect(str_getcsv(array_shift($lines)));

        self::createTable($model, $header);

        $rows = collect($lines)->filter();
        $data = $rows->map(fn($row) => $header->combine(str_getcsv($row)));

        self::insert($model, $data);

        return $model;
    }

    public static function toCsv($filename, Collection $collection)
    {
        $file = fopen($filename, 'w');

        $model = $collection->first();
        $keyName = $model->getKeyName();
        fputcsv($file, collect($model->toArray())->except($keyName)->keys()->all());

        foreach ($collection as $model) {
            fputcsv($file,  collect($model->toArray())->except($keyName)->all());
        };

        fclose($file);
    }

    public static function getColumns()
    {
        $model = new static;

        $columns = collect(Schema::connection($model->getConnectionName())->getColumnListing($model->getTable()));
        $columns->shift();

        return $columns;
    }

    public static function addColumn($columnName, $after = null)
    {
        $model = new static;

        $columns = self::getColumns();

        if (! $after || ! $columns->contains($after)) {
            Schema::connection($model->getConnectionName())
                ->table($model->getTable(), function (Blueprint $table) use ($columnName) {
                    $table->string($columnName)->nullable();
                });

            return $model;
        }

        $newColumns = $columns;
        $newColumns->splice($columns->search($after) + 1, 0, $columnName);

        return self::reorderColumns($newColumns);
    }

    public static function reorderColumns($newColumns)
    {
        $model = new static;

        $columns = self::getColumns();

        Schema::connection($model->getConnectionName())->rename($model->getTable(), $model->getTable().'_temp_rename');

        self::createTable($model, $newColumns);

        DB::connection($model->getConnectionName())
            ->table($model->getTable())->insertUsing(
                $columns->all(),
                DB::connection($model->getConnectionName())
                    ->table($model->getTable().'_temp_rename')
                    ->select($columns->all())
            );

        Schema::connection($model->getConnectionName())->dropIfExists($model->getTable().'_temp_rename');

        return $model;
    }

    public static function dropColumn(array | string $columnNames)
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

    private static function insert($model, $data)
    {
        $data->each(function ($row) use ($model) {
            DB::connection($model->getConnectionName())
                ->table($model->getTable())
                ->insert($row->toArray());
        });
    }

    private static function createTable($model, $header)
    {
        Schema::connection($model->getConnectionName())->dropIfExists($model->getTable());
        Schema::connection($model->getConnectionName())
            ->create($model->getTable(), function (Blueprint $table) use ($header) {
                $table->id();
                foreach ($header as $column) {
                    $table->string($column)->nullable();
                }
            });
    }
}
