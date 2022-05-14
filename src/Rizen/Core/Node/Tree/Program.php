<?php

namespace Rizen\Core\Node\Tree;

use Rizen\Core\Node\Node;
use Rizen\Core\Node\Tree\Expressions\Statements\ExportStatement;
use Rizen\Core\Node\Tree\Expressions\Statements\ImportStatement;
use Rizen\Core\Node\Tree\Expressions\Statements\ModuleStatement;

class Program extends Node
{
    private ?ModuleStatement $moduleStatement = null;
    /** @var array<ExportStatement> */
    private array $exportStatements = [];
    /** @var array<ImportStatement> */
    private array $importStatements = [];

    public function getNodeName(): string
    {
        return 'program';
    }

    public function getModuleStatement(): ?ModuleStatement
    {
        return $this->moduleStatement;
    }

    public function setModuleStatement(?ModuleStatement $moduleStatement): self
    {
        $this->moduleStatement = $moduleStatement;
        return $this;
    }

    public function emitExportStatement(ExportStatement $stmt): self
    {
        $this->exportStatements[] = $stmt;
        return $this;
    }

    /**
     * @return array<ExportStatement>
     */
    public function getExportStatements(): array
    {
        return $this->exportStatements;
    }

    public function emitImportStatement(ImportStatement $stmt): self
    {
        $this->importStatements[] = $stmt;
        return $this;
    }

    /**
     * @return ImportStatement[]
     */
    public function getImportStatements(): array
    {
        return $this->importStatements;
    }
}
