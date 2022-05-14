<?php

namespace Core\Lex\Stream;

use Core\Lex\Exception\LexException;
use Core\Lex\Token\TokenInterface;

class MutableTokenStream extends TokenStream
{
    public function push(TokenInterface $token): self
    {
        $this->tokens[] = $token;
        ++$this->length;
        return $this;
    }

    public function pop(): self
    {
        if ($this->length === 0) {
            throw new LexException('Cannot pop token from stream, it is already empty');
        }

        array_pop($this->tokens);
        --$this->length;
        return $this;
    }
}
