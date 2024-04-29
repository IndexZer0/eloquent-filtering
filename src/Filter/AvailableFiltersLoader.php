<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Exceptions\DuplicateFiltersException;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\BetweenFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\DoesntHasFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\EqualFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\GreaterThanEqualToFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\GreaterThanFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\HasFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\InFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\LessThanEqualToFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\LessThanFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\LikeEndFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\LikeFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\LikeStartFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\NotBetweenFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\NotEqualFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\NotInFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\NotLikeEndFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\NotLikeFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\NotLikeStartFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\NullFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\OrFilter;

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

            // NotLike
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
