<?php

namespace Rizen\Core\Node\Tree\Expressions\Statements;

use Rizen\Core\Node\Node;
use Rizen\Core\Node\Tree\Expressions\NamedTypeExpression;
use Rizen\Core\Source\SourceRange;

class ExportStatement extends Statement
{
    private ?NamedTypeExpression $type;
    private ?NamedTypeExpression $alias;

    public function __construct(
        ?NamedTypeExpression $type = null,
        ?NamedTypeExpression $alias = null,
        ?SourceRange $sourceRange = null,
        ?Node $parent = null
    ) {
        parent::__construct($sourceRange, $parent);
        $this->type = $type;
        $this->alias = $alias;
    }

    public function getNodeName(): string
    {
        return 'export';
    }

    public function getType(): ?NamedTypeExpression
    {
        return $this->type;
    }

    public function setType(?NamedTypeExpression $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getAlias(): ?NamedTypeExpression
    {
        return $this->alias;
    }

    public function setAlias(?NamedTypeExpression $alias): self
    {
        $this->alias = $alias;
        return $this;
    }
}