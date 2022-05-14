<?php

namespace Core\Lex\Source;

class SourcePosition
{
    public int $row;
    public int $column;

    public function __construct(int $row = 0, int $column = 0)
    {
        $this->row = $row;
        $this->column = $column;
    }

    public function increment(): static
    {
        return new self($this->row, $this->column + 1);
    }

    public function newLine(): static
    {
        return new self($this->row + 1, 0);
    }

    public function decrement(): static
    {
        return new self($this->row, $this->column - 1);
    }

    public function previousLine(int $column): static
    {
        return new self($this->row - 1, $column);
    }
}
