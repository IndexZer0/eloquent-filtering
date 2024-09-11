<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext\FieldFilter;

class DuplicateFilter implements FilterMethod
{
    use FieldFilter;

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return FilterType::EQUAL->value;
    }

    public static function format(): array
    {
        return [];
    }

    public function apply(Builder $query): Builder
    {
        return $query->where(
            $this->eloquentContext->qualifyColumn($this->target),
            'value',
        );
    }
}
