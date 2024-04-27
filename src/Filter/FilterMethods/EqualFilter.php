<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class EqualFilter extends WhereFilter
{
    protected function operator(): string
    {
        return '=';
    }

    public static function type(): string
    {
        return '$eq';
    }
}
