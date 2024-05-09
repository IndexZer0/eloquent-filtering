<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Suppression\Traits;

trait CanBeSuppressed
{
    public function isSuppressed(): bool
    {
        return config(implode('.', ['eloquent-filtering', $this->suppressionKey()]));
    }
}
