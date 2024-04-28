<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;

class DeniedFilterException extends Exception implements FilterException, SuppressibleException
{
    public function __construct(string $type, ?string $target)
    {
        parent::__construct("\"{$type}\" filter for \"{$target}\" is not allowed");
    }

    public function shouldSuppress(): bool
    {
        return config('eloquent-filtering.suppress.filter.denied', false);
    }
}
