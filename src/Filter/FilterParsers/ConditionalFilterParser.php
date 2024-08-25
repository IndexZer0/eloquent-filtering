<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterParsers;

use IndexZer0\EloquentFiltering\Filter\Builder\FilterBuilder;
use IndexZer0\EloquentFiltering\Filter\Context\EloquentContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterParser\EnsuresChildFiltersAllowed;

class ConditionalFilterParser implements CustomFilterParser
{
    use EnsuresChildFiltersAllowed;

    public function parse(
        PendingFilter  $pendingFilter,
        ?AllowedFilter $allowedFilter = null,
        ?AllowedFilterList $allowedFilterList = null,
    ): FilterMethod {

        $childFilters = $this->ensureChildFiltersAllowed(
            $pendingFilter,
            $allowedFilterList,
            $pendingFilter->model(),
            $pendingFilter->relation(),
        );

        return (new FilterBuilder($pendingFilter))
            ->childFilters($childFilters)
            ->build(new EloquentContext(
                $pendingFilter->model(),
                $pendingFilter->relation(),
            ));
    }
}
