<?php

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

interface Target
{
    public function isFor(string $target): bool;

    public function hasAlias(): bool;

    public function getReal(): string;
}
