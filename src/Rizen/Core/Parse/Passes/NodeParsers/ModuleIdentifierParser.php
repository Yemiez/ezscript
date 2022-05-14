<?php

namespace Rizen\Core\Parse\Passes\NodeParsers;

use Rizen\Core\Node\Node;
use Rizen\Core\Node\Tree\Expressions\IdentifierExpression;
use Rizen\Core\Node\Tree\Expressions\ModuleIdentifierExpression;
use Rizen\Core\Parse\Passes\Exception\ExpectedPunctuationException;
use Rizen\Core\Parse\Passes\Exception\UnexpectedEndOfFileException;
use Rizen\Core\Parse\Passes\Exception\UnexpectedTokenException;
use Rizen\Core\Parse\Passes\Helper\TokenStreamHelper;
use Rizen\Core\Parse\Passes\NodeParserInterface;
use Rizen\Core\Stream\TokenStream;

class ModuleIdentifierParser implements NodeParserInterface
{
    public function parse(Node $parent, TokenStream $stream): ?ModuleIdentifierExpression
    {
        /** @var array<IdentifierExpression> $chain */
        $chain = [];
        $state = 0;
        $moduleIdentifier = new ModuleIdentifierExpression(null, $parent);

        do {
            TokenStreamHelper::consumeNonMeaningfulTokens($stream);
            if ($stream->eof()) {
                throw new UnexpectedEndOfFileException();
            }

            $token = $stream->peek();

            if ($state == 0) {
                if (!$token->isIdentifier()) {
                    throw new ExpectedPunctuationException();
                }

                $chain[] = new IdentifierExpression(
                    $token->getSourceString()->getContent(),
                    $token->getSourceString()->getRange(),
                    $moduleIdentifier
                );
                $state = 1;
            } elseif ($state == 1) {
                if ($token->getSourceString()->getContent() === '.') {
                    $state = 0;
                } elseif ($token->getSourceString()->getContent() === ';') {
                    $state = 2;
                } else {
                    throw new UnexpectedTokenException($token);
                }
            }
            $stream->next();
        } while (!$stream->eof() && $state !== 2);

        $moduleIdentifier->setChain($chain);
        return $moduleIdentifier;
    }
}
