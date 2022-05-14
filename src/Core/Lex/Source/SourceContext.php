<?php

namespace Core\Lex\Source;

use Core\Lex\Stream\SourceCodeStream;

class SourceContext
{
    public string $filename = '';
    public SourceCodeStream $code;

    public function __construct(string $filename, SourceCodeStream $code)
    {
        $this->filename = $filename;
        $this->code = $code;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function isSame(SourceContext $other): bool
    {
        return $this->filename === $other->filename;
    }

    public function getCode(): SourceCodeStream
    {
        return $this->code;
    }
}
