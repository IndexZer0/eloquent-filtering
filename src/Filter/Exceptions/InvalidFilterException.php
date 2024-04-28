<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;

class InvalidFilterException extends Exception implements FilterException, SuppressibleException
{
    public static function throw(): void
    {
        throw new self('Filter must be an array containing `type` (string).');
    }

    public function shouldSuppress(): bool
    {
        return config('eloquent-filtering.suppress.filter.invalid', false);
    }
}
