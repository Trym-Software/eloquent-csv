<?php

use EloquentCsv\Tests\Feature\Models\Customer;

test('customer model reads and outputs to csv', function () {
    Storage::fake('test');

    $customers = Customer::all();
    $customers->toCsv(Storage::disk('test')->path('customer-10-output.csv'));

    expect($customers->count())->toBe(10);
    expect(Storage::disk('test')->get('customer-10-output.csv'))->toMatchSnapshot();
});
