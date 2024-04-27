<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

readonly class SortableColumn
{
    public function __construct(public string $target)
    {
    }

    public function target(): string
    {
        return $this->target;
    }
}
