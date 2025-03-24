<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Serialization;

interface DeserializerInterface
{
  public function deserializeRecord(array $record, ?int $row = null): array;
}
