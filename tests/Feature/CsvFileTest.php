<?php

use EloquentCsv\Facades\CSV;

test('reading csv file', function () {
    $collection = CSV::read(storage_path('customers-10.csv'));

    expect($collection->all())->toMatchSnapshot();
});

test('writing csv file', function () {
    Storage::fake('test');

    $data = collect([
        ['name' => 'John Doe', 'email' => 'john@email.com'],
        ['name' => 'Jane Doe', 'email' => 'jane@email.com'],
    ]);

    CSV::write(Storage::disk('test')->path('test.csv'), $data);

    expect(Storage::disk('test')->get('test.csv'))->toMatchSnapshot();
});