<?php

namespace Rizen\Core\Source;

class SourceString
{
    private SourceRange $range;
    private string $content;
    private array $metadata = [];
    private string $contextName;

    public function __construct(SourceRange $range, string $content, string $contextName)
    {
        $this->range = $range;
        $this->content = $content;
        $this->contextName = $contextName;
    }

    public function getRange(): SourceRange
    {
        return $this->range;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }
}
