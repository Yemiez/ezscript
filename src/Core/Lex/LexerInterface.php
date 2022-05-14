<?php

namespace Core\Lex;

use Core\Lex\Stream\StreamInterface;

interface LexerInterface
{
    public function lex(string $code, ?string $contextName = null): StreamInterface;

    public function lexFile(string $filename): StreamInterface;
}
