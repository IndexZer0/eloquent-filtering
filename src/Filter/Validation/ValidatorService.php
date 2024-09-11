<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Validation;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
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
                $rules->getAttributes(),
            );
            $validator->validate();
        } catch (ValidationException $ve) {
            $mainMessage = collect([
                Str::ucfirst($pendingFilter->identifier()),
                'filter does not match required format.',
            ])->join(' ');

            throw MalformedFilterFormatException::withMessages([
                $pendingFilter->nestedIdentifer() => [$mainMessage],
                ...$this->nestErrorKeys($pendingFilter, $ve->errors()),
            ]);
        }

    }

    private function nestErrorKeys(PendingFilter $pendingFilter, array $errors): array
    {
        return collect($errors)
            ->mapWithKeys(fn ($value, $key) => [
                "{$pendingFilter->nestedIdentifer()}.{$key}" => $value,
            ])->toArray();
    }
}
