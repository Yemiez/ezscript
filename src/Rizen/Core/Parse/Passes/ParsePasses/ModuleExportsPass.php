<?php

namespace Rizen\Core\Parse\Passes\ParsePasses;

use Rizen\Core\Input\TokenInput;
use Rizen\Core\Node\Tree\Program;
use Rizen\Core\Parse\Passes\NodeParsers\ExportStatementParser;
use Rizen\Core\Parse\Passes\ParsePassInterface;

class ModuleExportsPass implements ParsePassInterface
{
    private ExportStatementParser $exportStatementParser;

    public function __construct()
    {
        $this->exportStatementParser = new ExportStatementParser();
    }

    public function run(Program $program, TokenInput $input): void
    {
        while (!$input->getInputStream()->eof()) {
            $stmt = $this->exportStatementParser->parse($program, $input->getInputStream());

            if ($stmt === null) {
                break;
            }

            $program->emitExportStatement($stmt);
        }
    }
}