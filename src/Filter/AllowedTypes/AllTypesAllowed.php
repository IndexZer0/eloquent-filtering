<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedTypes;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;

class AllTypesAllowed implements AllowedTypes
{
    public function contains(string $type): bool
    {
        return true;
    }
}
