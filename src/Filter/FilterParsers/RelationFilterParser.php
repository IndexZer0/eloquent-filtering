<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterParsers;

use IndexZer0\EloquentFiltering\Filter\Builder\FilterBuilder;
use IndexZer0\EloquentFiltering\Filter\Context\EloquentContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\DefinesAllowedChildFilters;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\TargetedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterParser\EnsuresChildFiltersAllowed;

class RelationFilterParser implements CustomFilterParser
{
    use EnsuresChildFiltersAllowed;

    public function parse(
        PendingFilter  $pendingFilter,
        ?AllowedFilter $allowedFilter = null,
        ?AllowedFilterList $allowedFilterList = null,
    ): FilterMethod {
        /** @var TargetedFilter&DefinesAllowedChildFilters $allowedFilter */
        $target = $allowedFilter->getTarget($pendingFilter);
        $relation = $pendingFilter->model()->{$target->getReal()}();

        $childFilters = $this->ensureChildFiltersAllowed(
            $pendingFilter,
            $allowedFilter->allowedFilters(),
            $relation->getRelated(),
            $relation,
        );

        $filterBuilder = new FilterBuilder(
            $pendingFilter,
            new EloquentContext(
                $pendingFilter->model(),
                $relation,
            ),
        );

        return $filterBuilder->target($target)
            ->childFilters($childFilters)
            ->build();
    }
}
