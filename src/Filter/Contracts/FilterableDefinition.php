<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface FilterableDefinition
{
    public function target(): string;

    public function types(): array;

    public function definitions(): array;
}
