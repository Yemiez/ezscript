<?php

namespace Rizen\Core\Lexer;

use Rizen\Core\Input\InputInterface;
use Rizen\Core\Input\TokenInput;
use Rizen\Core\Lexer\Exception\InvalidInputException;
use Rizen\Core\Source\SourceRange;
use Rizen\Core\Source\SourceString;
use Rizen\Core\Stream\MutableTokenStream;
use Rizen\Core\Stream\TokenStream;
use Rizen\Core\Token\OperatorType;
use Rizen\Core\Token\PunctuationType;
use Rizen\Core\Token\Token;
use Rizen\Core\Token\TokenType;

class PostProcessingLexer implements LexerInterface
{
    private MutableTokenStream $output;
    private TokenStream $stream;

    public function lex(InputInterface $input): TokenStream
    {
        if (!$input instanceof TokenInput) {
            throw new InvalidInputException();
        }

        return $this->lexImpl($input);
    }

    private function lexImpl(TokenInput $input): TokenStream
    {
        $this->output = new MutableTokenStream();
        $this->stream = $input->getInputStream();

        while (!$this->stream->eof()) {
            $token = $this->stream->peek();

            if ($token->isPunctuation()) {
                $this->lexPunctuation();
                continue;
            }

            $this->emitNext();
        }

        return $this->output;
    }

    private function lexPunctuation(): void
    {
        $token = $this->stream->peek();
        $peekOne = $this->stream->peek(1);
        $peekTwo = $this->stream->peek(2);

        $isPunct = function (?Token $token, PunctuationType $type): bool {
            return $token && $token->isPunctuation() && $token->getPunctuationType() === $type;
        };

        $peekOneIsPunct = function (PunctuationType $type) use ($peekOne, $isPunct) {
            return $isPunct($peekOne, $type);
        };
        $peekTwoIsPunct = function (PunctuationType $type) use ($peekTwo, $isPunct) {
            return $isPunct($peekTwo, $type);
        };

        switch ($token->getPunctuationType()) {
            case PunctuationType::CLOSING_SQUIGGLY_BRACKET:
            case PunctuationType::OPENING_SQUIGGLY_BRACKET:
            case PunctuationType::CLOSING_BRACKET:
            case PunctuationType::OPENING_BRACKET:
            case PunctuationType::AT_SIGN:
            case PunctuationType::QUESTION_MARK:
            case PunctuationType::SEMI_COLON:
            case PunctuationType::COMMA:
            case PunctuationType::CLOSING_PARENTHESIS:
            case PunctuationType::OPENING_PARENTHESIS:
            case PunctuationType::DOLLAR_SIGN:
            case PunctuationType::HASHTAG:
                $this->emitNext();
                break;
            case PunctuationType::EXCLAMATION_POINT:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::NotEquals,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::LogicalNot,
                        [$token]
                    );
                }
                break;
            case PunctuationType::RIGHT_SLASH:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::CompoundAssignmentRemainder,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::Division,
                        [$token]
                    );
                }
                break;
            case PunctuationType::PERCENTAGE_SIGN:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::CompoundAssignmentRemainder,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::Remainder,
                        [$token]
                    );
                }
                break;
            case PunctuationType::AMPERSAND:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::CompoundAssignmentBitwiseAnd,
                        [$token, $peekOne]
                    );
                } elseif ($peekOneIsPunct(PunctuationType::AMPERSAND)) {
                    $this->emitOperator(
                        OperatorType::LogicalAnd,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::BitwiseAnd,
                        [$token]
                    );
                }
                break;
            case PunctuationType::STAR:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::CompoundAssignmentProduct,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::Multiplication,
                        [$token]
                    );
                }
                break;
            case PunctuationType::PLUS:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::CompoundAssignmentSum,
                        [$token, $peekOne]
                    );
                } elseif ($peekOneIsPunct(PunctuationType::PLUS)) {
                    $this->emitOperator(
                        OperatorType::PostOrSuffixIncrement,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::UnaryPlus,
                        [$token]
                    );
                }
                break;
            case PunctuationType::MINUS:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::CompoundAssignmentDifference,
                        [$token, $peekOne]
                    );
                } elseif ($peekOneIsPunct(PunctuationType::MINUS)) {
                    $this->emitOperator(
                        OperatorType::PostOrSuffixDecrement,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::UnaryMinus,
                        [$token]
                    );
                }
                break;
            case PunctuationType::DOT:
                if ($peekOneIsPunct(PunctuationType::DOT) && $peekTwoIsPunct(PunctuationType::DOT)) {
                    $this->emitOperator(
                        OperatorType::DotDotDot,
                        [$token, $peekOne, $peekTwo]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::MemberAccess,
                        [$token]
                    );
                }
                break;
            case PunctuationType::COLON:
                if ($peekOneIsPunct(PunctuationType::COLON)) {
                    $this->emitOperator(
                        OperatorType::ScopeResolution,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitNext(); // Colon is not an operator in this case.
                }
                break;
            case PunctuationType::LEFT_ARROW:
                if ($peekOneIsPunct(PunctuationType::EQUALS) && $peekTwoIsPunct(PunctuationType::RIGHT_ARROW)) {
                    $this->emitOperator(
                        OperatorType::ThreeWayComparisonOperator,
                        [$token, $peekOne, $peekTwo]
                    );
                } elseif ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::LessThanEqual,
                        [$token, $peekOne]
                    );
                } elseif ($peekOneIsPunct(PunctuationType::LEFT_ARROW) && $peekTwoIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::CompoundAssignmentBitwiseLeftShift,
                        [$token, $peekOne, $peekTwo]
                    );
                } elseif ($peekOneIsPunct(PunctuationType::LEFT_ARROW)) {
                    $this->emitOperator(
                        OperatorType::BitwiseLeftShift,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::LessThan,
                        [$token]
                    );
                }
                break;
            case PunctuationType::EQUALS:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::Equals,
                        [$token, $peekOne]
                    );
                } elseif ($peekOneIsPunct(PunctuationType::RIGHT_ARROW)) {
                    $this->emitOperator(
                        OperatorType::ArrowFunction,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::Assignment,
                        [$token]
                    );
                }
                break;
            case PunctuationType::RIGHT_ARROW:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::GreaterThanEqual,
                        [$token, $peekOne]
                    );
                } elseif ($peekOneIsPunct(PunctuationType::RIGHT_ARROW) && $peekTwoIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::CompoundAssignmentBitwiseRightShift,
                        [$token, $peekOne, $peekTwo]
                    );
                } elseif ($peekOneIsPunct(PunctuationType::RIGHT_ARROW)) {
                    $this->emitOperator(
                        OperatorType::BitwiseRightShift,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::LessThan,
                        [$token]
                    );
                }
                break;
            case PunctuationType::UP_ARROW:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::CompoundAssignmentBitwiseXor,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::BitwiseXor,
                        [$token]
                    );
                }
                break;
            case PunctuationType::VERTICAL_BAR:
                if ($peekOneIsPunct(PunctuationType::EQUALS)) {
                    $this->emitOperator(
                        OperatorType::CompoundAssignmentBitwiseOr,
                        [$token, $peekOne]
                    );
                } elseif ($peekOneIsPunct(PunctuationType::VERTICAL_BAR)) {
                    $this->emitOperator(
                        OperatorType::LogicalOr,
                        [$token, $peekOne]
                    );
                } else {
                    $this->emitOperator(
                        OperatorType::BitwiseOr,
                        [$token]
                    );
                }
                break;
            case PunctuationType::TILDE:
                throw new \Exception('To be implemented');
        }
    }

    private function emitNext(): void
    {
        $next = $this->stream->next();
        $token = new Token(
            clone $next->getSourceString(),
            $next->getTokenType(),
            $next->getPunctuationType(),
            $next->getOperatorType(),
            [$next]
        );

        $this->output->push($token);
    }

    /**
     * @param OperatorType $operatorType
     * @param array<Token> $parents
     */
    private function emitOperator(OperatorType $operatorType, array $parents): void
    {
        $content = '';
        $range = new SourceRange(
            $parents[0]->getSourceString()->getRange()->getStart(),
            end($parents)->getSourceString()->getRange()->getEnd()
        );
        $name = $parents[0]->getSourceString()->getContextName();
        $metadata = [];

        foreach ($parents as $parent) {
            $content .= $parent->getSourceString()->getContent();
            $metadata = array_merge($metadata, $parent->getSourceString()->getMetadata());
        }

        $this->stream->fastForward(count($parents));
        $this->output->push(
            new Token(
                new SourceString($range, $content, $name),
                TokenType::Operator,
                null,
                $operatorType,
                $parents
            )
        );
    }
}