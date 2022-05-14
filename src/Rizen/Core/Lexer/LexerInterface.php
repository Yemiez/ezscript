<?php

namespace Rizen\Core\Lexer;

use Rizen\Core\Input\InputInterface;
use Rizen\Core\Stream\TokenStream;

interface LexerInterface
{
    public function lex(InputInterface $input): TokenStream;
}