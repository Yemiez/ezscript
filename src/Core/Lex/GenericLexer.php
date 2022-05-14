<?php

namespace Core\Lex;

use Core\Lex\Exception\LexException;
use Core\Lex\Helper\StringHelper;
use Core\Lex\Keyword\KeywordDictionary;
use Core\Lex\Policy\FeaturePolicy;
use Core\Lex\Source\SourceContext;
use Core\Lex\Source\SourceRange;
use Core\Lex\Source\SourceString;
use Core\Lex\Stream\MutableTokenStream;
use Core\Lex\Stream\SourceCodeStream;
use Core\Lex\Stream\StreamInterface;
use Core\Lex\Token\PunctuationType;
use Core\Lex\Token\Token;
use Core\Lex\Token\TokenType;

class GenericLexer implements LexerInterface
{
    public const FEATURE_INCLUDE_WHITESPACE = 'whitespace';
    public const FEATURE_INCLUDE_COMMENTS = 'comments';
    public const DEFAULT_FEATURE_POLICIES = [
        self::FEATURE_INCLUDE_COMMENTS => true,
        self::FEATURE_INCLUDE_WHITESPACE => true,
    ];

    private static int $anonCounter = 0;

    private FeaturePolicy $featurePolicy;
    private KeywordDictionary $keywordDictionary;
    private ?SourceCodeStream $code;
    private ?MutableTokenStream $stream = null;
    private ?SourceContext $sourceContext = null;

    public function __construct(?KeywordDictionary $keywordDictionary = null, ?FeaturePolicy $featurePolicy = null)
    {
        $this->keywordDictionary = $keywordDictionary ?: new KeywordDictionary();
        $this->featurePolicy = $featurePolicy ?: new FeaturePolicy(self::DEFAULT_FEATURE_POLICIES);
    }

    public function lex(string $code, ?string $contextName = null): StreamInterface
    {
        $contextName = $contextName ?: '__anon@' . (++self::$anonCounter);
        $context = new SourceContext($contextName, new SourceCodeStream($code));
        return $this->genericImpl($context);
    }

    protected function genericImpl(SourceContext $context): StreamInterface
    {
        $this->sourceContext = $context;
        $this->stream = new MutableTokenStream();
        $this->code = $context->getCode();

        while (!$context->getCode()->eof()) {
            $char = $context->getCode()->peek();
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
                $this->consumePunctionationOrBinOp();
            } else {
                $this->consumeFallback();
            }
        }

        return clone $this->stream;
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

        return new SourceString(new SourceRange($start, clone $this->code->getPosition()), $content);
    }

    protected function emit(SourceString $sourceString, TokenType $type, ?PunctuationType $punctuationType = null): void
    {
        $this->stream->push(
            new Token(
                $sourceString,
                $this->sourceContext,
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
                    // TODO: source context, code line, column etc
                    throw new LexException('A floating point number can only contain one decimal point');
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
        $this->emit(new SourceString(new SourceRange($startPosition, $endPosition), $content), $type);
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

        return new SourceString(new SourceRange($start, clone $this->code->getPosition()), $content);
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

    private function consumePunctionationOrBinOp(): void
    {
        $startPosition = $this->code->getPosition();
        $character = $this->code->next();
        $endPosition = $this->code->getPosition();
        $punctuationType = PunctuationType::tryFrom($character);

        if ($punctuationType === null) {
            throw new \RuntimeException('Blame the rays from space, this should never happen');
        }

        $this->emit(
            new SourceString(new SourceRange($startPosition, $endPosition), $character),
            TokenType::Punctuation,
            $punctuationType
        );
    }

    private function consumeFallback(): void
    {
        $startPosition = $this->code->getPosition();
        $char = $this->code->next();
        $this->emit(
            new SourceString(new SourceRange($startPosition, clone $this->code->getPosition()), $char),
            TokenType::Unknown
        );
    }

    public function lexFile(string $filename): StreamInterface
    {
        if (!file_exists($filename)) {
            throw new LexException('Filename doesnt exist: ' . $filename);
        }
        $context = new SourceContext($filename, new SourceCodeStream(file_get_contents($filename)));
        return $this->genericImpl($context);
    }

}
