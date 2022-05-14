<?php

namespace Rizen\Core\Parse\Runner;

use Rizen\Core\Input\TokenInput;
use Rizen\Core\Node\Tree\Program;

interface ParsePassRunnerInterface
{
    public function parseInto(Program $program, TokenInput $input): void;
}