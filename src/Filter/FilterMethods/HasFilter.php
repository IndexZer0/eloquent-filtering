<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\TargetedFilterMethod;

readonly class HasFilter implements TargetedFilterMethod
{
    public FilterCollection $filters;

    public function __construct(
        public string $target,
        array         $value,
    ) {
        /** @var FilterParser $filterParser */
        $filterParser = resolve(FilterParser::class);
        $this->filters = $filterParser->parse($value);
    }

    public static function type(): string
    {
        return '$has';
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
        return $query->whereHas($this->target, function (Builder $query) use ($filterableList): void {

            /** @var FilterApplier $filterApplier */
            $filterApplier = resolve(FilterApplier::class);
            $filterApplier->apply($query, $filterableList, $this->filters);

        });
    }
}
