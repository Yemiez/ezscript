<?php

namespace Core\Stream;


namespace Rizen\Core\Stream;

interface StreamInterface
{
    public function getLength(): int;

    public function getCursor(): int;

    public function fastForward(int $steps): void;

    public function next(): mixed;

    public function previous(): mixed;

    public function peek(int $offset = 0): mixed;

    public function rewind(): void;

    public function eof(int $offset = 0): bool;
}