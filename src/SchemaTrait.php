<?php
declare(strict_types=1);

namespace Averay\Csv;

use Averay\Csv\Schema\Schema;
use Averay\Csv\Schema\Types\TypeInterface;

trait SchemaTrait
{
  private ?Schema $schema = null;

  /**
   * @param Schema|array<string, TypeInterface> $schema
   */
  public function setSchema(Schema|array $schema): static
  {
    $this->schema = \is_array($schema) ? new Schema($schema) : $schema;
    return $this;
  }

  public function getSchema(): ?Schema
  {
    return $this->schema;
  }
}
