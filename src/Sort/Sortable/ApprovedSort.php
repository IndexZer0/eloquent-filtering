<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Sortable;

use IndexZer0\EloquentFiltering\Contracts\Target;

class ApprovedSort
{
    public function __construct(
        protected Target $target,
        protected string $direction,
    ) {
    }

    public function target(): Target
    {
        return $this->target;
    }

    public function direction(): string
    {
        return $this->direction;
    }
}
