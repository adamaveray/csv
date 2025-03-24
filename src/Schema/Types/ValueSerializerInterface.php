<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @psalm-type TValue
 */
interface ValueSerializerInterface
{
  /**
   * @return TValue
   */
  public function deserialize(string $value): mixed;

  /**
   * @param TValue $value
   */
  public function serialize(mixed $value): string;
}
