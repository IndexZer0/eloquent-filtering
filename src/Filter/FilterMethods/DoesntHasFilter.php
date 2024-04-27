<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;
use IndexZer0\EloquentFiltering\Filter\FilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\TargetedFilterMethod;

readonly class DoesntHasFilter implements TargetedFilterMethod
{
    public FilterCollection $filters;

    public function __construct(
        public string $target,
        array         $value,
    ) {
        $this->filters = resolve(FilterParser::class)->parse($value);
    }

    public static function type(): string
    {
        return '$doesntHas';
    }

    public function target(): string
    {
        return $this->target;
    }

    public function hasTarget(): true
    {
        return true;
    }

    public static function format(): array
    {
        return [
            'target'  => ['required', 'string'],
            'value'   => ['array'],
            'value.*' => ['array'],
        ];
    }

    public function apply(Builder $query, FilterableList $filterableList): Builder
    {
        return $query->whereDoesntHave($this->target, function (Builder $query) use ($filterableList): void {

            /** @var FilterApplier $filterApplier */
            $filterApplier = resolve(FilterApplier::class);
            $filterApplier->apply($query, $filterableList, $this->filters);

        });
    }
}
