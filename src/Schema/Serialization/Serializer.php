<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Serialization;

use Averay\Csv\Exceptions\InvalidCellException;
use Averay\Csv\Exceptions\InvalidRowException;
use Averay\Csv\Schema\Schema;

final readonly class Serializer implements DeserializerInterface, SerializerInterface
{
  public function __construct(private Schema $schema)
  {
  }

  public function deserializeRecord(array $record, ?int $row = null): array
  {
    $deserializedRecord = [];
    foreach ($this->schema->columns as $columnName => $columnType) {
      if (!\array_key_exists($columnName, $record)) {
        throw new InvalidRowException(\sprintf('Missing column "%s".', $columnName), $row);
      }
      try {
        $deserializedRecord[$columnName] = $columnType->deserialize($record[$columnName]);
      } catch (\UnexpectedValueException $exception) {
        throw new InvalidCellException($exception->getMessage(), $columnName, $row, previous: $exception);
      }
    }
    return $deserializedRecord;
  }

  public function serializeRecord(array $record): array
  {
    $serializedRecord = [];
    foreach ($this->schema->columns as $columnName => $columnType) {
      if (\array_key_exists($columnName, $record)) {
        $value = $record[$columnName];
      } else if ($columnType->default !== null) {
        $value = $columnType->default;
      } else if ($columnType->nullable) {
        $value = null;
      } else {
        throw new \OutOfBoundsException(\sprintf('Column "%s" is required.', $columnName));
      }
      $serializedRecord[$columnName] = $columnType->serialize($value);
    }
    return $serializedRecord;
  }
}
