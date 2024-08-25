<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Contracts;

use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

interface Target
{
    public function isFor(string $target): bool;

    public function getReal(): string;

    public function target(): string;

    public function getForFilterMethod(PendingFilter $pendingFilter): self;
}
