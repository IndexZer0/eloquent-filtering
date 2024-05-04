<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract;

use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;

abstract class AbstractCustomFilter implements FilterMethod
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function usage(): string
    {
        return FilterMethod::USAGE_CUSTOM;
    }

    public static function format(): array
    {
        return [];
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static();
    }
}
