<?php

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface AllowedTypes
{
    public function contains(string $type): bool;
}
