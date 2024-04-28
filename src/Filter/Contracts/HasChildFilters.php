<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface HasChildFilters
{
    public static function childFiltersKey(): string;
}
