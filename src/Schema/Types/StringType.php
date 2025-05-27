<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @extends AbstractType<string>
 */
final class StringType extends AbstractType
{
  public function __construct(bool $nullable = false, ?string $default = null)
  {
    parent::__construct($nullable, $default);
  }

  #[\Override]
  public function deserialize(string $value): string
  {
    return $value;
  }

  #[\Override]
  public function serialize(mixed $value): string
  {
    return (string) $value;
  }
}
