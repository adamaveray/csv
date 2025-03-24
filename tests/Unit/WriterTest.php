<?php
declare(strict_types=1);

namespace Averay\Csv\Tests\Unit;

use Averay\Csv\HeaderFormattingTrait;
use Averay\Csv\SchemaTrait;
use Averay\Csv\Schema\Schema;
use Averay\Csv\Schema\Types;
use Averay\Csv\Tests\Resources\TemporaryStream;
use Averay\Csv\Writer;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;

#[CoversClass(Writer::class)]
#[CoversTrait(HeaderFormattingTrait::class)]
#[CoversTrait(SchemaTrait::class)]
final class WriterTest extends TestCase
{
  public function testHeaderFormatter(): void
  {
    $stream = new TemporaryStream();

    $writer = Writer::createFromStream($stream->stream);
    $writer->setHeaderFormatter(static fn(string $header): string => '[[' . $header . ']]');
    $writer->insertHeader(['Column One', 'Column Two']);

    self::assertEquals(
      [['[[Column One]]', '[[Column Two]]']],
      $stream->toArray(),
      'The header should be formatted with the custom formatter.',
    );
  }

  public function testSchemaStorage(): void
  {
    $schema = new Schema([]);

    $writer = Writer::createFromString();
    $writer->setSchema($schema);

    self::assertSame($schema, $writer->getSchema(), 'The schema object should be stored.');
  }

  #[Depends('testSchemaStorage')]
  #[DataProvider('schemaUsageDataProvider')]
  public function testSchemaUsage(string $expected, array $records, Schema $schema): void
  {
    $stream = new TemporaryStream();

    $writer = Writer::createFromStream($stream->stream)->setSchema($schema);

    $writer->insertHeader(\array_keys($schema->columns));
    $writer->insertAll($records);

    self::assertEquals($expected, \rtrim($stream->toString()), 'The CSV should be processed according to the schema.');
  }

  public static function schemaUsageDataProvider(): iterable
  {
    $tz = new \DateTimeZone('Antarctica/McMurdo');
    $utc = new \DateTimeZone('UTC');
    yield 'CSV' => [
      \sprintf(
        <<<'CSV'
        string,int,float,bool,datetime,date,time,list,json,serialized
        "Hello World",123,123.45,true,2025-01-01T01:23:45Z,2025-01-01,01:23:45,"one,two,three","{""one"":""two""}","%s"
        CSV,
        str_replace('"', '""', (\serialize(['hello' => 'world']))),
      ),
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
