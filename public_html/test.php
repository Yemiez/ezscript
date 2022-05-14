<?php

use Rizen\Core\Input\StringInput;
use Rizen\Core\Input\TokenInput;
use Rizen\Core\Parse\Parser;
use Rizen\RizenLexer;

include '../vendor/autoload.php';

$lexer = new RizenLexer();
$tokens = $lexer->lex(
    new StringInput(
    /** @lang rizen */ '
module Parse.Tests.First;
import * from System.Text;
import Vector<int> as IntVector from Standard.Containers;
export Array;

template<T>
class Array {

}
'
    )
);

$parser = new Parser();
$program = $parser->parse(new TokenInput($tokens, 'code'));
