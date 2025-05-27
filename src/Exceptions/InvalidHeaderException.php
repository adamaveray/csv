<?php
declare(strict_types=1);

namespace Averay\Csv\Exceptions;

final class InvalidHeaderException extends SchemaException
{
  /**
   * @param list<string> $header
   * @param list<string> $expected
   */
  public function __construct(array $header, array $expected, int $code = 0, ?\Throwable $previous = null)
  {
    $message = \sprintf(
      'The header does not match the schema (expected "%s" but found "%s").',
      \implode(',', $expected),
      \implode(',', $header),
    );
    parent::__construct($message, $code, $previous);
  }
}
