<?php
declare(strict_types=1);

namespace Averay\Csv\Schema;

use Averay\Csv\Schema\Types\TypeInterface;

interface SchemaInterface
{
  /** @return array<string, TypeInterface> */
  public function getColumns(): array;
}
