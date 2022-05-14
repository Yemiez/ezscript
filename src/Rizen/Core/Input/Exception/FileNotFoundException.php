<?php

namespace Rizen\Core\Input\Exception;

use Throwable;

class FileNotFoundException extends InputException
{
    private string $filename;

    public function __construct(string $filename, $message = 'File not found: ', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message . $filename, $code, $previous);
        $this->filename = $filename;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}