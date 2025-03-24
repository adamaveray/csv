<?php
declare(strict_types=1);

namespace Averay\Csv\Exceptions;

final class InvalidRowException extends SchemaException
{
  public function __construct(string $message, ?int $row, int $code = 0, ?\Throwable $previous = null)
  {
    if ($row !== null) {
      $message .= \sprintf(' (on row %d)', $row);
    }
    parent::__construct($message, $code, $previous);
  }
}
