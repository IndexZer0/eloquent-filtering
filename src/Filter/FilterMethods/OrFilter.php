<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;
use IndexZer0\EloquentFiltering\Filter\FilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\UntargetedFilterMethod;

readonly class OrFilter implements UntargetedFilterMethod
{
    public FilterCollection $filters;

    public function __construct(
        array $value,
    ) {
        /** @var \IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser $filterParser */
        $filterParser = resolve(FilterParser::class);
        $this->filters = $filterParser->parse($value);
    }

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

    public function hasTarget(): false
    {
        return false;
    }

    public function apply(Builder $query, FilterableList $filterableList): Builder
    {
        return $query->where(function (Builder $query) use ($filterableList): void {
            foreach ($this->filters as $filter) {
                $query->orWhere(function ($query) use ($filter, $filterableList): void {
                    $filterApplier = resolve(FilterApplier::class);
                    $filterApplier->apply($query, $filterableList, new FilterCollection([$filter]));
                });
            }
        });
    }


}
