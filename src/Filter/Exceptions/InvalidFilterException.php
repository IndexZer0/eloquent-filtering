<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;
use IndexZer0\EloquentFiltering\Suppression\Traits\CanBeSuppressed;

class InvalidFilterException extends Exception implements FilterException, SuppressibleException
{
    use CanBeSuppressed;

    public static function throw(): void
    {
        throw new self('Filter must be an array containing `type` (string).');
    }

    public function suppressionKey(): string
    {
        return 'suppress.filter.invalid';
    }
}
