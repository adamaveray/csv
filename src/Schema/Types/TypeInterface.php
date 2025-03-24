<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @template TType
 */
interface TypeInterface
{
  public bool $nullable { get; }

  /** @var TType|null */
  public mixed $default { get; }

  /**
   * @return TType
   */
  public function deserialize(string $value): mixed;

  /**
   * @param TType $value
   */
  public function serialize(mixed $value): string;
}
