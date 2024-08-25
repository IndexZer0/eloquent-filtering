<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\AllowedFilter;

trait CanBePivot
{
    protected bool $pivot = false;

    public function pivot(bool $pivot = true): self
    {
        $this->pivot = $pivot;
        return $this;
    }

    public function isPivot(): bool
    {
        return $this->pivot;
    }
}
