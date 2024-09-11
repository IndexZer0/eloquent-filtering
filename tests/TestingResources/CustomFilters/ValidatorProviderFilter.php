<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\Targetable;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext\FieldFilter;
use IndexZer0\EloquentFiltering\Filter\Validation\ValidatorProvider;

class ValidatorProviderFilter implements FilterMethod, Targetable
{
    use FieldFilter;

    public function __construct(
        protected string $value,
    ) {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$validatorProvider';
    }

    public static function format(): ValidatorProvider
    {
        return ValidatorProvider::from([
            'value' => ['string', ],
        ], [
            'string' => ':attribute IS NOT A STRING',
        ], [
            'value' => 'attribute_value',
        ]);
    }

    public function apply(Builder $query): Builder
    {
        return $query->where('a', 'b');
    }
}
