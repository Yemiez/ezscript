<?php

namespace Rizen\Core\Node\Visitor;

use Rizen\Core\Node\NodeInterface;

interface NodeVisitorInterface
{
    public function accepts(NodeInterface $node): bool;

    public function visit(NodeInterface $node): void;
}