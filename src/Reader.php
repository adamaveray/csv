<?php
declare(strict_types=1);

namespace Averay\Csv;

use Averay\Csv\Schema\Schema;
use Averay\Csv\Schema\Serialization\Serializer;
use Averay\Csv\Schema\Types\TypeInterface;
use League\Csv\MapIterator;
use League\Csv\SyntaxError;
use Symfony\Component\String\Slugger;

/**
 * @template TRow of array
 *
 * @method \Iterator<array-key, TRow> getRecords(array<string> $header = [])
 * @method array<array-key, TRow> getRecords(array<string> $header = [])
 */
class Reader extends \League\Csv\Reader
{
  use HeaderFormattingTrait {
    setHeaderFormatter as private setHeaderFormatterInner;
  }
  use SchemaTrait {
    setSchema as private setSchemaInner;
  }

  public function slugHeaders(?Slugger\SluggerInterface $slugger = null, ?int $headerOffset = 0): static
  {
    return $this->setHeaderFormatter(
      static fn(string $header): string => static::slug($header, $slugger),
      $headerOffset,
    );
  }

  /**
   * @param (callable(string):string)|null $headerFormatter
   */
  public function setHeaderFormatter(?callable $headerFormatter, ?int $headerOffset = 0): static
  {
    if ($headerOffset !== null) {
      $this->setHeaderOffset($headerOffset);
    }
    return $this->setHeaderFormatterInner($headerFormatter);
  }

  /**
   * @param Schema|array<string, TypeInterface> $schema
   */
  public function setSchema(Schema|array $schema, ?int $headerOffset = 0): static
  {
    if ($headerOffset !== null) {
      $this->setHeaderOffset($headerOffset);
    }
    return $this->setSchemaInner($schema);
  }

  /**
   * @return array<string>
   * @throws SyntaxError
   */
  #[\Override]
  protected function setHeader(int $offset): array
  {
    $header = parent::setHeader($offset);
    if ($this->headerFormatter) {
      $header = \array_map($this->headerFormatter, $header);
    }
    if ($this->schema !== null) {
      // Validate header
      $schemaHeader = \array_keys($this->schema->columns);
      if ($header !== $schemaHeader) {
        throw new Exceptions\InvalidHeaderException($header, expected: $schemaHeader);
      }
    }
    return $header;
  }

  /**
   * @return \Iterator<array-key, TRow>
   */
  #[\Override]
  protected function combineHeader(\Iterator $iterator, array $header): \Iterator
  {
    // Combine headers
    if (!empty($header)) {
      $iterator = new MapIterator($iterator, static function (array $record) use ($header): array {
        $assocRecord = [];
        foreach ($header as $offset => $headerName) {
          $assocRecord[$headerName] = $record[$offset] ?? null;
        }
        return $assocRecord;
      });
    }

    // Deserialize for schema
    if ($this->schema !== null) {
      $serializer = new Serializer($this->schema);
      $iterator = new MapIterator($iterator, $serializer->deserializeRecord(...));
    }

    // Apply formatters
    if (!empty($this->formatters)) {
      $iterator = new MapIterator(
        $iterator,
        fn(array $record): array => array_reduce(
          $this->formatters,
          static fn(array $record, \Closure $formatter): array => $formatter($record),
          $record,
        ),
      );
    }

    return $iterator;
  }

  /**
   * @return list<TRow> The rows of the CSV with row numbers discarded.
   */
  public function toArray(): array
  {
    $rows = [];
    foreach ($this->getIterator() as $row) {
      $rows[] = $row;
    }
    return $rows;
  }

  protected static function slug(string $value, ?Slugger\SluggerInterface $slugger): string
  {
    $slugger ??= new Slugger\AsciiSlugger();
    return $slugger->slug($value)->lower()->toString();
  }
}
