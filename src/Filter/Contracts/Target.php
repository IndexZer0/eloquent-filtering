<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface Target
{
    public function isFor(string $target): bool;

    public function getReal(): string;
}
