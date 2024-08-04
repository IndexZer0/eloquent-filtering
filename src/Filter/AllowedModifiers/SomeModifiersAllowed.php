<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\AllowedModifiers;

use Illuminate\Support\Collection;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedModifiers;

class SomeModifiersAllowed implements AllowedModifiers
{
    protected Collection $modifiers;

    public function __construct(string ...$modifiers)
    {
        $this->modifiers = collect($modifiers);
    }

    public function containsAll(string ...$modifiers): bool
    {
        foreach ($modifiers as $modifier) {
            if (!$this->modifiers->contains($modifier)) {
                return false;
            }
        }
        return true;
    }
}
