<?php

namespace Rizen\Core\Parse;

use Rizen\Core\Input\TokenInput;
use Rizen\Core\Node\Tree\Program;

interface ParserInterface
{
    public function parse(TokenInput $input): ?Program;
}