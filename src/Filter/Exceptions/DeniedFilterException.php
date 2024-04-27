<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;
use IndexZer0\EloquentFiltering\Filter\Contracts\TargetedFilterMethod;

class DeniedFilterException extends Exception implements FilterException
{
    public static function throw(TargetedFilterMethod $filter): void
    {
        throw new self("\"{$filter::type()}\" filter for \"{$filter->target()}\" is not allowed");
    }

    public function shouldSuppress(): bool
    {
        return config('eloquent-filtering.suppress.filter.denied', false);
    }
}
