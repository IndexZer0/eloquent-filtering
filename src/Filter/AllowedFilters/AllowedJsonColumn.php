<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedFilters;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\AllFiltersAllowed;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Helpers\JsonPath;

class AllowedJsonColumn implements AllowedFilter
{
    public function __construct(
        protected string $target,
        protected AllowedTypes $types,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public function allowedFilters(): FilterableList
    {
        return new AllFiltersAllowed();
    }

    public function matches(PendingFilter $pendingFilter): bool
    {
        if ($pendingFilter->usage() !== FilterMethod::USAGE_JSON_COLUMN) {
            return false;
        }

        if (!$this->types->contains($pendingFilter->type())) {
            return false;
        }

        return $this->targetMatches($pendingFilter->target());
    }

    public function hydrate(PendingFilter $pendingFilter): PendingFilter
    {
        return $pendingFilter;
    }

    /*
     * -----------------------------
     * Class specific methods
     * -----------------------------
     */

    private function targetMatches(string $target): bool
    {
        $jsonPath = JsonPath::of($this->target);
        return $jsonPath->allows($target);
    }
}
