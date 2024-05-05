<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Sort\Contracts\SortException;

class MalformedSortFormatException extends Exception implements SortException, SuppressibleException
{
    public function __construct(ValidationException $validationException)
    {
        parent::__construct(
            "a sort does not match required format.",
            previous: $validationException
        );
    }

    public function shouldSuppress(): bool
    {
        return config('eloquent-filtering.suppress.sort.malformed_format', false);
    }
}
