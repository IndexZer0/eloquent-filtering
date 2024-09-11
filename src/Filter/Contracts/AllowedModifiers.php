<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface AllowedModifiers
{
    public function containsAll(string ...$modifiers): bool;
}
