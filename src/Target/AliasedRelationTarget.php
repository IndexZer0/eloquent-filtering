<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Target;

use IndexZer0\EloquentFiltering\Contracts\Target as TargetContract;

readonly class AliasedRelationTarget extends AliasedTarget
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
     * AliasedRelationTarget methods
     * -----------------------------
     */

    public function getChildTargets(): array
    {
        return $this->targets;
    }
}
