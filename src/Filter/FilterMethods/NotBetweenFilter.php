<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class NotBetweenFilter extends BetweenFilter
{
    public static function type(): string
    {
        return '$notBetween';
    }

    protected function not(): bool
    {
        return true;
    }
}
