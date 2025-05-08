<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @template TData
 * @extends AbstractType<TData>
 */
final class JsonType extends AbstractType
{
  /**
   * @param TData|null $defaultValue
   */
  public function __construct(
    bool $nullable = false,
    mixed $defaultValue = null,
    private readonly bool $associative = true,
    private readonly int $flags = \JSON_INVALID_UTF8_SUBSTITUTE,
  ) {
    parent::__construct($nullable, $defaultValue);
  }

  /**
   * @return TData
   */
  public function deserialize(string $value): mixed
  {
    return \json_decode($value, $this->associative, flags: \JSON_THROW_ON_ERROR | $this->flags);
  }

  public function serialize(mixed $value): string
  {
    return \json_encode($value, flags: \JSON_THROW_ON_ERROR | $this->flags);
  }
}
