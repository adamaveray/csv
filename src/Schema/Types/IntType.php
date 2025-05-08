<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @extends AbstractType<int>
 */
final class IntType extends AbstractType
{
  public function __construct(bool $nullable = false, ?int $defaultValue = null)
  {
    parent::__construct($nullable, $defaultValue);
  }

  public function deserialize(string $value): int
  {
    \assert(\is_numeric($value));
    return (int) $value;
  }

  public function serialize(mixed $value): string
  {
    return (string) $value;
  }
}
