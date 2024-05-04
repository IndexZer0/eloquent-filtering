<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Target;

use IndexZer0\EloquentFiltering\Filter\Contracts\Target as TargetContract;

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

    public function hasAlias(): bool
    {
        return $this->targetAlias !== null;
    }

    public function getReal(): string
    {
        return $this->targetAlias ?? $this->target;
    }
}
