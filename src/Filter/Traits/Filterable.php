<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;

trait Filterable
{
    public function scopeFilter(
        Builder $query,
        array $filters,
        ?FilterableList $filterableList = null
    ): Builder {

        /** @var FilterParser $filterParser */
        $filterParser = resolve(FilterParser::class);
        $filters = $filterParser->parse($filters);

        /** @var FilterApplier $filterApplier */
        $filterApplier = resolve(FilterApplier::class);
        return $filterApplier->apply(
            $query,
            $filterableList ?? $this->allowedFilters(),
            $filters
        );
    }

    protected function allowedFilters(): FilterableList
    {
        return Filter::all();
    }
}