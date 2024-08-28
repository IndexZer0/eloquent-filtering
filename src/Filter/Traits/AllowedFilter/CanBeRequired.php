<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\AllowedFilter;

trait CanBeRequired
{
    protected bool $required = false;
    protected bool $scoped = false;
    protected bool $matched = false;

    public function required(bool $required = true, $scoped = false): self
    {
        $this->required = $required;
        $this->scoped = $scoped;
        return $this;
    }

    public function markMatched(): void
    {
        $this->matched = true;
    }

    public function isRequired(): bool
    {
        return $this->required;
    }

    public function isScoped(): bool
    {
        return $this->scoped;
    }

    public function hasBeenMatched(): bool
    {
        return $this->matched;
    }
}
