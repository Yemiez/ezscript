<?php

namespace Rizen\Core\Node\Tree\Expressions;

class TemplateTypeCollection extends Expression
{
    /** @var array<NamedTypeExpression> */
    private array $types = [];

    public function getNodeName(): string
    {
        return 'templateTypeCollection';
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(array $types): TemplateTypeCollection
    {
        $this->types = $types;
        return $this;
    }

    public function emit(NamedTypeExpression $typeExpression): self
    {
        $this->types[] = $typeExpression;
        return $this;
    }
}