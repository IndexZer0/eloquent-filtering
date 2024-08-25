<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilter\AllowedFilter;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;

class RequiredFilterException extends ValidationException implements FilterException
{
    public static function fromAllowedFilters(AllowedFilter ...$allowedFilters): self
    {
        return self::withMessages(
            collect($allowedFilters)
                ->mapWithKeys(
                    fn (AllowedFilter $allowedFilter) => [$allowedFilter->getDescription() => $allowedFilter->getDescription() . ' is required.']
                )->toArray()
        );
    }
}
