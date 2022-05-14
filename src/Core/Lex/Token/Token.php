<?php

namespace Core\Lex\Token;

use Core\Lex\Source\SourceContext;
use Core\Lex\Source\SourceString;

class Token implements TokenInterface
{
    private SourceString $sourceString;
    private SourceContext $sourceContext;
    private TokenType $tokenType;
    private ?PunctuationType $punctuationType;

    /**
     * @param SourceString         $sourceString
     * @param SourceContext        $sourceContext
     * @param TokenType            $tokenType
     * @param PunctuationType|null $punctuationType
     */
    public function __construct(
        SourceString $sourceString,
        SourceContext $sourceContext,
        TokenType $tokenType,
        ?PunctuationType $punctuationType
    ) {
        $this->sourceContext = $sourceContext;
        $this->tokenType = $tokenType;
        $this->sourceString = $sourceString;
        $this->punctuationType = $punctuationType;
    }

    /**
     * @return SourceString
     */
    public function getSourceString(): SourceString
    {
        return $this->sourceString;
    }

    /**
     * @return SourceContext
     */
    public function getSourceContext(): SourceContext
    {
        return $this->sourceContext;
    }

    /**
     * @return TokenType
     */
    public function getTokenType(): TokenType
    {
        return $this->tokenType;
    }

    public function jsonSerialize(): array
    {
        return [
            'tokenType' => $this->tokenType->name,
            'punctuationType' => $this->punctuationType?->name ?? null,
            'source' => [
                'content' => $this->sourceString,
                'range' => $this->sourceString->getRange(),
                'context' => $this->sourceContext->getFilename(),
            ],
        ];
    }
}
