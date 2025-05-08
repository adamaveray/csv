<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @extends AbstractType<bool>
 */
final class BoolType extends AbstractType
{
  public function __construct(
    bool $nullable = false,
    ?bool $defaultValue = null,
    public readonly string $valueTrue = 'true',
    public readonly string $valueFalse = 'false',
  ) {
    parent::__construct($nullable, $defaultValue);
  }

  public function deserialize(string $value): bool
  {
    return match ($value) {
      $this->valueTrue => true,
      $this->valueFalse => false,
      default => (bool) $value,
    };
  }

  public function serialize(mixed $value): string
  {
    \assert(\is_bool($value));
    return $value ? $this->valueTrue : $this->valueFalse;
  }
}
