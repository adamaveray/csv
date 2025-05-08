<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

final class DateType extends AbstractDateTimeType
{
  public function __construct(
    bool $nullable = false,
    ?\DateTimeInterface $defaultValue = null,
    string $format = 'Y-m-d',
  ) {
    parent::__construct($format, $format . '\\TH:i:sP', $nullable, $defaultValue, dateSuffix: 'T00:00:00Z');
  }
}
