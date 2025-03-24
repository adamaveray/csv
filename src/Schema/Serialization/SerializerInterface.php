<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Serialization;

interface SerializerInterface
{
  public function serializeRecord(array $record): array;
}
