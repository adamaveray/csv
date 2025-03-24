<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @template TData
 * @extends AbstractType<TData>
 */
final class SerializedType extends AbstractType
{
  /**
   * @param TData|null $default
   * @param bool|list<class-string> $allowedClasses
   */
  public function __construct(
    bool $nullable = false,
    mixed $default = null,
    private readonly bool|array $allowedClasses = false,
  )
  {
    parent::__construct($nullable, $default);
  }

  public function deserialize(string $value): mixed
  {
    $result = \unserialize($value, ['allowed_classes' => $this->allowedClasses]);
    if ($result === false) {
      var_dump($value, $this);
      throw new \UnexpectedValueException('Invalid serialized value.');
    }
    return $result;
  }

  public function serialize(mixed $value): string
  {
    return \serialize($value);
  }
}
