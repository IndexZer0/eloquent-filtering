<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Exceptions;

use Exception;
use IndexZer0\EloquentFiltering\Contracts\SuppressibleException;
use IndexZer0\EloquentFiltering\Sort\Contracts\SortException;
use IndexZer0\EloquentFiltering\Sort\Sortable\PendingSort;
use IndexZer0\EloquentFiltering\Suppression\Traits\CanBeSuppressed;

class DeniedSortException extends Exception implements SortException, SuppressibleException
{
    use CanBeSuppressed;

    public function __construct(PendingSort $pendingSort)
    {
        parent::__construct("\"{$pendingSort->target()}\" sort is not allowed");
    }

    public function suppressionKey(): string
    {
        return 'suppress.sort.denied';
    }
}
