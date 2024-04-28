<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;

class MissingFilterException extends Exception implements FilterException, SuppressibleException
{
    public static function throw(string $type): void
    {
        throw new self("Can not find filter for \"{$type}\"");
    }

    public function shouldSuppress(): bool
    {
        return config('eloquent-filtering.suppress.filter.missing', false);
    }
}
