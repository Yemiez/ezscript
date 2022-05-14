<?php

namespace Core\Lex\Stream;

use Core\Lex\Exception\LexException;
use Core\Lex\Source\SourcePosition;

class SourceCodeStream implements StreamInterface
{
    private string $code;
    private int $length;
    private int $cursor = 0;
    private SourcePosition $position;

    public function __construct(string $code)
    {
        $this->code = $code;
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
            throw new LexException('End of stream, cannot retrieve next character');
        }

        $ch = mb_substr($this->code, $this->cursor++, 1);

        if ($ch === PHP_EOL) {
            $this->position = $this->position->newLine();
        } else {
            $this->position = $this->position->increment();
        }

        return $ch;
    }

    public function eof(): bool
    {
        return $this->cursor >= $this->length;
    }

    public function previous(): string
    {
        if ($this->length === 0) {
            throw new LexException('Cannot retrieve previous token (it is already at the beginning)');
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
            try {
                $ch = $this->peek(--$counter);

                if ($ch === PHP_EOL) {
                    $pos = $pos->previousLine($counter + 1);
                    break;
                }
            } catch (LexException) {
                // First line
                return $pos->decrement();
            }
        }

        return $pos;
    }

    public function peek(int $ahead = 0): string
    {
        if ($this->cursor + $ahead >= $this->length) {
            throw new LexException('Cannot peek ahead over the end of the stream');
        }

        return mb_substr($this->code, $this->cursor + $ahead, 1);
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
