<?php

namespace Rizen\Core\Parse\Passes\NodeParsers;

use Rizen\Core\Node\Node;
use Rizen\Core\Node\Tree\Expressions\Statements\ModuleStatement;
use Rizen\Core\Parse\Passes\Exception\UnexpectedEndOfFileException;
use Rizen\Core\Parse\Passes\Helper\TokenStreamHelper;
use Rizen\Core\Parse\Passes\NodeParserInterface;
use Rizen\Core\Stream\TokenStream;

class ModuleStatementParser implements NodeParserInterface
{
    private ModuleIdentifierParser $moduleIdentifierParser;

    public function __construct()
    {
        $this->moduleIdentifierParser = new ModuleIdentifierParser();
    }

    public function parse(Node $parent, TokenStream $stream): ?ModuleStatement
    {
        TokenStreamHelper::consumeNonMeaningfulTokens($stream);
        if ($stream->eof()) {
            throw new UnexpectedEndOfFileException();
        }
        $moduleKeyword = $stream->peek();

        if (!($moduleKeyword->isKeyword() && $moduleKeyword->getSourceString()->getContent() === 'module')) {
            return null;
        }
        $stream->next(); // consume module

        $module = new ModuleStatement();
        $module->setModuleIdentifier($this->moduleIdentifierParser->parse($module, $stream));
        return $module;
    }
}