<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterParser;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;

trait EnsuresChildFiltersAllowed
{
    private function ensureChildFiltersAllowed(
        PendingFilter $pendingFilter,
        AllowedFilterList $allowedFilterList,
        Model $model,
        ?Relation $relation = null
    ): ?FilterCollection {
        $filterFqcn = $pendingFilter->filterFqcn();

        if (is_a($filterFqcn, HasChildFilters::class, true)) {

            $filterData = $pendingFilter->data();

            /** @var FilterParser $filterParser */
            $filterParser = resolve(FilterParser::class);

            $filters = $filterData['value'] ?? [];

            return $filterParser->parse(
                $model,
                $filters,
                $allowedFilterList,
                $relation ?? $pendingFilter->relation(),
                $pendingFilter,
            );
        }

        return null;
    }
}
