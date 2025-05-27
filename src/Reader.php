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
 * @extends \League\Csv\Reader<TRow>
 *
 * @api
 * @psalm-suppress DeprecatedInterface Parent class implements internally-deprecated interface ByteSequence.
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
   * @return $this
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
   * @return $this
   */
  public function setSchema(Schema|array $schema, ?int $headerOffset = 0): static
  {
    if ($headerOffset !== null) {
      $this->setHeaderOffset($headerOffset);
    }
    return $this->setSchemaInner($schema);
  }

  /**
   * @return list<string>
   * @throws SyntaxError
   */
  #[\Override]
  protected function setHeader(int $offset): array
  {
    $header = parent::setHeader($offset);
    \assert(\array_is_list($header));
    if ($this->headerFormatter !== null) {
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
        /** @var array-key $headerName */
        foreach ($header as $offset => $headerName) {
          /** @psalm-suppress MixedAssignment */
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
        fn(array $record): array => \array_reduce(
          $this->formatters,
          /** @psalm-suppress MixedReturnStatement Psalm does not support generics on closures. */
          static fn(array $record, \Closure $formatter): array => $formatter($record),
          $record,
        ),
      );
    }

    /** @var \Iterator<array-key, TRow> */
    return $iterator;
  }

  /**
   * @return list<TRow> The rows of the CSV with row numbers discarded.
   */
  public function toArray(): array
  {
    $rows = [];
    foreach ($this->getIterator() as $row) {
      /** @var TRow */
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
