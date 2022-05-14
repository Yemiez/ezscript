<?php

namespace Rizen\Core\Stream;

interface MutableStreamInterface extends StreamInterface
{
    public function push(mixed $item): self;

    public function pop(): mixed;
}