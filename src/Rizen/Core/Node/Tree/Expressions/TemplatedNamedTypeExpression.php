<?php

namespace Rizen\Core\Node\Tree\Expressions;

class TemplatedNamedTypeExpression extends NamedTypeExpression
{
    private ?TemplateTypeCollection $templateTypeCollection = null;

    public function getNodeName(): string
    {
        return 'namedType';
    }

    public function getTemplateTypeCollection(): ?TemplateTypeCollection
    {
        return $this->templateTypeCollection;
    }

    public function setTemplateTypeCollection(?TemplateTypeCollection $templateTypeCollection): self
    {
        $this->templateTypeCollection = $templateTypeCollection;
        return $this;
    }
}