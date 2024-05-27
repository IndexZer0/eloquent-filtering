<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract;

use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;

abstract class AbstractCustomFilter implements FilterMethod
{
    final public function __construct()
    {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function context(): FilterContext
    {
        return FilterContext::CUSTOM;
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
