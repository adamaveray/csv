<?php
declare(strict_types=1);

namespace Averay\Csv;

use Averay\Csv\Schema\Serialization\Serializer;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;

/**
 * @api
 * @psalm-suppress DeprecatedInterface Parent class implements internally-deprecated interface ByteSequence.
 */
class Writer extends \League\Csv\Writer
{
  use HeaderFormattingTrait;
  use SchemaTrait;

  private bool $hasInserted = false;

  /**
   * @param list<string> $header
   */
  public function insertHeader(array $header): int
  {
    if ($this->hasInserted) {
      throw new Exception('Headers cannot be inserted after records have been inserted.');
    }
    if ($this->headerFormatter !== null) {
      $header = \array_map($this->headerFormatter, $header);
    }
    return $this->insertOne($header, applySchema: false);
  }

  #[\Override]
  public function insertOne(array $record, bool $applySchema = true): int
  {
    if ($applySchema && $this->schema !== null) {
      $serializer = new Serializer($this->schema);
      try {
        $record = $serializer->serializeRecord($record);
      } catch (\Throwable) {
        throw CannotInsertRecord::triggerOnInsertion($record);
      }
    }

    $this->hasInserted = true;
    return parent::insertOne($record);
  }
}
