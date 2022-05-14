<?php

namespace Rizen\Core\Node\Format;

use Rizen\Core\Node\NodeInterface;

interface NodeFormatterInterface
{
    public function format(NodeInterface $node): string;
}