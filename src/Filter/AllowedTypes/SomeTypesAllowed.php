<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedTypes;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;

class SomeTypesAllowed implements AllowedTypes
{
    public function __construct(public array $types = [])
    {

    }

    public function contains(string $type): bool
    {
        return in_array($type, $this->types, true);
    }
}
