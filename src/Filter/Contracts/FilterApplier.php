<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use Illuminate\Contracts\Database\Query\Builder;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;

interface FilterApplier
{
    public function apply(
        Builder $query,
        FilterCollection $filters
    ): Builder;
}
