<?php

namespace Rizen\Core\Parse\Passes\Exception;

use Rizen\Core\Parse\Exception\ParseException;
use Rizen\Core\Token\Token;
use Throwable;

class UnexpectedTokenException extends ParseException
{
    public function __construct(Token $token, $message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Unexpected token: ' . (string)$token . ' expected ' . $message, $code, $previous);
    }
}
