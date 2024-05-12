<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\AllowedFilterResolver;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterSet;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterSets\FilterSets;

trait Filterable
{
    public function scopeFilter(
        Builder $query,
        array $filters,
        FilterSet|AllowedFilterList|string|null $allowedFilters = null
    ): Builder {

        $allowedFilterResolver = new AllowedFilterResolver(
            $allowedFilters,
            $this
        );
        $allowedFilters = $allowedFilterResolver->resolve();

        /** @var FilterParser $filterParser */
        $filterParser = resolve(FilterParser::class);
        $filters = $filterParser->parse($filters, $allowedFilters);

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

    public function filterSets(): FilterSets
    {
        return Filter::sets();
    }

    public function getFilterSets(): FilterSets
    {
        return $this->filterSets()->add(
            Filter::set('default', $this->allowedFilters())
        );
    }
}
