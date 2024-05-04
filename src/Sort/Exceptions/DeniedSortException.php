<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Sort\Contracts\SortException;

class DeniedSortException extends Exception implements SortException, SuppressibleException
{
    public function __construct(string $field = "")
    {
        parent::__construct("\"{$field}\" sort is not allowed");
    }

    public function shouldSuppress(): bool
    {
        return config('eloquent-filtering.suppress.sort.denied', false);
    }
}
