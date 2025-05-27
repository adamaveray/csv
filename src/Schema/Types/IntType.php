<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @extends AbstractType<int>
 */
final class IntType extends AbstractType
{
  public function __construct(bool $nullable = false, ?int $default = null)
  {
    parent::__construct($nullable, $default);
  }

  #[\Override]
  public function deserialize(string $value): int
  {
    \assert(\is_numeric($value));
    return (int) $value;
  }

  #[\Override]
  public function serialize(mixed $value): string
  {
    return (string) $value;
  }
}
