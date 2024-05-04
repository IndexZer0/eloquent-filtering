<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract;

use IndexZer0\EloquentFiltering\Filter\Contracts\AppliesToTarget;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;

abstract class AbstractJsonFieldFilter implements FilterMethod, AppliesToTarget
{
    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function usage(): string
    {
        return FilterMethod::USAGE_JSON_FIELD;
    }

    public static function targetKey(): string
    {
        return 'target';
    }
}
