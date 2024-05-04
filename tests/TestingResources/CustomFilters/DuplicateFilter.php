<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractColumnFilter;

class DuplicateFilter extends AbstractColumnFilter
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$eq';
    }

    public static function format(): array
    {
        return [];
    }

    public function apply(Builder $query): Builder
    {
        return $query->where('column', 'value');
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static();
    }
}
