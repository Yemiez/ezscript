<?php

namespace Rizen\Core\Node;

use Rizen\Core\Source\SourceRange;

interface NodeInterface
{
    public function getNodeName(): string;

    public function isRoot(): bool;

    public function getParent(): ?Node;

    public function getSourceRange(): ?SourceRange;
}