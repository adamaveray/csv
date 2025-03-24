<?php
declare(strict_types=1);

namespace Averay\Csv\Tests\Unit;

use Averay\Csv\HeaderFormattingTrait;
use Averay\Csv\Reader;
use Averay\Csv\Schema\Schema;
use Averay\Csv\Schema\Types;
use Averay\Csv\SchemaTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

#[CoversClass(Reader::class)]
#[CoversTrait(HeaderFormattingTrait::class)]
#[CoversTrait(SchemaTrait::class)]
final class ReaderTest extends TestCase {
  private const string TEST_CSV_STRING = <<<'CSV'
Column One,Column Two
one,two
CSV;
  private const array TEST_CSV_HEADERS_SLUGGED = ['column-one', 'column-two'];

  public function testHeaderFormatter(): void
  {
    $reader = Reader::createFromString(self::TEST_CSV_STRING);

    $reader->setHeaderFormatter(static fn (string $header): string => '[[' . $header . ']]');
    self::assertEquals(
      ['[[Column One]]', '[[Column Two]]'],
      $reader->getHeader(),
      'The header should be formatted with the custom formatter.',
    );
  }

  #[Depends('testHeaderFormatter')]
  public function testSlugHeaders(): void
  {
    $reader = Reader::createFromString(self::TEST_CSV_STRING);
    $reader->slugHeaders();

    self::assertEquals(
      self::TEST_CSV_HEADERS_SLUGGED,
      $reader->getHeader(),
      'The headers should be slugged.',
    );
    self::assertEquals(
      self::TEST_CSV_HEADERS_SLUGGED,
      \array_keys($reader->toArray()[0]),
      'The slugged headers should be used for row keys.',
    );
  }

  public function testSchemaStorage(): void
  {
    $schema = new Schema([]);

    $reader = Reader::createFromString();
    $reader->setSchema($schema);

    self::assertSame($schema, $reader->getSchema(), 'The schema object should be stored.');
  }

  #[Depends('testSchemaStorage')]
  #[DataProvider('schemaUsageDataProvider')]
  public function testSchemaUsage(array $expected, string $csv, Schema $schema): void
  {
    $reader = Reader::createFromString($csv)->setSchema($schema);
    self::assertEquals($expected, $reader->toArray(), 'The CSV should be processed according to the schema.');
  }

  public static function schemaUsageDataProvider(): iterable
  {
    $tz = new \DateTimeZone('Antarctica/McMurdo');
    $utc = new \DateTimeZone('UTC');
    yield 'CSV' => [
      [
        [
          'string' => 'Hello World',
          'int' => 123,
          'float' => 123.45,
          'bool' => true,
          'datetime' => new \DateTimeImmutable('2025-01-01T01:23:45Z', $tz),
          'date' => new \DateTimeImmutable('2025-01-01T00:00:00Z', $utc),
          'time' => new \DateTimeImmutable('1970-01-01T01:23:45Z', $utc),
          'list' => ['one', 'two', 'three'],
          'json' => ['one' => 'two'],
          'serialized' => ['hello' => 'world'],
        ],
      ],
      \sprintf(
        <<<'CSV'
        string,int,float,bool,datetime,date,time,list,json,serialized
        Hello World,123,123.45,1,2025-01-01T01:23:45Z,2025-01-01,01:23:45,"one,two,three",{"one":"two"},"%s"
        CSV,
       str_replace('"', '""', (\serialize(['hello' => 'world']))),
      ),
      new Schema([
        'string' => new Types\StringType(),
        'int' => new Types\IntType(),
        'float' => new Types\FloatType(),
        'bool' => new Types\BoolType(),
        'datetime' => new Types\DateTimeType(timezone: $tz),
        'date' => new Types\DateType(),
        'time' => new Types\TimeType(),
        'list' => new Types\ListType(),
        'json' => new Types\JsonType(),
        'serialized' => new Types\SerializedType(),
      ]),
    ];
  }
}
