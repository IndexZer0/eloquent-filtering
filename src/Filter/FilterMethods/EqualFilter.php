<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class EqualFilter extends WhereFilter
{
    public static function type(): string
    {
        return '$eq';
    }

    protected function operator(): string
    {
        return '=';
    }
}
