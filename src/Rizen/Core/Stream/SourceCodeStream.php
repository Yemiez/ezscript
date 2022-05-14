<?php

namespace Rizen\Core\Stream;

use Rizen\Core\Source\SourcePosition;
use Rizen\Core\Stream\Exception\EndOfFileException;
use Rizen\Core\Stream\Exception\OutOfBoundsException;
use Rizen\Core\Stream\Traits\FastForwardTrait;

class SourceCodeStream implements StreamInterface
{
    use FastForwardTrait;

    private string $code;
    private int $length;
    private int $cursor = 0;
    private SourcePosition $position;

    public function __construct(string|StringStream $code)
    {
        $this->code = $code instanceof StringStream ? (string)$code : $code;
        $this->length = mb_strlen($code);
        $this->position = new SourcePosition(0, 0);
    }

    public function getCode(): string
    {
        return $this->code;
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
        if ($this->eof()) {
            throw new EndOfFileException('End of file in SourceCodeStream::next()');
        }

        $ch = mb_substr($this->code, $this->cursor++, 1);

        if ($ch === PHP_EOL) {
            $this->position = $this->position->newLine();
        } else {
            $this->position = $this->position->increment();
        }

        return $ch;
    }

    public function eof(int $offset = 0): bool
    {
        return !($this->cursor + $offset < $this->length);
    }

    public function previous(): string
    {
        if ($this->length === 0) {
            throw new OutOfBoundsException('Out of bounds in SourceCodeStream::previous()');
        }

        $ch = mb_substr($this->code, $this->length - 1, 1);
        $this->position = $this->rewindPositionBackwards($ch);
        return $ch;
    }

    private function rewindPositionBackwards(string $ch): SourcePosition
    {
        if ($ch !== PHP_EOL) {
            return $this->position->decrement();
        }

        $pos = clone $this->position;
        $counter = 0;
        while (true) {
            $ch = $this->peek(--$counter);

            if ($ch === PHP_EOL) {
                $pos = $pos->previousLine($counter + 1);
                break;
            }

            if ($ch === null) {
                return new SourcePosition(0, 0); // First line
            }
        }

        return $pos;
    }

    public function peek(int $offset = 0): ?string
    {
        if ($this->eof($offset)) {
            return null;
        }

        return mb_substr($this->code, $this->cursor + $offset, 1);
    }

    public function rewind(): void
    {
        $this->cursor = 0;
        $this->position = new SourcePosition(0, 0);
    }

    public function getPosition(): SourcePosition
    {
        return $this->position;
    }
}
