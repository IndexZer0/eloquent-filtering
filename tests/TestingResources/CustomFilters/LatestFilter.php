<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractCustomFilter;

class LatestFilter extends AbstractCustomFilter
{
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
