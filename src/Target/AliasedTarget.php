<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Target;

use IndexZer0\EloquentFiltering\Contracts\Target as TargetContract;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

readonly class AliasedTarget implements TargetContract
{
    public function __construct(
        public string  $target,
        public ?string $targetAlias = null
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public function isFor(string $target): bool
    {
        return $this->target === $target;
    }

    public function getReal(): string
    {
        return $this->targetAlias ?? $this->target;
    }

    public function target(): string
    {
        return $this->target;
    }

    public function getForApprovedFilter(PendingFilter $pendingFilter): self
    {
        return $this;
    }
}
