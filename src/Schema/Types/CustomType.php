<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @template TType
 * @extends AbstractType<TType>
 */
final class CustomType extends AbstractType
{
  /**
   * @param ValueSerializerInterface<TType> $transformer
   * @param TType|null $default
   */
  public function __construct(
    private readonly ValueSerializerInterface $transformer,
    bool $nullable = false,
    mixed $default = null,
  )
  {
    parent::__construct($nullable, $default);
  }

  public function deserialize(string $value): mixed
  {
    return $this->transformer->deserialize($value);
  }

  public function serialize(mixed $value): string
  {
    return $this->transformer->serialize($value);
  }
}
