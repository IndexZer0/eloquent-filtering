<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedModifiers;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedModifiers;

class AllModifiersAllowed implements AllowedModifiers
{
    public function containsAll(string ...$modifiers): bool
    {
        return true;
    }
}
