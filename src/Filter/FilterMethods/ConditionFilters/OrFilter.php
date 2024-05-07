<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ConditionFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\Contracts\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractConditionFilter;

class OrFilter extends AbstractConditionFilter implements HasChildFilters
{
    final public function __construct(
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
            'value'   => ['required', 'array', 'min:2'],
            'value.*' => ['array'],
        ];
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static(
            $approvedFilter->childFilters()
        );
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
