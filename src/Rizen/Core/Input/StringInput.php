<?php

namespace Rizen\Core\Input;

use Rizen\Core\Stream\SourceCodeStream;

class StringInput implements InputInterface
{
    private static int $counter = 0;

    private SourceCodeStream $stream;
    private string $name;

    public function __construct(string $content, ?string $name = null)
    {
        $this->stream = new SourceCodeStream($content);
        $this->name = $name ?: 'anon@' . ++self::$counter;
    }

    public function getInputStream(): SourceCodeStream
    {
        return $this->stream;
    }

    public function getName(): string
    {
        return $this->name;
    }
}