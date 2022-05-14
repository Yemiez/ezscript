<?php

namespace Rizen\Core\Token;

use Rizen\Core\Source\SourceString;

class Token
{
    private SourceString $sourceString;
    private TokenType $tokenType;
    private ?PunctuationType $punctuationType;
    private ?OperatorType $operatorType;
    /** @var array<Token> $parents */
    private array $parents;

    public function __construct(
        SourceString $sourceString,
        TokenType $tokenType,
        ?PunctuationType $punctuationType = null,
        ?OperatorType $operatorType = null,
        array $parents = [],
    ) {
        $this->tokenType = $tokenType;
        $this->sourceString = $sourceString;
        $this->punctuationType = $punctuationType;
        $this->operatorType = $operatorType;
        $this->parents = $parents;
    }

    /**
     * @return SourceString
     */
    public function getSourceString(): SourceString
    {
        return $this->sourceString;
    }

    /**
     * @return TokenType
     */
    public function getTokenType(): TokenType
    {
        return $this->tokenType;
    }

    /**
     * @return PunctuationType|null
     */
    public function getPunctuationType(): ?PunctuationType
    {
        return $this->punctuationType;
    }

    /**
     * @return OperatorType|null
     */
    public function getOperatorType(): ?OperatorType
    {
        return $this->operatorType;
    }

    /**
     * @return Token[]
     */
    public function getParents(): array
    {
        return $this->parents;
    }

    public function isUnknown(): bool
    {
        return $this->isType(TokenType::Unknown);
    }

    public function isType(TokenType $type): bool
    {
        return $this->tokenType === $type;
    }

    public function isWhitespace(): bool
    {
        return $this->isType(TokenType::Whitespace);
    }

    public function isComment(): bool
    {
        return $this->isSimpleComment() || $this->isMultiLineComment();
    }

    public function isSimpleComment(): bool
    {
        return $this->isType(TokenType::SimpleComment);
    }

    public function isMultiLineComment(): bool
    {
        return $this->isType(TokenType::MultiLineComment);
    }

    public function isIdentifier(): bool
    {
        return $this->isType(TokenType::Identifier);
    }

    public function isKeyword(): bool
    {
        return $this->isType(TokenType::Keyword);
    }

    public function isString(): bool
    {
        return $this->isType(TokenType::String);
    }

    public function isNumber(): bool
    {
        return $this->isInteger() || $this->isFloat();
    }

    public function isInteger(): bool
    {
        return $this->isType(TokenType::String);
    }

    public function isFloat(): bool
    {
        return $this->isType(TokenType::Float);
    }

    public function isPunctuation(): bool
    {
        return $this->isType(TokenType::Punctuation);
    }

    public function isOperator(): bool
    {
        return $this->isType(TokenType::Operator);
    }

    public function __toString(): string
    {
        return sprintf(
            '%s@%d: %s (content="%s")',
            $this->sourceString->getContextName(),
            $this->sourceString->getRange()->getStart()->row,
            $this->tokenType->name,
            $this->sourceString->getContent()
        );
    }
}
