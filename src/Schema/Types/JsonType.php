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
   * @param TData|null $default
   */
  public function __construct(
    bool $nullable = false,
    mixed $default = null,
    private readonly bool $associative = true,
    private readonly int $flags = \JSON_INVALID_UTF8_SUBSTITUTE,
  ) {
    parent::__construct($nullable, $default);
  }

  /**
   * @return TData
   */
  #[\Override]
  public function deserialize(string $value): mixed
  {
    /** @var TData */
    return \json_decode($value, $this->associative, flags: \JSON_THROW_ON_ERROR | $this->flags);
  }

  /**
   * @param TData $value
   * @psalm-suppress MixedReturnTypeCoercion Psalm does not recognise that this will throw.
   */
  #[\Override]
  public function serialize(mixed $value): string
  {
    /** @var TData */
    return \json_encode($value, flags: \JSON_THROW_ON_ERROR | $this->flags);
  }
}
