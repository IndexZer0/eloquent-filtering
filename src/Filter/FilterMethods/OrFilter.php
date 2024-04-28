<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;

class OrFilter implements FilterMethod, HasChildFilters
{
    public function __construct(
        protected FilterCollection $value,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$or';
    }

    public static function format(): array
    {
        return [
            'value'   => ['required', 'array', 'min:1'],
            'value.*' => ['array'],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            foreach ($this->value as $filter) {
                $query->orWhere(function ($query) use ($filter): void {
                    $filterApplier = resolve(FilterApplier::class);
                    $filterApplier->apply($query, new FilterCollection([$filter]));
                });
            }
        });
    }

    public static function childFiltersKey(): string
    {
        return 'value';
    }
}
