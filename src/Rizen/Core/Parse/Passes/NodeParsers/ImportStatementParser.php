<?php

namespace Rizen\Core\Parse\Passes\NodeParsers;

use Rizen\Core\Node\Node;
use Rizen\Core\Node\Tree\Expressions\Statements\ImportStatement;
use Rizen\Core\Parse\Passes\Exception\UnexpectedTokenException;
use Rizen\Core\Parse\Passes\Helper\TokenStreamHelper;
use Rizen\Core\Parse\Passes\NodeParserInterface;
use Rizen\Core\Stream\TokenStream;

class ImportStatementParser implements NodeParserInterface
{
    private ModuleIdentifierParser $moduleIdentifierParser;
    private NamedTypeExpressionParser $typeExpressionParser;
    private NamedTypeExpressionParser $aliasExpressionParser;

    public function __construct()
    {
        $this->moduleIdentifierParser = new ModuleIdentifierParser();
        $this->typeExpressionParser = new NamedTypeExpressionParser(true);
        $this->aliasExpressionParser = new NamedTypeExpressionParser(false);
    }

    public function parse(Node $parent, TokenStream $stream): ?ImportStatement
    {
        TokenStreamHelper::consumeNonMeaningfulTokens($stream);
        $token = $stream->peek();

        if (!$token->isKeyword() || $token->getSourceString()->getContent() !== 'import') {
            return null;
        }
        $stream->next(); // consume import

        $importStatement = new ImportStatement($token->getSourceString()->getRange(), $parent);
        $importStatement->setType($this->typeExpressionParser->parse($importStatement, $stream));

        TokenStreamHelper::consumeNonMeaningfulTokens($stream);
        $token = $stream->peek();

        if ($token->isKeyword() && $token->getSourceString()->getContent() === 'as') {
            $stream->next();
            $importStatement->setAlias($this->aliasExpressionParser->parse($importStatement, $stream));
        }
        TokenStreamHelper::consumeNonMeaningfulTokens($stream);
        $token = $stream->peek();

        if (!$token->isKeyword() || $token->getSourceString()->getContent() !== 'from') {
            throw new UnexpectedTokenException($token);
        }
        $stream->next();
        TokenStreamHelper::consumeNonMeaningfulTokens($stream);

        $importStatement->setModuleIdentifier($this->moduleIdentifierParser->parse($importStatement, $stream));
        return $importStatement;
    }
}
