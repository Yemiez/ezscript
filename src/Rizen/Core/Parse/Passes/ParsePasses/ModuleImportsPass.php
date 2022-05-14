<?php

namespace Rizen\Core\Parse\Passes\ParsePasses;

use Rizen\Core\Input\TokenInput;
use Rizen\Core\Node\Tree\Program;
use Rizen\Core\Parse\Passes\NodeParsers\ImportStatementParser;
use Rizen\Core\Parse\Passes\ParsePassInterface;

class ModuleImportsPass implements ParsePassInterface
{
    private ImportStatementParser $importStatementParser;

    public function __construct()
    {
        $this->importStatementParser = new ImportStatementParser();
    }

    public function run(Program $program, TokenInput $input): void
    {
        while (!$input->getInputStream()->eof()) {
            $stmt = $this->importStatementParser->parse($program, $input->getInputStream());

            if (!$stmt) {
                break;
            }

            $program->emitImportStatement($stmt);
        }
    }
}
