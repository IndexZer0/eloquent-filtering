<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\AllowedFilterResolver;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\RequiredFilters\RequiredFiltersChecker;

trait Filterable
{
    public function scopeFilter(
        Builder $query,
        array $filters,
        ?AllowedFilterList $allowedFilterList = null,
    ): Builder {

        $allowedFilterResolver = new AllowedFilterResolver(
            $allowedFilterList ?? $this->allowedFilters(),
            self::class,
        );
        $allowedFilterList = $allowedFilterResolver->resolve();

        /** @var FilterParser $filterParser */
        $filterParser = resolve(FilterParser::class);
        $filters = $filterParser->parse(
            model: $this,
            filters: $filters,
            allowedFilterList: $allowedFilterList,
        );

        $requiredFiltersChecker = new RequiredFiltersChecker(
            $allowedFilterList,
            true,
        );
        $requiredFiltersChecker->__invoke();

        /** @var FilterApplier $filterApplier */
        $filterApplier = resolve(FilterApplier::class);
        return $filterApplier->apply(
            $query,
            $filters,
        );
    }

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::none();
    }
}
