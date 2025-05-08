<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

final class DateTimeType extends AbstractDateTimeType
{
  public function __construct(
    bool $nullable = false,
    ?\DateTimeInterface $defaultValue = null,
    \DateTimeZone $timezone = new \DateTimeZone('UTC'),
    string $format = \DateTimeInterface::ATOM,
  ) {
    parent::__construct($format, $format, $nullable, $defaultValue, $timezone);
  }
}
