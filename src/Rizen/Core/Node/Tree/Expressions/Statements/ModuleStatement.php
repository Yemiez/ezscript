<?php

namespace Rizen\Core\Node\Tree\Expressions\Statements;

use Rizen\Core\Node\Node;
use Rizen\Core\Node\Tree\Expressions\ModuleIdentifierExpression;
use Rizen\Core\Source\SourceRange;

class ModuleStatement extends Statement
{
    private ?ModuleIdentifierExpression $moduleIdentifier;

    public function __construct(
        ?ModuleIdentifierExpression $moduleIdentifier = null,
        ?SourceRange $sourceRange = null,
        ?Node $parent = null
    ) {
        parent::__construct($sourceRange, $parent);
        $this->moduleIdentifier = $moduleIdentifier;
    }

    public function getNodeName(): string
    {
        return 'module';
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