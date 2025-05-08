<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

use Averay\Csv\Reader;
use Averay\Csv\Writer;

/**
 * @template TItem
 * @extends AbstractType<list<TItem>>
 */
final class ListType extends AbstractType
{
  /**
   * @param list<TItem>|null $defaultValue
   */
  public function __construct(
    bool $nullable = false,
    ?array $defaultValue = null,
    public readonly string $separator = ',',
  ) {
    parent::__construct($nullable, $defaultValue);
  }

  public function deserialize(string $value): array
  {
    $csv = Reader::createFromString($value)->toArray();
    if (\count($csv) !== 1) {
      throw new \InvalidArgumentException('Invalid list.');
    }
    return $csv[0];
  }

  public function serialize(mixed $value): string
  {
    $stream = \fopen('php://temp', 'wb+');
    Writer::createFromStream($stream)->insertOne($value);
    \rewind($stream);
    $result = \stream_get_contents($stream);
    if ($result === false) {
      throw new \UnexpectedValueException('Failed to serialize list.');
    }
    return \rtrim($result);
  }
}
