<?php

namespace Rizen\Core\Parse\Runner;

use Rizen\Core\Input\TokenInput;
use Rizen\Core\Node\Tree\Program;
use Rizen\Core\Parse\Passes\ParsePassInterface;

class ParsePassRunnerInOrder implements ParsePassRunnerInterface
{
    /** @var array<ParsePassInterface> $passes */
    private array $passes;

    public function __construct(ParsePassInterface...$passes)
    {
        $this->passes = $passes;
    }

    public function parseInto(Program $program, TokenInput $input): void
    {
        foreach ($this->passes as $pass) {
            $pass->run($program, $input);
        }
    }
}