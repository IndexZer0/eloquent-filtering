<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables;

use IndexZer0\EloquentFiltering\Filter\Validation\ValidatorProvider;

trait HasTarget
{
    protected string $target;

    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    public static function hasTargetRules(): ValidatorProvider
    {
        return ValidatorProvider::from([
            'target' => ['required', 'string'],
        ], [
            'target.required' => 'filter target is required.',
            'target.string' => 'filter target must be a string.',
        ]);
    }
}
