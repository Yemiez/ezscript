<?php

namespace Rizen\Core\Parse\Passes\NodeParsers;

use Rizen\Core\Node\Node;
use Rizen\Core\Node\Tree\Expressions\Statements\ExportStatement;
use Rizen\Core\Parse\Passes\Exception\UnexpectedTokenException;
use Rizen\Core\Parse\Passes\Helper\TokenStreamHelper;
use Rizen\Core\Parse\Passes\NodeParserInterface;
use Rizen\Core\Stream\TokenStream;

class ExportStatementParser implements NodeParserInterface
{
    private NamedTypeExpressionParser $typeExpressionParser;
    private NamedTypeExpressionParser $aliasExpressionParser;

    public function __construct()
    {
        $this->typeExpressionParser = new NamedTypeExpressionParser(true);
        $this->aliasExpressionParser = new NamedTypeExpressionParser(false);
    }

    public function parse(Node $parent, TokenStream $stream): ?ExportStatement
    {
        TokenStreamHelper::consumeNonMeaningfulTokens($stream);
        if ($stream->eof()) {
            return null;
        }
        if (!$stream->peek()->isKeyword() && $stream->peek()->getSourceString()->getContent() !== 'export') {
            return null;
        }
        $stream->next();

        $statement = new ExportStatement();
        $statement->setType($this->typeExpressionParser->parse($statement, $stream));

        TokenStreamHelper::consumeNonMeaningfulTokens($stream);
        $token = $stream->peek();

        if ($token->isKeyword() && $token->getSourceString()->getContent() === 'as') {
            $stream->next(); // consume as
            $statement->setAlias($this->aliasExpressionParser->parse($statement, $stream));
        }

        TokenStreamHelper::consumeNonMeaningfulTokens($stream);
        $token = $stream->peek();

        // now we're expecting a semi-colon
        if ($token->getSourceString()->getContent() !== ';') {
            throw new UnexpectedTokenException($token, ';');
        }

        $stream->next(); // consume ;
        return $statement;
    }
}
