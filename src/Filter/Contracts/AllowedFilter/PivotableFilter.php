<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;

interface PivotableFilter
{
    public function pivot(string $pivotTable): self;

    public function isPivot(): bool;

    public function getPivotTable(): string;
}
