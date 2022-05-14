<?php

namespace Core\Lex\Stream;

use Core\Lex\Token\TokenInterface;
use JsonSerializable;

interface StreamInterface
{
    public function getLength(): int;

    public function getCursor(): int;

    public function next(): TokenInterface|JsonSerializable|string;

    public function previous(): TokenInterface|JsonSerializable|string;

    public function peek(int $ahead = 1): TokenInterface|JsonSerializable|string;

    public function rewind(): void;

    public function eof(): bool;
}
