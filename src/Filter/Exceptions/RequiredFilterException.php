<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;

class RequiredFilterException extends ValidationException implements FilterException
{
    public static function fromStrings(Collection $strings): self
    {
        return self::withMessages([
            'Missing required filters.' => $strings->map(
                fn ($string) => "{$string} filter is required."
            )->toArray(),
        ]);
    }
}
