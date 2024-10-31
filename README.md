# Eloquent CSV

An elegant way to read, manipulate and write CSV files using Laravel Eloquent and Collections using in memory sqlite.

## Installation

```bash
composer require trym/eloquent-csv
```

## Eloquent Model Trait

Add the `EloquentCsv\Traits\Csv` trait to an Eloquent model to enable reading from a CSV file.

```php
<?php

namespace App\Models;

use EloquentCsv\Traits\Csv;

class Customer extends Model
{
    use Csv;
}
```

Specify the CSV file on the public `$csvFile` property. The file path should be relative to the Laravel storage folder path.

```php
use EloquentCsv\Traits\Csv;

class Customer extends Model
{
    use Csv;

    public $csvFile = 'customers.csv';
}
```

If you need a custom path you can use the `csvFile()` method instead.

```php
use EloquentCsv\Traits\Csv;

class Customer extends Model
{
    use Csv;

    public function csvFile()
    {
        return '/some/other/folder/customer.csv';
    }
}
```

The header from the CSV file will automatically be converted into attributes on the model allowing querying of fields using all the standard Eloquent model methods.

```php

use App\Models\Customer;

// Get all customers
$customers = Customer::all();

// Get name and email of customers where the email is john@email.com
$customers = Customer::select(['name', 'email'])->where('email', 'john@email.com')->get();
```

## Collection to CSV

Once you have a collection of records you can write them to a CSV file by using the `toCsv()` method.

```php
$customers = Customer::all();

$customers->toCsv(storage_path('customers.copy.csv'));
```