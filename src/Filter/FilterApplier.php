<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier as FilterApplierContract;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Suppression\Suppression;

readonly class FilterApplier implements FilterApplierContract
{
    public function __construct()
    {
    }

    public function apply(
        Builder $query,
        FilterableList $filterableList,
        FilterCollection $filters
    ): Builder {
        /** @var FilterMethod $filter */
        foreach ($filters as $filter) {

            Suppression::honour(
                fn () => $this->applyFilter($query, $filterableList, $filter),
            );

        }

        return $query;
    }

    private function applyFilter(Builder $query, FilterableList $filterableList, FilterMethod $filter): Builder
    {
        $filterableList = $filterableList->ensureAllowed($filter);

        return $filter->apply($query, $filterableList);
    }
}
