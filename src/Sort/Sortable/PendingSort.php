<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use IndexZer0\EloquentFiltering\Contracts\Target;

class PendingSort
{
    public function __construct(
        protected string $target,
        protected string $direction,
    ) {
    }

    public function target(): string
    {
        return $this->target;
    }

    public function approveWith(Target $target): ApprovedSort
    {
        return new ApprovedSort(
            $target,
            $this->direction
        );
    }
}
