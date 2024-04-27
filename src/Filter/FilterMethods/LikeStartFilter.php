<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class LikeStartFilter extends LikeFilter
{
    protected function valueBefore(): string
    {
        return '';
    }

    public static function type(): string
    {
        return '$like:start';
    }
}