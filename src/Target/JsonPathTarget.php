<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Target;

use IndexZer0\EloquentFiltering\Contracts\Target as TargetContract;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;
use IndexZer0\EloquentFiltering\Filter\Helpers\JsonPath;

readonly class JsonPathTarget implements TargetContract
{
    public function __construct(
        public string $target,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public function isFor(string $target): bool
    {
        $jsonPath = JsonPath::of($this->target);
        return $jsonPath->allows($target);
    }

    public function getReal(): string
    {
        return $this->target;
    }

    public function target(): string
    {
        return $this->target;
    }

    public function getForApprovedFilter(PendingFilter $pendingFilter): TargetContract
    {
        return Target::alias($pendingFilter->desiredTarget());
    }
}
