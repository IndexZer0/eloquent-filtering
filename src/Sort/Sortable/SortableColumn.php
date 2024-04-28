<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

class SortableColumn
{
    public function __construct(protected string $target)
    {
    }

    public function target(): string
    {
        return $this->target;
    }
}
