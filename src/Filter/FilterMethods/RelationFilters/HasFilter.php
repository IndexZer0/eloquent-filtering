<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\RelationFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractRelationFilter;
use IndexZer0\EloquentFiltering\Filter\FilterType;

class HasFilter extends AbstractRelationFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return FilterType::HAS->value;
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereHas($this->target, function (Builder $query): void {

            /** @var FilterApplier $filterApplier */
            $filterApplier = resolve(FilterApplier::class);
            $filterApplier->apply($query, $this->value);

        }, $this->operator());
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function operator(): string
    {
        return '>=';
    }
}
