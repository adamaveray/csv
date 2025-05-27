<?php
declare(strict_types=1);

namespace Averay\Csv;

use Averay\Csv\Schema\Schema;
use Averay\Csv\Schema\SchemaInterface;
use Averay\Csv\Schema\Types\TypeInterface;

trait SchemaTrait
{
  private ?SchemaInterface $schema = null;

  /**
   * @param SchemaInterface|array<string, TypeInterface> $schema
   * @return $this
   */
  public function setSchema(SchemaInterface|array $schema): static
  {
    $this->schema = \is_array($schema) ? new Schema($schema) : $schema;
    return $this;
  }

  public function getSchema(): ?SchemaInterface
  {
    return $this->schema;
  }
}
