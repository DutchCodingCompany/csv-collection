# CSV Collection

[![Latest Version on Packagist](https://img.shields.io/packagist/v/dutchcodingcompany/csv-collection.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/csv-collection)
[![Total Downloads](https://img.shields.io/packagist/dt/dutchcodingcompany/csv-collection.svg?style=flat-square)](https://packagist.org/packages/dutchcodingcompany/csv-collection)

This package provides a simple but powerful way to read and write large CSV files using the power of Laravel's lazy
collections.

## Installation

You can install the package via composer:

```bash
composer require dutchcodingcompany/csv-collection
```

## Usage

You may create a collection using the `new` keyword or the `make` method.

```php 
CsvCollection::make();
```

This gives you access to all [Collection](https://laravel.com/docs/8.x/collections#available-methods)
and [Lazy Collection](https://laravel.com/docs/8.x/collections#lazy-collection-methods) methods.

### Open

To open a file and load it's content into a new collection you may use the `open` method on the collection.

```php
use DutchCodingCompany\CsvCollection\CsvCollection;

CsvCollection::make()
    ->open('path/to/file.csv')
    ->count();
```

### Save

To save the collection items to a file you may use the `save` method on the collection.

```php
use DutchCodingCompany\CsvCollection\CsvCollection;

CsvCollection::make(static function () {
    yield [
        'key' => 'value',
    ];
})
    ->save('path/to/file.csv');
```

#### Model exports

When exporting models a memory efficient method is to lazily iterate through the models and `yield` it's content.

```php
use DutchCodingCompany\CsvCollection\CsvCollection;

CsvCollection::make(static function () {
    $models = Model::query()->lazy();
    
    foreach ($models as $model){
        yield $model->only([
            'id',
            //
        ]);
    }
})
    ->save('path/to/file.csv');
```

### Options

The following options are available to suit your needs:

- `header`, default: `true`
- `delimiter`, default: `,`
- `enclosure`, default: `"`
- `escape`, default: `\\`

These options could be passed to the `open` and `save` methods, be set using the `options` method, or be set as the
global default using the static `defaults` method.

#### Header

When using a header, lines will contain an associated array. Otherwise, lines will contain an indexed array.

```php
// Without header
[
    0 => 'John',
    1 => 'Doe',
]

// With header
[
    'first_name' => 'John',
    'last_name' => 'Doe',
]
```

_**Note**: When saving a collection to a file the keys of the first element in the collection will be used as the
header._

## Testing

```bash
composer test
```

## Credits

- [Bjorn Voesten](https://github.com/bjornvoesten)
- [Dutch Coding Company](https://github.com/dutchcodingcompany)
- [All contributors](https://github.com/dutchcodingcompany/csv-collection/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
