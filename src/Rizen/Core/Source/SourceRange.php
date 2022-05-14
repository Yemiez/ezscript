<?php

namespace Rizen\Core\Source;

class SourceRange implements \Stringable
{
    private SourcePosition $start;
    private SourcePosition $end;

    public function __construct(SourcePosition $start, SourcePosition $end)
    {
        $this->start = $start;
        $this->end = $end;
    }

    public function __toString(): string
    {
        return sprintf(
            '%d:%d-%d-%d',
            $this->start->row + 1,
            $this->start->column + 1,
            $this->end->row + 1,
            $this->end->column + 1
        );
    }

    /**
     * @return SourcePosition
     */
    public function getStart(): SourcePosition
    {
        return $this->start;
    }

    /**
     * @param SourcePosition $start
     */
    public function setStart(SourcePosition $start): void
    {
        $this->start = $start;
    }

    /**
     * @return SourcePosition
     */
    public function getEnd(): SourcePosition
    {
        return $this->end;
    }

    /**
     * @param SourcePosition $end
     */
    public function setEnd(SourcePosition $end): void
    {
        $this->end = $end;
    }
}
