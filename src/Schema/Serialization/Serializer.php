<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Serialization;

use Averay\Csv\Exceptions\InvalidCellException;
use Averay\Csv\Exceptions\InvalidRowException;
use Averay\Csv\Schema\SchemaInterface;
use Averay\Csv\Schema\Types\TypeInterface;

final readonly class Serializer implements DeserializerInterface, SerializerInterface
{
  public function __construct(private SchemaInterface $schema) {}

  #[\Override]
  public function deserializeRecord(array $record, ?int $row = null): array
  {
    $deserializedRecord = [];
    foreach ($this->schema->getColumns() as $columnName => $columnType) {
      /** @psalm-suppress MixedAssignment */
      $value =
        $record[$columnName] ?? throw new InvalidRowException(\sprintf('Missing column "%s".', $columnName), $row);
      /** @psalm-suppress MixedArgument,MixedAssignment */
      $deserializedRecord[$columnName] = $this->deserializeCell($value, $columnName, $columnType, $row);
    }
    return $deserializedRecord;
  }

  private function deserializeCell(string $value, string $columnName, TypeInterface $columnType, ?int $row): mixed
  {
    if ($value === '') {
      // Empty cell
      /** @psalm-suppress MixedAssignment */
      $defaultValue = $columnType->default;
      if ($defaultValue === null && !$columnType->nullable) {
        throw new InvalidCellException(
          \sprintf('Missing value for non-nullable column "%s".', $columnName),
          $columnName,
          $row,
        );
      }
      return $defaultValue;
    }

    try {
      return $columnType->deserialize($value);
    } catch (\UnexpectedValueException $exception) {
      throw new InvalidCellException($exception->getMessage(), $columnName, $row, previous: $exception);
    }
  }

  /**
   * @template TKey of array-key
   * @param array<TKey, mixed> $record
   * @return array<TKey, string>
   */
  #[\Override]
  public function serializeRecord(array $record): array
  {
    /** @var array<TKey, string> $serializedRecord */
    $serializedRecord = [];
    foreach ($this->schema->getColumns() as $columnName => $columnType) {
      if (\array_key_exists($columnName, $record)) {
        /** @psalm-suppress MixedAssignment */
        $value = $record[$columnName];
      } elseif ($columnType->default !== null) {
        /** @psalm-suppress MixedAssignment */
        $value = $columnType->default;
      } elseif ($columnType->nullable) {
        $value = null;
      } else {
        throw new \OutOfBoundsException(\sprintf('Column "%s" is required.', $columnName));
      }
      $serializedRecord[$columnName] = $columnType->serialize($value);
    }
    /** @var array<TKey, string> */
    return $serializedRecord;
  }
}
