<?php

namespace EloquentCsv\Model;

use EloquentCsv\CsvFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Sushi\Sushi;

trait Csv
{
    use Sushi;

    public function csvFile(): string
    {
        return $this->csvFile;
    }

    public function getRows()
    {
        return (new CsvFile())->read($this->csvFile());
    }

    // protected static function bootCsv()
    // {
    //     self::bootSuishi();
    // }

    // public static function fromCsv(string $filename): CsvModelInterface
    // {
    //     $rows = CSV::read($filename);
    //     $headers = $rows->first()->keys();

    //     $model = new static;
    //     $schema = Schema::connection($model->getConnectionName());
    //     $schema->dropIfExists($model->getTable());
    //     $schema->create($model->getTable(), function (Blueprint $table) use ($headers) {
    //         $table->id();
    //         $headers->each(fn ($header) => $table->string($header)->nullable());
    //     });

    //     foreach ($rows as $row) {
    //         DB::connection($model->getConnectionName())
    //             ->table($model->getTable())
    //             ->insert($row->toArray());
    //     }

    //     return $model;
    // }

    // public static function getColumns(): Collection
    // {
    //     $model = new static;

    //     $columns = collect(Schema::connection($model->getConnectionName())->getColumnListing($model->getTable()));
    //     $columns->shift();

    //     return $columns;
    // }

    // public static function addColumn(string $columnName): CsvModelInterface
    // {
    //     $model = new static;

    //     Schema::connection($model->getConnectionName())
    //         ->table($model->getTable(), function (Blueprint $table) use ($columnName) {
    //             $table->string($columnName)->nullable();
    //         });

    //     return $model;
    // }

    // public static function dropColumn(array|string $columnNames): CsvModelInterface
    // {
    //     $model = new static;

    //     if (! is_array($columnNames)) {
    //         $columnNames = [$columnNames];
    //     }

    //     Schema::connection($model->getConnectionName())
    //         ->table($model->getTable(), function (Blueprint $table) use ($columnNames) {
    //             foreach ($columnNames as $columnName) {
    //                 $table->dropColumn($columnName);
    //             }
    //         });

    //     return $model;
    // }
}
