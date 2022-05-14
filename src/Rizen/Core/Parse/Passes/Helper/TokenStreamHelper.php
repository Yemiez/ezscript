<?php

namespace Rizen\Core\Parse\Passes\Helper;

use Rizen\Core\Parse\Passes\Exception\UnexpectedEndOfFileException;
use Rizen\Core\Stream\TokenStream;

abstract class TokenStreamHelper
{
    public static function consumeNonMeaningfulTokens(TokenStream $stream)
    {
        while (!$stream->eof()) {
            $token = $stream->peek();

            if ($token->isComment() || $token->isWhitespace()) {
                $stream->next();
                continue;
            }

            break;
        }

        if ($stream->eof()) {
            throw new UnexpectedEndOfFileException();
        }
    }
}