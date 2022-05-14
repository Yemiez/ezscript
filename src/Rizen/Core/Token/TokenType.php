<?php

namespace Rizen\Core\Token;

enum TokenType: string
{
    case Unknown = 'unknown';
    case Whitespace = 'whitespace';
    case SimpleComment = 'simplecomment';
    case MultiLineComment = 'multilinecomment';
    case Identifier = 'identifier';
    case Keyword = 'keyword';
    case String = 'string';
    case Integer = 'integer';
    case Float = 'float';

    // Operators
    case Punctuation = 'punctuation';
    case Operator = 'operator';

    public function getHtmlClass(): string
    {
        return match ($this) {
            self::Unknown => 'token--unknown',
            self::Whitespace => 'token--ws',
            self::SimpleComment => 'token--comment__simple',
            self::MultiLineComment => 'token--comment__long',
            self::Identifier => 'token--identifier',
            self::Keyword => 'token--keyword',
            self::String => 'token--string',
            self::Integer => 'token--integer',
            self::Float => 'token--float',
            self::Punctuation => 'token--punctuation',
            self::Operator => 'token--operator',
        };
    }
}
