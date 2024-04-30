<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

class DeniedFilterException extends Exception implements FilterException, SuppressibleException
{
    public function __construct(PendingFilter $pendingFilter)
    {
        parent::__construct($pendingFilter->getDeniedMessage());
    }

    public function shouldSuppress(): bool
    {
        return config('eloquent-filtering.suppress.filter.denied', false);
    }
}
