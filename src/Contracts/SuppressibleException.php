<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Contracts;

interface SuppressibleException
{
    public function suppressionKey(): string;
    public function isSuppressed(): bool;
}
