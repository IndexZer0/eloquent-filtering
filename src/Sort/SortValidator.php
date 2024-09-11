<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Sort;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use IndexZer0\EloquentFiltering\Sort\Contracts\SortValidator as SortValidatorContract;
use IndexZer0\EloquentFiltering\Sort\Exceptions\MalformedSortFormatException;
use IndexZer0\EloquentFiltering\Sort\Sortable\PendingSort;
use IndexZer0\EloquentFiltering\Suppression\Suppression;

class SortValidator implements SortValidatorContract
{
    protected PendingSortCollection $pendingSorts;

    public function __construct()
    {
        $this->pendingSorts = new PendingSortCollection();
    }

    public function validate(array $sorts): PendingSortCollection
    {
        foreach ($sorts as $sort) {
            Suppression::honour(
                fn () => $this->ensureSortIsValid($sort),
            );
        }

        return $this->pendingSorts;
    }

    private function ensureSortIsValid(mixed $sort): void
    {
        if (!is_array($sort)) {
            throw MalformedSortFormatException::withMessages([
                'sort' => ['Sort must be an array.'],
            ]);
        }

        try {
            Validator::validate($sort, [
                'target' => ['required', 'string'],
                'value'  => ['required', Rule::in(['asc', 'desc'])],
            ], ['value.in' => 'The :attribute must be one of the following types: :values']);
            $this->pendingSorts->push(
                new PendingSort($sort['target'], $sort['value']),
            );
        } catch (ValidationException $ve) {
            throw MalformedSortFormatException::withMessages([
                ...$ve->errors(),
            ]);
        }
    }
}
