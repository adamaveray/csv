<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @template TType
 * @implements TypeInterface<TType>
 * @psalm-suppress PossiblyUnusedProperty Properties are declared by interface.
 */
abstract class AbstractType implements TypeInterface
{
  /**
   * @param bool $nullable
   * @param TType|null $default
   */
  public function __construct(public readonly bool $nullable = false, public readonly mixed $default = null) {}
}
