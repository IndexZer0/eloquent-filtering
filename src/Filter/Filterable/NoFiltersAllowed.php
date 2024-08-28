<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Filterable;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;

class NoFiltersAllowed implements AllowedFilterList
{
    public function ensureAllowed(PendingFilter $pendingFilter): FilterMethod
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

    public function getAllowedFields(): array
    {
        return [];
    }

    public function getAllowedRelations(): array
    {
        return [];
    }

    public function getAll(): array
    {
        return [];
    }

    public function getUnmatchedRequiredFiltersIdentifiers(bool $parentWasMatched): Collection
    {
        return collect();
    }
}
