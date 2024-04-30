<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\HasChildFilters;
use IndexZer0\EloquentFiltering\Filter\FilterCollection;

abstract class AbstractRelationFilter extends AbstractColumnFilter implements HasChildFilters
{
    public function __construct(
        protected string           $target,
        protected FilterCollection $value,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function usage(): string
    {
        return FilterMethod::USAGE_RELATION;
    }

    public static function format(): array
    {
        return [
            'target'  => ['required', 'string'],
            'value'   => ['array'],
            'value.*' => ['array'],
        ];
    }

    public static function childFiltersKey(): string
    {
        return 'value';
    }
}