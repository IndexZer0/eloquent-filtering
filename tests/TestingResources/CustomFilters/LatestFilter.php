<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext\CustomFilter;

class LatestFilter implements FilterMethod
{
    use CustomFilter;

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$latest';
    }

    public function apply(Builder $query): Builder
    {
        return $query->latest();
    }
}
