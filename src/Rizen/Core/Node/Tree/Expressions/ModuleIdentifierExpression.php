<?php

namespace Rizen\Core\Node\Tree\Expressions;

use Rizen\Core\Node\Node;
use Rizen\Core\Source\SourceRange;

class ModuleIdentifierExpression extends Expression
{
    /** @var array<IdentifierExpression> */
    private array $chain = [];

    public function getNodeName(): string
    {
        return 'moduleIdentifier';
    }

    public function getFullyQualifiedName(): string
    {
        return implode('.', array_map(fn(IdentifierExpression $e) => $e->getIdentifier(), $this->chain));
    }

    /**
     * @return IdentifierExpression[]
     */
    public function getChain(): array
    {
        return $this->chain;
    }

    /**
     * @param IdentifierExpression[] $chain
     */
    public function setChain(array $chain): self
    {
        $this->chain = $chain;
        return $this;
    }
}