<?php

namespace Rizen\Core\Input;

use Rizen\Core\Stream\SourceCodeStream;
use Rizen\Core\Stream\TokenStream;

class TokenInput implements InputInterface
{
    private TokenStream $stream;
    private string $name;

    public function __construct(TokenStream $stream, string $name)
    {
        $this->stream = $stream;
        $this->name = $name;
    }

    public function getInputStream(): TokenStream
    {
        return $this->stream;
    }

    public function getName(): string
    {
        return $this->name;
    }
}