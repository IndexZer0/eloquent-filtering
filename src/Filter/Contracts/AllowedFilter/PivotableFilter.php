<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;

use Illuminate\Support\Collection;

interface PivotableFilter
{
    public function pivot(string $pivotParentModelFqcn, string ...$pivotParentModelFqcns): static;

    public function isPivot(): bool;

    public function getPivotParentModelFqcns(): Collection;
}
