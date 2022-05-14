<?php

namespace Rizen\Core\Lexer;

use Rizen\Core\Helper\StringHelper;
use Rizen\Core\Input\FileInput;
use Rizen\Core\Input\InputInterface;
use Rizen\Core\Input\StringInput;
use Rizen\Core\Keyword\CaseInsensitiveKeywordDictionary;
use Rizen\Core\Lexer\Exception\InvalidFloatingPointNumberException;
use Rizen\Core\Lexer\Exception\InvalidInputException;
use Rizen\Core\Policy\FeaturePolicy;
use Rizen\Core\Source\SourcePosition;
use Rizen\Core\Source\SourceRange;
use Rizen\Core\Source\SourceString;
use Rizen\Core\Stream\MutableTokenStream;
use Rizen\Core\Stream\SourceCodeStream;
use Rizen\Core\Stream\TokenStream;
use Rizen\Core\Token\PunctuationType;
use Rizen\Core\Token\Token;
use Rizen\Core\Token\TokenType;

class GenericLexer implements LexerInterface
{
    public const FEATURE_INCLUDE_WHITESPACE = 'whitespace';
    public const FEATURE_INCLUDE_COMMENTS = 'comments';
    public const DEFAULT_FEATURE_POLICIES = [
        self::FEATURE_INCLUDE_COMMENTS => true,
        self::FEATURE_INCLUDE_WHITESPACE => true,
    ];

    private FeaturePolicy $featurePolicy;
    private CaseInsensitiveKeywordDictionary $keywordDictionary;
    private ?SourceCodeStream $code;
    private ?MutableTokenStream $output = null;
    private StringInput|FileInput $input;

    public function __construct(
        ?CaseInsensitiveKeywordDictionary $keywordDictionary = null,
        ?FeaturePolicy $featurePolicy = null
    ) {
        $this->keywordDictionary = $keywordDictionary ?: new CaseInsensitiveKeywordDictionary();
        $this->featurePolicy = $featurePolicy ?: new FeaturePolicy(self::DEFAULT_FEATURE_POLICIES);
    }

    public function lex(InputInterface $input): TokenStream
    {
        if (!$input instanceof FileInput && !$input instanceof StringInput) {
            throw new InvalidInputException();
        }
        return $this->lexImpl($input);
    }

    private function lexImpl(FileInput|StringInput $input): TokenStream
    {
        $this->input = $input;
        $this->output = new MutableTokenStream();
        $this->code = $input->getInputStream();

        while (!$this->code->eof()) {
            $char = $this->code->peek();
            if (StringHelper::isWhitespace($char)) {
                $this->consumeWhitespace();
            } elseif (StringHelper::isAlphabetical($char) || $char === '_') {
                $this->consumeIdentifier();
            } elseif (StringHelper::isNumeric($char)) {
                $this->consumeNumber();
            } elseif ($char === '/' && $this->code->peek(1) === '/') {
                $this->consumeComment();
            } elseif ($char === '/' && $this->code->peek(1) === '*') {
                $this->consumeLongComment();
            } elseif ($char === '"' || $char === '\'') {
                $this->consumeString();
            } elseif (StringHelper::isPunctuationMark($char)) {
                $this->consumePunctuationOrBinOp();
            } else {
                $this->consumeFallback();
            }
        }

        return clone $this->output;
    }

    private function consumeWhitespace(): void
    {
        $sourceString = $this->consumeWhile([StringHelper::class, 'isWhitespace']);

        if ($this->featurePolicy->isEnabled(self::FEATURE_INCLUDE_WHITESPACE)) {
            $this->emit($sourceString, TokenType::Whitespace);
        }
    }

    protected function consumeWhile(callable $predicate): SourceString
    {
        $start = clone $this->code->getPosition();
        $content = '';
        while (!$this->code->eof() && $predicate($this->code->peek())) {
            $content .= $this->code->next();
        }

        return $this->createSourceString($content, $start);
    }

    private function createSourceString(
        string $content,
        SourcePosition $startPosition,
        ?SourcePosition $endPosition = null
    ): SourceString {
        return new SourceString(
            new SourceRange($startPosition, is_null($endPosition) ? clone $this->code->getPosition() : $endPosition),
            $content,
            $this->input->getName()
        );
    }

    protected function emit(SourceString $sourceString, TokenType $type, ?PunctuationType $punctuationType = null): void
    {
        $this->output->push(
            new Token(
                $sourceString,
                $type,
                $punctuationType
            )
        );
    }

    private function consumeIdentifier(): void
    {
        $sourceString = $this->consumeWhile(fn(string $c) => $c === '_' || StringHelper::isAlphanumeric($c));
        $type = TokenType::Identifier;

        if ($this->keywordDictionary->isKeyword($sourceString->getContent())) {
            $type = TokenType::Keyword;
        }

        $this->emit($sourceString, $type);
    }

    private function consumeNumber(): void
    {
        $startPosition = clone $this->code->getPosition();
        $content = '';
        $type = TokenType::Integer;

        while (!$this->code->eof()) {
            $ch = $this->code->peek();

            if ($ch === '.') {
                if ($type === TokenType::Float) {
                    throw new InvalidFloatingPointNumberException(
                        'A floating point number can only contain one decimal point'
                    );
                }

                $type = TokenType::Float;
                $content .= $this->code->next();
            } elseif (StringHelper::isNumeric($ch)) {
                $content .= $this->code->next();
            } else {
                break;
            }
        }

        $endPosition = clone $this->code->getPosition();
        $this->emit(
            $this->createSourceString($content, $startPosition, $endPosition),
            $type
        );
    }

    private function consumeComment(): void
    {
        $sourceString = $this->consumeWhile(fn(string $c) => $c !== PHP_EOL);

        if ($this->featurePolicy->isEnabled(self::FEATURE_INCLUDE_COMMENTS)) {
            $this->emit($sourceString, TokenType::SimpleComment);
        }
    }

    private function consumeLongComment(): void
    {
        $sourceString = $this->consumeUntil(function (string $c) {
            return $c === '*' && $this->code->peek(1) === '/';
        });
        // Consume * and /
        $this->code->next();
        $this->code->next();
        $sourceString->setContent($sourceString->getContent() . '*/');

        if ($this->featurePolicy->isEnabled(self::FEATURE_INCLUDE_COMMENTS)) {
            $this->emit($sourceString, TokenType::MultiLineComment);
        }
    }

    private function consumeUntil(callable $predicate): SourceString
    {
        $start = clone $this->code->getPosition();
        $content = '';
        while (!$this->code->eof()) {
            $ch = $this->code->peek();
            if ($predicate($ch)) {
                break;
            }

            $content .= $this->code->next();
        }

        return $this->createSourceString($content, $start);
    }

    private function consumeString(): void
    {
        // Get the correct stating position and the delimiter we're expecting to end the string.
        $startPosition = $this->code->getPosition();
        $delimiter = $this->code->next(); // either ' or "

        $sourceString = $this->consumeWhile(fn(string $c) => $this->code->peek(-1) !== '\\' && $c !== $delimiter);
        // Also eat the last $delimiter
        $this->code->next();

        // Correctly adjust the start position to be the first str character
        $sourceString->setMetadata([
            'delimiter' => $delimiter,
        ]);
        $sourceString->getRange()->setStart($startPosition);
        $sourceString->setContent(StringHelper::escape($sourceString->getContent()));

        $this->emit($sourceString, TokenType::String);
    }

    private function consumePunctuationOrBinOp(): void
    {
        $startPosition = clone $this->code->getPosition();
        $character = $this->code->next();
        $endPosition = clone $this->code->getPosition();
        $punctuationType = PunctuationType::tryFrom($character);

        if ($punctuationType === null) {
            throw new \RuntimeException('Blame the space rays, this should never happen: ' . $character);
        }

        $this->emit(
            $this->createSourceString($character, $startPosition, $endPosition),
            TokenType::Punctuation,
            $punctuationType
        );
    }

    private function consumeFallback(): void
    {
        $startPosition = $this->code->getPosition();
        $char = $this->code->next();
        $this->emit(
            $this->createSourceString($char, $startPosition),
            TokenType::Unknown
        );
    }
}
