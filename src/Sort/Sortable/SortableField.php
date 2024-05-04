<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use IndexZer0\EloquentFiltering\Contracts\Target;

class SortableField
{
    public function __construct(protected Target $target)
    {
    }

    public function target(): Target
    {
        return $this->target;
    }
}
