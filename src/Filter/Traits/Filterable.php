<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\AllowedFilterResolver;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Exceptions\RequiredFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;

trait Filterable
{
    public function scopeFilter(
        Builder $query,
        array $filters,
        ?AllowedFilterList $allowedFilterList = null
    ): Builder {

        $allowedFilterResolver = new AllowedFilterResolver(
            $allowedFilterList ?? $this->allowedFilters(),
            self::class
        );
        $allowedFilterList = $allowedFilterResolver->resolve();

        /** @var FilterParser $filterParser */
        $filterParser = resolve(FilterParser::class);
        $filters = $filterParser->parse($filters, $allowedFilterList);

        $unmatchedRequiredFilters = $allowedFilterList->getUnmatchedRequiredFilters();
        if ($unmatchedRequiredFilters->isNotEmpty()) {
            throw RequiredFilterException::fromRequiredFilters(...$unmatchedRequiredFilters->toArray());
        }

        /** @var FilterApplier $filterApplier */
        $filterApplier = resolve(FilterApplier::class);
        return $filterApplier->apply(
            $query,
            $filters
        );
    }

    public function allowedFilters(): AllowedFilterList
    {
        $defaultAllowedList = config('eloquent-filtering.default_allowed_filter_list', 'none');

        return $defaultAllowedList === 'none' ? Filter::none() : Filter::all();
    }
}
