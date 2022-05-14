<?php

namespace Rizen\Core\Node\Tree\Expressions;

use Rizen\Core\Node\Node;
use Rizen\Core\Source\SourceRange;

class IdentifierExpression extends Expression
{
    private string $identifier = '';

    public function __construct(string $identifier = '', ?SourceRange $sourceRange = null, ?Node $parent = null)
    {
        parent::__construct($sourceRange, $parent);
        $this->identifier = $identifier;
    }

    public function getNodeName(): string
    {
        return 'identifier';
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
        return $this;
    }
}