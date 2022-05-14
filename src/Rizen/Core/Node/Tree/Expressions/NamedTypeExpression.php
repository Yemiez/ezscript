<?php

namespace Rizen\Core\Node\Tree\Expressions;

class NamedTypeExpression extends Expression
{
    private string $typeName = '';

    public function getNodeName(): string
    {
        return 'namedType';
    }

    public function getTypeName(): string
    {
        return $this->typeName;
    }

    public function setTypeName(string $typeName): self
    {
        $this->typeName = $typeName;
        return $this;
    }
}