<?php
declare(strict_types=1);

namespace Averay\Csv\Schema;

use Averay\Csv\Schema\Types\TypeInterface;

/**
 * @api
 */
readonly class Schema implements SchemaInterface
{
  /**
   * @param array<string, TypeInterface> $columns
   * @psalm-suppress RedundantConditionGivenDocblockType Additional validation for non-Psalm-using consumers.
   */
  public function __construct(private array $columns)
  {
    foreach ($this->columns as $column => $schema) {
      \assert(\is_string($column), 'Column names must be strings.');
      \assert(
        $schema instanceof TypeInterface,
        \sprintf('Column schemas must be instances of %s.', TypeInterface::class),
      );
    }
  }

  #[\Override]
  public function getColumns(): array
  {
    return $this->columns;
  }
}
