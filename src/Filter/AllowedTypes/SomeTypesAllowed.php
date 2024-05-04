<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedTypes;

use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedTypes;

class SomeTypesAllowed implements AllowedTypes
{
    public function __construct(
        protected array $types = [],
        protected bool $except = false,
    ) {
    }

    public function contains(string $type): bool
    {
        $inTypes = in_array($type, $this->types, true);

        return $this->except ? !$inTypes : $inTypes;
    }
}
