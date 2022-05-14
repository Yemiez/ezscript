<?php

namespace Rizen\Core\Parse\Passes\ParsePasses;

use Rizen\Core\Input\TokenInput;
use Rizen\Core\Node\Tree\Program;
use Rizen\Core\Parse\Passes\NodeParsers\ModuleStatementParser;
use Rizen\Core\Parse\Passes\ParsePassInterface;

class ModuleStatementPass implements ParsePassInterface
{
    public function run(Program $program, TokenInput $input): void
    {
        $moduleParser = new ModuleStatementParser();
        $module = $moduleParser->parse($program, $input->getInputStream());
        $program->setModuleStatement($module);
    }
}