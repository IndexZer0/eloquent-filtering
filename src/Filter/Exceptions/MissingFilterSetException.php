<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;

class MissingFilterSetException extends Exception implements FilterException
{
    public static function throw(string $filterSetName): void
    {
        throw new self("Can not find filter set named: \"{$filterSetName}\"");
    }
}
