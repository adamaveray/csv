<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Serialization;

interface SerializerInterface
{
  /**
   * @template T of array-key
   * @param array<T, mixed> $record
   * @return array<T, string>
   */
  public function serializeRecord(array $record): array;
}
