<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DuplicateFiltersException;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\BetweenColumnsFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\BetweenFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\EqualFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\GreaterThanEqualToFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\GreaterThanFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\InFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\LessThanEqualToFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\LessThanFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\LikeEndFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\LikeFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\LikeStartFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\NotBetweenColumnsFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\NotBetweenFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\NotEqualFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\NotInFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\NotLikeEndFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\NotLikeFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\NotLikeStartFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters\NullFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\ConditionFilters\OrFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\JsonFieldFilters\JsonContainsFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\JsonFieldFilters\JsonLengthFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\JsonFieldFilters\JsonNotContainsFilter;
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
