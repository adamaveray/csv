<?php
declare(strict_types=1);

namespace Averay\Csv\Schema\Types;

/**
 * @extends AbstractType<\DateTimeInterface>
 */
abstract class AbstractDateTimeType extends AbstractType
{
  public function __construct(
    private readonly string $formatSerialize,
    private readonly string $formatDeserialize,
    bool $nullable = false,
    ?\DateTimeInterface $default = null,
    public readonly \DateTimeZone $timezone = new \DateTimeZone('UTC'),
    private readonly string $datePrefix = '',
    private readonly string $dateSuffix = '',
  ) {
    parent::__construct($nullable, $default);
  }

  public function deserialize(string $value): \DateTimeInterface
  {
    $fullValue = $this->datePrefix . $value . $this->dateSuffix;
    $datetime = \DateTimeImmutable::createFromFormat($this->formatDeserialize, $fullValue, $this->timezone);
    if ($datetime === false) {
      throw new \UnexpectedValueException('Invalid date format.');
    }
    return $datetime;
  }

  public function serialize(mixed $value): string
  {
    \assert($value instanceof \DateTimeInterface);
    $formatted = $value->format($this->formatSerialize);

    $suffixUtc = '+00:00';
    if (\str_ends_with($formatted, $suffixUtc)) {
      $formatted = \substr_replace($formatted, 'Z', -\strlen($suffixUtc));
    }
    return $formatted;
  }
}
