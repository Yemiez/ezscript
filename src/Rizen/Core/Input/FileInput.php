<?php

namespace Rizen\Core\Input;

use Rizen\Core\Input\Exception\FileNotFoundException;
use Rizen\Core\Stream\SourceCodeStream;
use Rizen\Core\Stream\StreamInterface;

class FileInput implements InputInterface
{
    private SourceCodeStream $stream;
    private string $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;

        if (!is_file($this->filename)) {
            throw new FileNotFoundException($this->filename);
        }

        $this->stream = new SourceCodeStream(file_get_contents($this->filename));
    }

    public function getInputStream(): SourceCodeStream
    {
        return $this->stream;
    }

    public function getName(): string
    {
        return $this->filename;
    }
}