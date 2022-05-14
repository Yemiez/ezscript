<?php

namespace Rizen\Core\Stream;

use Rizen\Core\Stream\Exception\EndOfFileException;
use Rizen\Core\Stream\Exception\OutOfBoundsException;
use Rizen\Core\Stream\Traits\FastForwardTrait;
use Rizen\Core\Token\Token;

class TokenStream implements StreamInterface
{
    use FastForwardTrait;

    /** @var array<Token> $tokens */
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

    public function next(): Token
    {
        if ($this->eof()) {
            throw new EndOfFileException('eof in TokenStream::next()');
        }
        return $this->tokens[$this->cursor++];
    }

    public function eof(int $offset = 0): bool
    {
        return !($this->cursor + $offset < $this->length);
    }

    public function previous(): Token
    {
        if ($this->cursor === 0) {
            throw new OutOfBoundsException('out of bounds in TokenStream::previous()');
        }

        return $this->tokens[$this->cursor--];
    }

    public function peek(int $offset = 0): ?Token
    {
        if ($this->eof($offset)) {
            return null;
        }
        return $this->tokens[$this->cursor + $offset];
    }

    public function rewind(): void
    {
        $this->length = 0;
    }
}
