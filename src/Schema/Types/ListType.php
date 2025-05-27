<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

use Averay\Csv\Reader;
use Averay\Csv\Writer;

/**
 * @template TItem extends string
 * @extends AbstractType<list<TItem>>
 */
final class ListType extends AbstractType
{
  /**
   * @param list<TItem>|null $default
   */
  public function __construct(bool $nullable = false, ?array $default = null, public readonly string $separator = ',')
  {
    parent::__construct($nullable, $default);
  }

  /**
   * @return list<TItem>
   */
  #[\Override]
  public function deserialize(string $value): array
  {
    $csv = Reader::createFromString($value)->toArray();
    if (\count($csv) !== 1) {
      throw new \InvalidArgumentException('Invalid list.');
    }
    /** @var list<TItem> */
    return $csv[0];
  }

  /**
   * @param list<TItem> $value
   */
  #[\Override]
  public function serialize(mixed $value): string
  {
    $stream = \fopen('php://temp', 'wb+');
    if ($stream === false) {
      throw new \UnexpectedValueException('Failed creating temporary stream.');
    }
    /** @psalm-suppress MixedArgumentTypeCoercion $value is list<TItem> which extends list<string>. */
    Writer::createFromStream($stream)->insertOne($value);
    \rewind($stream);
    $result = \stream_get_contents($stream);
    if ($result === false) {
      throw new \UnexpectedValueException('Failed serializing list.');
    }
    return \rtrim($result);
  }
}
