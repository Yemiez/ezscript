<?php

namespace Rizen\Core\Node\Visitor;

use Rizen\Core\Node\NodeInterface;

interface MutableNodeVisitorInterface
{
    public function accepts(NodeInterface $node): bool;

    public function visit(NodeInterface $node): ?NodeInterface;
}