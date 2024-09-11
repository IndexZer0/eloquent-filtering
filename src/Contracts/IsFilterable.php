<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Contracts;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;

interface IsFilterable
{
    public function scopeFilter(
        Builder $query,
        array $filters,
        ?AllowedFilterList $allowedFilterList = null,
    ): Builder;

    public function allowedFilters(): AllowedFilterList;
}
