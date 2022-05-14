<?php

namespace Rizen\Core\Keyword;

interface KeywordInterface
{
    public function isKeyword(string $word): bool;
}