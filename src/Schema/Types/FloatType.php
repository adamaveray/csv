<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @extends AbstractType<float>
 */
final class FloatType extends AbstractType
{
  public function __construct(bool $nullable = false, ?float $defaultValue = null)
  {
    parent::__construct($nullable, $defaultValue);
  }

  public function deserialize(string $value): float
  {
    \assert(\is_numeric($value));
    return (float) $value;
  }

  public function serialize(mixed $value): string
  {
    return (string) $value;
  }
}
