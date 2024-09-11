<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ConditionFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext\ConditionFilter;

class OrFilter implements FilterMethod, HasChildFilters
{
    use ConditionFilter;

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return FilterType::OR->value;
    }

    public static function format(): array
    {
        return [
            'value'   => ['required', 'array', 'min:2'],
            'value.*' => ['array'],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            foreach ($this->filters as $filter) {
                $query->orWhere(function ($query) use ($filter): void {
                    $filterApplier = resolve(FilterApplier::class);
                    $filterApplier->apply(
                        $query,
                        new FilterCollection([$filter]),
                    );
                });
            }
        });
    }
}
