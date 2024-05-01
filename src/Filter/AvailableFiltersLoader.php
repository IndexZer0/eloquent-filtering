<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DuplicateFiltersException;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\BetweenColumnsFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\BetweenFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\EqualFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\GreaterThanEqualToFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\GreaterThanFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\InFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\LessThanEqualToFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\LessThanFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\LikeEndFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\LikeFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\LikeStartFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\NotBetweenColumnsFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\NotBetweenFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\NotEqualFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\NotInFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\NotLikeEndFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\NotLikeFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\NotLikeStartFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters\NullFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ConditionFilters\OrFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\JsonColumnFilters\JsonContainsFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\JsonColumnFilters\JsonLengthFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\JsonColumnFilters\JsonNotContainsFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\RelationFilters\DoesntHasFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\RelationFilters\HasFilter;

class AvailableFiltersLoader
{
    public function __invoke(): Collection
    {
        $filters = $this->getPackageFilters()->merge($this->getCustomFilters());

        $this->ensureNoDuplicateTypes($filters);

        return $filters->keyBy(
            function (string $filterMethodFqcn) {
                /** @var $filterMethodFqcn FilterMethod */
                return $filterMethodFqcn::type();
            }
        );
    }

    private function getPackageFilters(): Collection
    {
        return collect([
            // Equal
            EqualFilter::class,
            NotEqualFilter::class,

            // Greater Than
            GreaterThanFilter::class,
            GreaterThanEqualToFilter::class,

            // Less Than
            LessThanFilter::class,
            LessThanEqualToFilter::class,

            // Like
            LikeFilter::class,
            LikeStartFilter::class,
            LikeEndFilter::class,

            // Not Like
            NotLikeFilter::class,
            NotLikeStartFilter::class,
            NotLikeEndFilter::class,

            // Or
            OrFilter::class,

            // Null
            NullFilter::class,

            // In
            InFilter::class,
            NotInFilter::class,

            // Between
            BetweenFilter::class,
            NotBetweenFilter::class,

            // Between Columns
            BetweenColumnsFilter::class,
            NotBetweenColumnsFilter::class,

            // Json
            JsonContainsFilter::class,
            JsonNotContainsFilter::class,
            JsonLengthFilter::class,

            // Relationship
            HasFilter::class,
            DoesntHasFilter::class,
        ]);
    }

    private function getCustomFilters(): Collection
    {
        return collect(config('eloquent-filtering.custom_filters'))
            ->filter(fn ($filter) => is_a($filter, FilterMethod::class, true));
    }

    private function ensureNoDuplicateTypes(Collection $filters): void
    {
        $duplicateTypes = $filters->map(
            function (string $filterMethodFqcn) {
                /** @var $filterMethodFqcn FilterMethod */
                return $filterMethodFqcn::type();
            }
        )->duplicates();

        if ($duplicateTypes->count() > 0) {
            throw new DuplicateFiltersException($duplicateTypes->toArray());
        }
    }
}
