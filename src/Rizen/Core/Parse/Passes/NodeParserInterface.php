<?php

namespace Rizen\Core\Parse\Passes;

use Rizen\Core\Node\Node;
use Rizen\Core\Stream\TokenStream;

interface NodeParserInterface
{
    public function parse(Node $parent, TokenStream $stream): ?Node;
}