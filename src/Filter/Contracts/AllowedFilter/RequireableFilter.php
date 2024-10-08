<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter;

interface RequireableFilter
{
    /*
     * -----------------------------
     * Required support
     * -----------------------------
     */

    public function required(bool $required): self;

    public function isRequired(): bool;

    public function isScoped(): bool;

    public function getRequiredMessage(): ?string;

    public function markMatched(): void;

    public function hasBeenMatched(): bool;
}
