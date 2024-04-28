<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;

class MalformedFilterFormatException extends Exception implements FilterException
{
    public function __construct(
        private string              $type,
        private ValidationException $validationException
    ) {
        parent::__construct(
            "\"{$type}\" filter does not match required format.",
            previous: $validationException
        );
    }

    public static function throw(string $type, ValidationException $validationException): void
    {
        throw new self($type, $validationException);
    }

    public function shouldSuppress(): bool
    {
        return config('eloquent-filtering.suppress.filter.malformed_format', false);
    }
}
