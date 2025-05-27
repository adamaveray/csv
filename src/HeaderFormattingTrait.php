<?php
declare(strict_types=1);

namespace Averay\Csv;

trait HeaderFormattingTrait
{
  /** @var (callable(string):string)|null */
  protected mixed $headerFormatter = null;

  /**
   * @param (callable(string):string)|null $headerFormatter
   * @return $this
   */
  public function setHeaderFormatter(?callable $headerFormatter): static
  {
    $this->headerFormatter = $headerFormatter;
    return $this;
  }
}
