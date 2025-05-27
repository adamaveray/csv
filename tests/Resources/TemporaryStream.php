<?php
declare(strict_types=1);

namespace Averay\Csv\Tests\Resources;

use Averay\Csv\Reader;

final readonly class TemporaryStream
{
  /** @var resource */
  public mixed $stream;

  public function __construct()
  {
    $stream = \fopen('php://memory', 'wb+');
    if ($stream === false) {
      throw new \UnexpectedValueException('Failed creating temporary stream.');
    }
    $this->stream = $stream;
  }

  public function toString(): string
  {
    \rewind($this->stream);
    return \stream_get_contents($this->stream);
  }

  public function toArray(): array
  {
    \rewind($this->stream);
    return Reader::createFromStream($this->stream)->toArray();
  }

  public function __destruct()
  {
    \fclose($this->stream);
  }
}
