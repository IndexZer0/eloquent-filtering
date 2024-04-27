<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class NotInFilter extends InFilter
{
    public static function type(): string
    {
        return '$notIn';
    }

    protected function not(): bool
    {
        return true;
    }
}
