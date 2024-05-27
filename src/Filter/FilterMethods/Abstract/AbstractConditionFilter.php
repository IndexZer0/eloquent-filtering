<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract;

use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;

abstract class AbstractConditionFilter implements FilterMethod
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function context(): FilterContext
    {
        return FilterContext::CONDITION;
    }
}
