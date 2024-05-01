<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface AllowedTypes
{
    public function contains(string $type): bool;
}
