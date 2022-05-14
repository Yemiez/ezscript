<?php

namespace Rizen;

use Rizen\Core\Input\InputInterface;
use Rizen\Core\Input\TokenInput;
use Rizen\Core\Keyword\CaseInsensitiveKeywordDictionary;
use Rizen\Core\Lexer\GenericLexer;
use Rizen\Core\Lexer\LexerInterface;
use Rizen\Core\Lexer\PostProcessingLexer;
use Rizen\Core\Policy\FeaturePolicy;
use Rizen\Core\Stream\TokenStream;

class RizenLexer implements LexerInterface
{
    public const RIZEN_KEYWORDS = [
        'module',
        'import',
        'export',
        'from',
        'as',
        'class',
        'implements',
        'this',
        'array',
        'string',
        'pub',
        'mut',
        'fn',
        'void',
        'const',
        'new',
        'ret',
        'private',
        'public',
        'protected',
        'extends',
        'int',
        'float',
        'bool',
        'var',
        'null',
    ];

    private GenericLexer $genericLexer;
    private PostProcessingLexer $postProcessingLexer;

    public function __construct(?FeaturePolicy $featurePolicy = null)
    {
        $this->genericLexer = new GenericLexer(
            new CaseInsensitiveKeywordDictionary(self::RIZEN_KEYWORDS),
            $featurePolicy
        );
        $this->postProcessingLexer = new PostProcessingLexer();
    }

    public function lex(InputInterface $input): TokenStream
    {
        $tokens = $this->genericLexer->lex($input);
        return $this->postProcessingLexer->lex(new TokenInput($tokens, $input->getName()));
    }
}