<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\Composables;

trait HasTarget
{
    protected string $target;

    public function setTarget(string $target): void
    {
        $this->target = $target;
    }

    public static function hasTargetRules(): array
    {
        return ['target' => ['required', 'string']];
    }
}
