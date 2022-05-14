<?php

namespace Rizen\Core\Node;

use Rizen\Core\Source\SourceRange;

abstract class Node implements NodeInterface
{
    protected ?SourceRange $sourceRange;
    protected ?Node $parent;

    public function __construct(?SourceRange $sourceRange = null, ?Node $parent = null)
    {
        $this->sourceRange = $sourceRange;
        $this->parent = $parent;
    }

    public function isRoot(): bool
    {
        return $this->parent === null;
    }

    public function getSourceRange(): ?SourceRange
    {
        return $this->sourceRange;
    }

    public function getParent(): ?Node
    {
        return $this->parent;
    }
}