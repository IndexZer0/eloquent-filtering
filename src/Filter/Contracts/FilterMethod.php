<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;

interface FilterMethod
{
    /*
     * The unique identifier of the filter.
     */
    public static function type(): string;

    /*
     * Whether this filter is applicable for an AllowedFilter definition.
     */
    public static function context(): FilterContext;

    /*
     * The format that the filter data must adhere to.
     * Defined as laravel validator rules.
     * On fail: throws MalformedFilterFormatException.
     */
    public static function format(): array;

    /*
     * Instantiate filter class from ApprovedFilter.
     */
    public static function from(ApprovedFilter $approvedFilter): static;

    /*
     * Apply the filter logic.
     */
    public function apply(Builder $query): Builder;
}
