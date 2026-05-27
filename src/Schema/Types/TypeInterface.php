<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @template TType
 */
interface TypeInterface
{
  public bool $nullable { get; } // prettier-ignore -- Incorrectly strips the `{ get; }` component

  /** @var TType|null */
  public mixed $default { get; } // prettier-ignore -- Incorrectly strips the `{ get; }` component

  /**
   * @return TType
   */
  public function deserialize(string $value): mixed;

  /**
   * @param TType $value
   */
  public function serialize(mixed $value): string;
}
