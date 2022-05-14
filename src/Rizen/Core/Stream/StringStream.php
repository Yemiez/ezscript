<?php

namespace Rizen\Core\Stream;

use Rizen\Core\Stream\Exception\EndOfFileException;
use Rizen\Core\Stream\Exception\OutOfBoundsException;
use Rizen\Core\Stream\Traits\FastForwardTrait;

class StringStream implements StreamInterface
{
    use FastForwardTrait;

    private string $content;
    private int $length;
    private int $cursor;

    public function __construct(string $content)
    {
        $this->content = $content;
        $this->length = mb_strlen($this->content);
        $this->cursor = 0;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getCursor(): int
    {
        return $this->cursor;
    }

    public function next(): string
    {
        if ($this->eof($this->cursor + 1)) {
            // TODO: more info
            throw new EndOfFileException('eof in StringStream::next()');
        }

        return mb_substr($this->content, $this->cursor++, 1);
    }

    public function eof(int $offset = 0): bool
    {
        return !($this->cursor + $offset < $this->length);
    }

    public function previous(): string
    {
        if ($this->cursor === 0) {
            throw new OutOfBoundsException('out of bounds in StringStream::previous()');
        }
        return $this->at(--$this->cursor);
    }

    private function at(int $offset): string
    {
        return mb_substr($this->content, $offset, 1);
    }

    public function peek(int $offset = 0): ?string
    {
        if ($this->eof($offset)) {
            return null;
        }

        return $this->at($this->cursor + $offset);
    }

    public function rewind(): void
    {
        $this->cursor = 0;
    }

    public function __toString(): string
    {
        return $this->content;
    }
}