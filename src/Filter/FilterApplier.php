<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Contracts\Database\Query\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier as FilterApplierContract;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Suppression\Suppression;

class FilterApplier implements FilterApplierContract
{
    public function __construct()
    {
    }

    public function apply(
        Builder $query,
        FilterCollection $filters
    ): Builder {
        /** @var FilterMethod $filter */
        foreach ($filters as $filter) {

            Suppression::honour(
                fn () => $this->applyFilter($query, $filter),
            );

        }

        return $query;
    }

    private function applyFilter(Builder $query, FilterMethod $filter): Builder
    {
        return $filter->apply($query);
    }
}
