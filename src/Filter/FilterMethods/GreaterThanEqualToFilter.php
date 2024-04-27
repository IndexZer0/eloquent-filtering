<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class GreaterThanEqualToFilter extends WhereFilter
{
    public static function type(): string
    {
        return '$gte';
    }

    protected function operator(): string
    {
        return '>=';
    }
}
