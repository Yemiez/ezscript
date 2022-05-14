<?php

namespace Rizen\Core\Parse\Passes\ParsePasses;

use Rizen\Core\Input\TokenInput;
use Rizen\Core\Node\Node;
use Rizen\Core\Node\Tree\Program;
use Rizen\Core\Parse\Passes\ParsePassInterface;

class ModuleBodyPass implements ParsePassInterface
{
    private Program $program;

    public function run(Node $workingNode, TokenInput $input): void
    {
        if ($workingNode instanceof Program) {
            $this->program = $workingNode;
            return;
        }
    }
}