<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Target\Alias;
use IndexZer0\EloquentFiltering\Filter\Traits\HydratesAlias;

class AllowedRelation implements AllowedFilter
{
    use HydratesAlias;

    public function __construct(
        protected Alias $target,
        protected AllowedTypes $types,
        protected FilterableList $allowedFilters,
        protected ?string $alias = null,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public function allowedFilters(): FilterableList
    {
        return $this->allowedFilters;
    }

    public function matches(PendingFilter $pendingFilter): bool
    {
        if ($pendingFilter->usage() !== FilterMethod::USAGE_RELATION) {
            return false;
        }

        return $this->types->contains($pendingFilter->type()) &&
            $this->target->isFor($pendingFilter->target());
    }

    public function hydrate(PendingFilter $pendingFilter): PendingFilter
    {
        return $this->hydrateAlias($pendingFilter, $this->target);
    }
}
