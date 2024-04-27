<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
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
use IndexZer0\EloquentFiltering\Filter\FilterMethods\NotLikeFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\NullFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\OrFilter;

class AvailableFiltersLoader
{
    public function __invoke(): Collection
    {
        return $this->getPackageFilters()
            ->merge($this->getCustomFilters())
            ->keyBy(
                fn (string $filterMethodClass) => /** @var $filterMethodClass FilterMethod */
                $filterMethodClass::type()
            );
    }

    private function getPackageFilters(): Collection
    {
        return collect([
            EqualFilter::class,
            NotEqualFilter::class,
            GreaterThanFilter::class,
            GreaterThanEqualToFilter::class,
            LessThanFilter::class,
            LessThanEqualToFilter::class,

            LikeFilter::class,
            LikeEndFilter::class,
            LikeStartFilter::class,
            NotLikeFilter::class,

            HasFilter::class,
            DoesntHasFilter::class,

            OrFilter::class,

            NullFilter::class,

            InFilter::class,
            NotInFilter::class,

            BetweenFilter::class,
            NotBetweenFilter::class,
        ]);
    }

    private function getCustomFilters(): Collection
    {
        return collect(config('eloquent-filtering.custom_filters'))
            ->filter(fn ($filter) => is_a($filter, FilterMethod::class, true));
    }
}
