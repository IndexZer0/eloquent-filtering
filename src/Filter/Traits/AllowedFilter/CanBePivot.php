<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\AllowedFilter;

use Illuminate\Support\Collection;

trait CanBePivot
{
    protected ?Collection $pivotParentModelFqcns = null;

    public function pivot(string $pivotParentModelFqcn, string ...$pivotParentModelFqcns): static
    {
        $this->pivotParentModelFqcns = collect($pivotParentModelFqcns)->prepend($pivotParentModelFqcn);
        return $this;
    }

    public function isPivot(): bool
    {
        return $this->pivotParentModelFqcns !== null;
    }

    public function getPivotParentModelFqcns(): Collection
    {
        return $this->pivotParentModelFqcns;
    }
}
