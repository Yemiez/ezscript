<?php

namespace Rizen\Core\Parse\Passes;

use Rizen\Core\Input\TokenInput;
use Rizen\Core\Node\Tree\Program;

interface ParsePassInterface
{
    public function run(Program $program, TokenInput $input): void;
}