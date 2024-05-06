<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Target;

use IndexZer0\EloquentFiltering\Contracts\Target as TargetContract;

readonly class AliasedRelationTarget implements TargetContract
{
    public array $targets;

    public function __construct(
        public string  $target,
        public ?string $targetAlias = null,
        TargetContract ...$targets
    ) {
        $this->targets = $targets;
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

    public function getChildrenTargets(): array
    {
        return $this->targets;
    }
}
