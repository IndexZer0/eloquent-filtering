<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class WhereValue implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            $fail('The :attribute must be string, integer or float.');
        }
    }
}
