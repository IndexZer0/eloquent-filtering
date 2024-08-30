<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Validation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\PendingFilter;

class ValidatorService
{
    public function execute(PendingFilter $pendingFilter, array|ValidatorProvider $rules): void
    {
        $rules = ValidatorProvider::normalizeRules($rules);

        try {
            $validator = Validator::make(
                $pendingFilter->data(),
                $rules->getRules(),
                $rules->getMessages(),
                $rules->getAttributes()
            );
            $validator->validate();
        } catch (ValidationException $ve) {
            throw MalformedFilterFormatException::withMessages([
                'filter' => ["\"{$pendingFilter->requestedFilter()->fullTypeString()}\" filter does not match required format."],
                ...$ve->errors(),
            ]);
        }

    }
}
