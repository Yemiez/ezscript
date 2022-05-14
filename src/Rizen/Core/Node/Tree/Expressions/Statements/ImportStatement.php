<?php

namespace Rizen\Core\Node\Tree\Expressions\Statements;

use Rizen\Core\Node\Tree\Expressions\IdentifierExpression;
use Rizen\Core\Node\Tree\Expressions\ModuleIdentifierExpression;
use Rizen\Core\Node\Tree\Expressions\NamedTypeExpression;

class ImportStatement extends Statement
{
    private ?NamedTypeExpression $type = null;
    private ?NamedTypeExpression $alias = null;
    private ?ModuleIdentifierExpression $moduleIdentifier = null;

    public function getNodeName(): string
    {
        return 'import';
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

    public function getModuleIdentifier(): ?ModuleIdentifierExpression
    {
        return $this->moduleIdentifier;
    }

    public function setModuleIdentifier(?ModuleIdentifierExpression $moduleIdentifier): self
    {
        $this->moduleIdentifier = $moduleIdentifier;
        return $this;
    }
}