<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort\Contracts;

interface AllowedSortList
{
    public function ensureAllowed(string $field): bool;
}
