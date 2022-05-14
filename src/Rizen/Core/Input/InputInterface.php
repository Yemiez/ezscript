<?php

namespace Rizen\Core\Input;

use Rizen\Core\Stream\StreamInterface;

interface InputInterface
{
    public function getInputStream(): StreamInterface;

    public function getName(): string;
}