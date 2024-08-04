<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NullableWhereValue implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) && !is_int($value) && !is_float($value) && !is_null($value)) {
            $fail('The :attribute must be string, integer, float or null.');
        }
    }
}
