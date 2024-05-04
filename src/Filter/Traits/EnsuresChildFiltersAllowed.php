<?php

namespace IndexZer0\EloquentFiltering\Filter\Traits;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser as FilterParserContract;
use IndexZer0\EloquentFiltering\Filter\Contracts\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;

trait EnsuresChildFiltersAllowed
{
    private function ensureChildFiltersAllowed(PendingFilter $pendingFilter, AllowedFilterList $allowedFilterList): ?FilterCollection
    {
        $filterFqcn = $pendingFilter->filterFqcn();

        if (is_a($filterFqcn, HasChildFilters::class, true)) {

            $data = $pendingFilter->data();

            /** @var FilterParserContract $filterParser */
            $filterParser = resolve(FilterParserContract::class);

            return $filterParser->parse(
                $data[$filterFqcn::childFiltersKey()],
                $allowedFilterList
            );
        }

        return null;
    }
}
