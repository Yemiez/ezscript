<?php

namespace Rizen\Core\Parse\Passes\NodeParsers;

use Rizen\Core\Node\Node;
use Rizen\Core\Node\Tree\Expressions\NamedTypeExpression;
use Rizen\Core\Node\Tree\Expressions\TemplatedNamedTypeExpression;
use Rizen\Core\Node\Tree\Expressions\TemplateTypeCollection;
use Rizen\Core\Parse\Passes\Exception\UnexpectedTemplatedTypeNameException;
use Rizen\Core\Parse\Passes\Exception\UnexpectedTokenException;
use Rizen\Core\Parse\Passes\Helper\TokenStreamHelper;
use Rizen\Core\Parse\Passes\NodeParserInterface;
use Rizen\Core\Stream\TokenStream;
use Rizen\Core\Token\Token;

class NamedTypeExpressionParser implements NodeParserInterface
{
    private bool $allowTemplates;

    public function __construct(bool $allowTemplates = false)
    {
        $this->allowTemplates = $allowTemplates;
    }

    public function parse(Node $parent, TokenStream $stream): ?NamedTypeExpression
    {
        TokenStreamHelper::consumeNonMeaningfulTokens($stream);
        if ($stream->eof()) {
            return null;
        }

        $token = $stream->peek();

        if ($token->getSourceString()->getContent() === '*') {
            $stream->next(); // consume
            return (new NamedTypeExpression($token->getSourceString()->getRange(), $parent))->setTypeName('*');
        }

        if (!($token->isKeyword() || $token->isIdentifier())) {
            return null;
        }
        $stream->next();
        TokenStreamHelper::consumeNonMeaningfulTokens($stream);

        $next = $stream->peek();
        if ($next && $next->isOperator() && $next->getSourceString()->getContent() === '<') {
            $stream->next();
            return $this->parseWithTemplate($token, $parent, $stream);
        }

        return (new NamedTypeExpression($token->getSourceString()->getRange(), $parent))
            ->setTypeName($token->getSourceString()->getContent());
    }

    private function parseWithTemplate(
        Token $typeNameToken,
        Node $parent,
        TokenStream $stream
    ): TemplatedNamedTypeExpression {
        if (!$this->allowTemplates) {
            throw new UnexpectedTemplatedTypeNameException();
        }

        // 0 = expecting named type expression
        // 1 = expecting > or comma
        // 2 = done
        $state = 0;
        $templatedTypeExpression = new TemplatedNamedTypeExpression(null, $parent);
        $collection = new TemplateTypeCollection(null, $templatedTypeExpression);
        $parser = new self(true);

        do {
            TokenStreamHelper::consumeNonMeaningfulTokens($stream);

            if ($state === 0) {
                $namedType = $parser->parse($collection, $stream);

                if ($namedType === null) {
                    throw new UnexpectedTokenException($stream->peek());
                }
                $collection->emit($namedType);
                $state = 1;
            } elseif ($state === 1) {
                $token = $stream->next();

                if ($token->getSourceString()->getContent() === ',') {
                    $state = 0;
                } elseif ($token->getSourceString()->getContent() === '>') {
                    $state = 2;
                } else {
                    throw new UnexpectedTokenException($token);
                }
            } else {
                throw new UnexpectedTokenException($stream->peek());
            }
        } while (!$stream->eof() && $state !== 2);

        $templatedTypeExpression->setTypeName($typeNameToken->getSourceString()->getContent());
        $templatedTypeExpression->setTemplateTypeCollection($collection);
        return $templatedTypeExpression;
    }
}
