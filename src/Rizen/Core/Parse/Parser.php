<?php

namespace Rizen\Core\Parse;

use Rizen\Core\Input\TokenInput;
use Rizen\Core\Node\Tree\Program;
use Rizen\Core\Parse\Passes\ParsePasses\ModuleBodyPass;
use Rizen\Core\Parse\Passes\ParsePasses\ModuleExportsPass;
use Rizen\Core\Parse\Passes\ParsePasses\ModuleImportsPass;
use Rizen\Core\Parse\Passes\ParsePasses\ModuleStatementPass;
use Rizen\Core\Parse\Runner\ParsePassRunnerInOrder;

class Parser implements ParserInterface
{
    public function parse(TokenInput $input): ?Program
    {
        $program = new Program();
        try {
            $parsers = new ParsePassRunnerInOrder(
                new ModuleStatementPass(),
                new ModuleImportsPass(),
                new ModuleExportsPass(),
                new ModuleBodyPass()
            );

            $parsers->parseInto($program, $input);
            return $program;
        } catch (\Exception $e) {
            echo 'Exception: ' . get_class($e) . ' : ' . $e->getMessage();
            return null;
        }
    }
}
