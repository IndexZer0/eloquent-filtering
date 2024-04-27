<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class LessThanFilter extends WhereFilter
{
    public static function type(): string
    {
        return '$lt';
    }

    protected function operator(): string
    {
        return '<';
    }
}
