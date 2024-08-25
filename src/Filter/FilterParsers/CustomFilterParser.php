<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterParsers;

use IndexZer0\EloquentFiltering\Filter\AllowedFilters\AllowedField;
use IndexZer0\EloquentFiltering\Filter\Builder\FilterBuilder;
use IndexZer0\EloquentFiltering\Filter\Context\EloquentContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\CustomFilterParser as CustomFilterParserContract;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

class CustomFilterParser implements CustomFilterParserContract
{
    public function parse(
        PendingFilter  $pendingFilter,
        ?AllowedFilter $allowedFilter = null,
        ?AllowedFilterList $allowedFilterList = null,
    ): FilterMethod {
        return (new FilterBuilder($pendingFilter))
            ->build(
                new EloquentContext(
                    $pendingFilter->model(),
                    $pendingFilter->relation(),
                    $allowedFilter instanceof AllowedField && $allowedFilter->isPivot()
                )
            );
    }
}
