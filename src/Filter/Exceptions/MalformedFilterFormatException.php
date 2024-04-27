<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterException;

class MalformedFilterFormatException extends Exception implements FilterException
{
    public function __construct(
        private string              $filterClass,
        private ValidationException $validationException
    ) {
        parent::__construct(
            "\"{$this->filterClass::type()}\" filter does not match required format.",
            previous: $validationException
        );
    }

    public static function throw(string $filterClass, ValidationException $validationException): void
    {
        throw new self($filterClass, $validationException);
    }

    public function shouldSuppress(): bool
    {
        return config('eloquent-filtering.suppress.filter.malformed_format', false);
    }
}
