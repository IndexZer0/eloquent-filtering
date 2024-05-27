<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;

class NoFiltersAllowed implements AllowedFilterList
{
    public function ensureAllowed(PendingFilter $pendingFilter): ApprovedFilter
    {
        throw new DeniedFilterException($pendingFilter);
    }

    public function resolveRelationsAllowedFilters(string $modelFqcn): NoFiltersAllowed
    {
        return $this;
    }

    public function add(AllowedFilter ...$allowedFilters): AllowedFilterList
    {
        return Filter::only(...$allowedFilters);
    }

    public function getAllowedFilters(): array
    {
        return [];
    }

    public function getAllowedFields(): array
    {
        return [];
    }

    public function getAllowedRelations(): array
    {
        return [];
    }
}
