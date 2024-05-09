<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;
use IndexZer0\EloquentFiltering\Suppression\Traits\CanBeSuppressed;

class MissingFilterException extends Exception implements FilterException, SuppressibleException
{
    use CanBeSuppressed;

    public static function throw(string $type): void
    {
        throw new self("Can not find filter for \"{$type}\"");
    }

    public function suppressionKey(): string
    {
        return 'suppress.filter.missing';
    }
}
