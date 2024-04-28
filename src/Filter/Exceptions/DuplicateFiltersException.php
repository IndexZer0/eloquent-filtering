<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;

class DuplicateFiltersException extends Exception implements FilterException
{
    public function __construct(array $types)
    {
        parent::__construct(
            sprintf(
                "Filters with the following types have been registered more than once: \"%s\"",
                join(', ', $types)
            )
        );
    }
}
