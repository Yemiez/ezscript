<?php

namespace Core\Lex\Helper;

use Core\Lex\Token\PunctuationType;

abstract class StringHelper
{
    public const PUNCTUATION_MARKS = [
        '!',
        '#',
        '$',
        '%',
        '&',
        '(',
        ')',
        '*',
        '+',
        ',',
        '-',
        '.',
        '/',
        ':',
        ';',
        '<',
        '=',
        '>',
        '?',
        '@',
        '[',
        ']',
        '^',
        '_',
        '`',
        '{',
        '|',
        '}',
        '~',
    ];

    public static function isWhitespace(string $character): bool
    {
        return ctype_space($character);
    }

    public static function isAlphabetical(string $character): bool
    {
        return ctype_alpha($character);
    }

    public static function isAlphanumeric(string $character): bool
    {
        return ctype_alnum($character);
    }

    public static function isNumeric(string $character): bool
    {
        return ctype_digit($character);
    }

    public static function escape(string $text): string
    {
        $length = mb_strlen($text);
        $result = '';
        for ($i = 0; $i < $length; ++$i) {
            $c = mb_substr($text, $i, 1);
            if ($c === '\\') {
                if ($i + 1 >= $length) {
                    throw new \RuntimeException('Unfinished escape sequence found in string');
                }

                $result .= self::escapeChar($text[++$i]);
                continue;
            }
            $result .= $c;
        }

        return $result;
    }

    private static function escapeChar(string $ch): string
    {
        return match ($ch) {
            't' => "\t",
            'r' => "\r",
            'n' => "\n",
            'f' => "\f",
            '\'' => '\'',
            '"' => '"',
            '\\' => '\\',
            default => $ch,
        };
    }

    public static function isPunctuationMark(string $c): bool
    {
        return PunctuationType::tryFrom($c) !== null;
    }
}
