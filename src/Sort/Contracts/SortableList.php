<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Contracts;

interface SortableList
{
    public function ensureAllowed(string $column): bool;
}
