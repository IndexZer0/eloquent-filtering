<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;
use IndexZer0\EloquentFiltering\Suppression\Traits\CanBeSuppressed;

class MalformedFilterFormatException extends Exception implements FilterException, SuppressibleException
{
    use CanBeSuppressed;

    public function __construct(
        string              $type,
        ValidationException $validationException
    ) {
        parent::__construct(
            "\"{$type}\" filter does not match required format.",
            previous: $validationException
        );
    }

    public function suppressionKey(): string
    {
        return 'suppress.filter.malformed_format';
    }
}
