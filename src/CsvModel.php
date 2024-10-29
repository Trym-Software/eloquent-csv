<?php

namespace EloquentCsv;

use EloquentCsv\Facades\CSV;
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

    public static function fromCsv(string $filename): CsvModelInterface
    {
        $rows = CSV::read($filename);
        $headers = $rows->first()->keys();

        $model = new static;
        $schema = Schema::connection($model->getConnectionName());
        $schema->dropIfExists($model->getTable());
        $schema->create($model->getTable(), function (Blueprint $table) use ($headers) {
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
