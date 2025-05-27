<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @extends AbstractType<float>
 */
final class FloatType extends AbstractType
{
  public function __construct(bool $nullable = false, ?float $default = null)
  {
    parent::__construct($nullable, $default);
  }

  #[\Override]
  public function deserialize(string $value): float
  {
    \assert(\is_numeric($value));
    return (float) $value;
  }

  #[\Override]
  public function serialize(mixed $value): string
  {
    return (string) $value;
  }
}
