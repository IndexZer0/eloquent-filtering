<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters;

use Illuminate\Contracts\Database\Query\Builder;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractFieldFilter;

class DuplicateFilter extends AbstractFieldFilter
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

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static();
    }

    public function apply(Builder $query): Builder
    {
        return $query->where('field', 'value');
    }
}
