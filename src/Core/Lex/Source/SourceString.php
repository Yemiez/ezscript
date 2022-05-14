<?php

namespace Core\Lex\Source;

class SourceString implements \JsonSerializable
{
    private SourceRange $range;
    private string $content;
    private array $metadata = [];

    public function __construct(SourceRange $range, string $content)
    {
        $this->range = $range;
        $this->content = $content;
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

    public function jsonSerialize(): array
    {
        return [
            'type' => self::class,
            'range' => $this->range,
            'content' => $this->content,
            'meta' =>  $this->metadata,
        ];
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param array $metadata
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }
}
