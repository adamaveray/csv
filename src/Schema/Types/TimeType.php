<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

final class TimeType extends AbstractDateTimeType
{
  public function __construct(
    bool $nullable = false,
    ?\DateTimeInterface $defaultValue = null,
    string $format = 'H:i:s',
  ) {
    parent::__construct(
      $format,
      'Y-m-d\\T' . $format . 'P',
      $nullable,
      $defaultValue,
      datePrefix: '1970-01-01T',
      dateSuffix: 'Z',
    );
  }
}
