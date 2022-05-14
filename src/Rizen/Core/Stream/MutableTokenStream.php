<?php

namespace Rizen\Core\Stream;

use Rizen\Core\Stream\Exception\InvalidItemException;
use Rizen\Core\Stream\Exception\OutOfBoundsException;
use Rizen\Core\Token\Token;

class MutableTokenStream extends TokenStream implements MutableStreamInterface
{
    public function push(mixed $item): self
    {
        if (!$item instanceof Token) {
            throw new InvalidItemException('Cannot push a non Token item to a MutableTokenStream');
        }

        $this->tokens[] = $item;
        ++$this->length;
        return $this;
    }

    public function pop(): self
    {
        if ($this->length === 0) {
            throw new OutOfBoundsException('Cannot pop token from stream, it is already empty');
        }

        array_pop($this->tokens);
        --$this->length;
        return $this;
    }
}
