<?php

namespace Core\Lex\Stream;

use Core\Lex\Exception\LexException;
use Core\Lex\Token\TokenInterface;

class TokenStream implements StreamInterface
{
    protected array $tokens;
    protected int $cursor = 0;
    protected int $length = 0;

    public function __construct(array $tokens = [])
    {
        $this->tokens = $tokens;
        $this->length = count($tokens);
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getCursor(): int
    {
        return $this->cursor;
    }

    public function next(): TokenInterface
    {
        if ($this->eof()) {
            throw new LexException('Unexpected end of tokens');
        }
        return $this->tokens[$this->cursor++];
    }

    public function eof(): bool
    {
        return $this->cursor >= $this->length;
    }

    public function previous(): TokenInterface
    {
        if ($this->cursor === 0) {
            throw new LexException('Cannot get previous token (current position is the first token)');
        }

        return $this->tokens[$this->cursor--];
    }

    public function peek(int $ahead = 1): TokenInterface
    {
        if ($this->cursor + $ahead >= $this->length) {
            throw new LexException('End of file');
        }
        return $this->tokens[$this->cursor + $ahead];
    }

    public function rewind(): void
    {
        $this->length = 0;
    }
}
