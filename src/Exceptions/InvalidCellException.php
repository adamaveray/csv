<?php
declare(strict_types=1);

namespace Averay\Csv\Exceptions;

final class InvalidCellException extends SchemaException
{
  public function __construct(
    string $message,
    int|string $column,
    ?int $row,
    int $code = 0,
    ?\Throwable $previous = null,
  ) {
    $message .= ' (in column "' . $column . '"';
    if ($row !== null) {
      $message .= ' on row ' . $row;
    }
    $message .= ')';

    parent::__construct($message, $code, $previous);
  }
}
