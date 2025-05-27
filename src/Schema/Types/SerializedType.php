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
  ) {
    parent::__construct($nullable, $default);
  }

  #[\Override]
  public function deserialize(string $value): mixed
  {
    // Ensure error thrown on unserialization failure
    \set_error_handler(
      static fn(int $severity, string $message): never => throw new \ErrorException($message, 0, $severity),
    );

    try {
      /** @var TData */
      return \unserialize($value, ['allowed_classes' => $this->allowedClasses]);
    } catch (\Throwable $exception) {
      throw new \UnexpectedValueException('Invalid serialized value.', previous: $exception);
    } finally {
      \restore_error_handler();
    }
  }

  #[\Override]
  public function serialize(mixed $value): string
  {
    return \serialize($value);
  }
}
