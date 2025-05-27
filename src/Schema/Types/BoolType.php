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
    ?bool $default = null,
    public readonly string $valueTrue = 'true',
    public readonly string $valueFalse = 'false',
  ) {
    parent::__construct($nullable, $default);
  }

  #[\Override]
  public function deserialize(string $value): bool
  {
    return match ($value) {
      $this->valueTrue => true,
      $this->valueFalse => false,
      default => (bool) $value,
    };
  }

  #[\Override]
  public function serialize(mixed $value): string
  {
    /** @psalm-suppress RedundantConditionGivenDocblockType Additional validation for non-Psalm-using consumers. */
    \assert(\is_bool($value));
    return $value ? $this->valueTrue : $this->valueFalse;
  }
}
