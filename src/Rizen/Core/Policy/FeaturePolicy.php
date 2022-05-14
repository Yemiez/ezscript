<?php

namespace Rizen\Core\Policy;

class FeaturePolicy
{
    private array $features = [];

    public function __construct(array $features = [])
    {
        $this->features = $features;
    }

    public function isEnabled(string $feature): bool
    {
        return $this->features[$feature] ?? false;
    }

    public function enable(string $feature): self
    {
        $this->features[$feature] = true;
        return $this;
    }

    public function disable(string $feature): self
    {
        $this->features[$feature] = false;
        return $this;
    }

}
