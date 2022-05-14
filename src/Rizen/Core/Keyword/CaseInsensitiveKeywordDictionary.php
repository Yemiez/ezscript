<?php

namespace Rizen\Core\Keyword;

class CaseInsensitiveKeywordDictionary implements KeywordInterface
{
    private array $keywords;

    public function __construct(array $keywords = [])
    {
        $this->keywords = $keywords;
    }

    public function getKeywords(): array
    {
        return $this->keywords;
    }

    public function isKeyword(string $word): bool
    {
        foreach ($this->keywords as $keyword) {
            if (strcasecmp($keyword, $word) === 0) {
                return true;
            }
        }

        return false;
    }
}
