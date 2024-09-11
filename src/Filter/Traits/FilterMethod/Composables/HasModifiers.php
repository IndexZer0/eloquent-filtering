<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables;

use Illuminate\Support\Collection;

trait HasModifiers
{
    protected Collection $modifiers;

    public function setModifiers(array $modifiers): void
    {
        $this->modifiers = collect($modifiers);
    }

    public function hasModifier(string $modifier): bool
    {
        return $this->modifiers->contains($modifier);
    }
}
