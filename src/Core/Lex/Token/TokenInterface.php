<?php

namespace Core\Lex\Token;

use Core\Lex\Source\SourceContext;
use Core\Lex\Source\SourceString;

interface TokenInterface extends \JsonSerializable
{
    public function getSourceString(): SourceString;

    public function getSourceContext(): SourceContext;

    public function getTokenType(): TokenType;
}
