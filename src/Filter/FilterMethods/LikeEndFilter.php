<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods;

readonly class LikeEndFilter extends LikeFilter
{
    public static function type(): string
    {
        return '$like:end';
    }

    protected function valueAfter(): string
    {
        return '';
    }
}
