<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Traits\HyrdratesAlias;

class AllowedRelation implements AllowedFilter
{
    use HyrdratesAlias;

    public function __construct(
        protected string $target,
        protected array  $types,
        protected FilterableList $allowedFilters,
        protected ?string $alias = null,
    ) {
    }

    public function allowedFilters(): FilterableList
    {
        return $this->allowedFilters;
    }

    public function matches(PendingFilter $pendingFilter): bool
    {
        if ($pendingFilter->usage() !== FilterMethod::USAGE_RELATION) {
            return false;
        }

        return in_array($pendingFilter->type(), $this->types, true) &&
            $this->target === $pendingFilter->target();
    }

    public function hydrate(PendingFilter $pendingFilter): PendingFilter
    {
        return $this->hydrateAlias($pendingFilter, $this->alias);
    }
}
