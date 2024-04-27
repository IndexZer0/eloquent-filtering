<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;

interface FilterApplier
{
    public function apply(
        Builder $query,
        FilterableList $filterableList,
        FilterCollection $filters
    ): Builder;
}
