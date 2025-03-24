<?php
declare(strict_types=1);

namespace Averay\Csv\Schema;

use Averay\Csv\Schema\Types\TypeInterface;

final readonly class Schema
{
  /**
   * @param array<string, TypeInterface> $columns
   */
  public function __construct(public array $columns)
  {
    foreach ($this->columns as $column => $schema) {
      \assert(\is_string($column), 'Column names must be strings.');
      \assert(
        $schema instanceof TypeInterface,
        \sprintf('Column schemas must be instances of %s.', TypeInterface::class),
      );
    }
  }
}
