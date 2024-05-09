<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Sort\Contracts\SortException;
use IndexZer0\EloquentFiltering\Suppression\Traits\CanBeSuppressed;

class MalformedSortFormatException extends Exception implements SortException, SuppressibleException
{
    use CanBeSuppressed;

    public function __construct(ValidationException $validationException)
    {
        parent::__construct(
            "a sort does not match required format.",
            previous: $validationException
        );
    }

    public function suppressionKey(): string
    {
        return 'suppress.sort.malformed_format';
    }
}
