<?php

namespace Rizen\Core\Node\Tree\Expressions\Statements;

class ScopeStatement extends Statement
{
    private string $name = '';
    private array $statements = [];

    public function getNodeName(): string
    {
        return 'scope';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): ScopeStatement
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array<Statement>
     */
    public function getStatements(): array
    {
        return $this->statements;
    }

    public function emit(Statement $statement): self
    {
        $this->statements[] = $statement;
        return $this;
    }
}