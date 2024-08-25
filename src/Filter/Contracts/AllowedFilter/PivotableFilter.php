<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;

interface PivotableFilter
{
    public function pivot(bool $pivot = true): self;

    public function isPivot(): bool;
}
