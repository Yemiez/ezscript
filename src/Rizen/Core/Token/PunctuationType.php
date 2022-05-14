<?php

namespace Rizen\Core\Token;

enum PunctuationType: string
{
    case EXCLAMATION_POINT = '!';
    case HASHTAG = '#';
    case DOLLAR_SIGN = '$';
    case PERCENTAGE_SIGN = '%';
    case AMPERSAND = '&';
    case OPENING_PARENTHESIS = '(';
    case CLOSING_PARENTHESIS = ')';
    case STAR = '*';
    case PLUS = '+';
    case MINUS = '-';
    CASE COMMA = ',';
    case DOT = '.';
    case RIGHT_SLASH = '/';
    case COLON = ':';
    case SEMI_COLON = ';';
    case LEFT_ARROW = '<';
    case EQUALS = '=';
    case RIGHT_ARROW = '>';
    case QUESTION_MARK = '?';
    case AT_SIGN = '@';
    case OPENING_BRACKET = '[';
    CASE CLOSING_BRACKET = ']';
    case UP_ARROW = '^';
    case OPENING_SQUIGGLY_BRACKET = '{';
    case CLOSING_SQUIGGLY_BRACKET = '}';
    case VERTICAL_BAR = '|';
    case TILDE = '~';
}
