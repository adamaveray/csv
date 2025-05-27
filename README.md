# CSV

Extensions to [the League CSV library](https://csv.thephpleague.com) to support declaring CSV schemas used for reading & writing.

## Installation

```
composer install averay/csv
```

## Schemas

A schema can be defined in a standalone class implementing `Averay\Csv\Schema\SchemaInterface`, or directly in an array on a `Reader` or `Writer` instance.

```php
<?php
use Averay\Csv\Reader;
use Averay\Csv\Schema\Types;

$reader = Reader::createFromPath('/path/to/csv');
$reader->setSchema([
  'id' => new Types\StringType(),
  'label' => new Types\StringType(nullable: true),
  'count' => new Types\IntType(),
]);
```

Records retrieved from or written to a CSV will be validated against the schema. Schema keys will be used as headers for the CSV.

## Additional Processing

A `slugHeaders()` method is provided on the `Reader` class, which after calling will apply a slug transformation to headers. This allows CSVs to be authored with title-cased headers but referenced by simplified slugged representations.

---

[MIT Licenced](./LICENSE)
