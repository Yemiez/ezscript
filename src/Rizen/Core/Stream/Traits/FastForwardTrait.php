<?php

namespace Rizen\Core\Stream\Traits;

trait FastForwardTrait
{
    public function fastForward(int $steps): void
    {
        for ($i = 0; $i < $steps && !$this->eof(); ++$i) {
            $this->next();
        }
    }
}