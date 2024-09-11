<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Context\EloquentContext;
use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Validation\ValidatorProvider;

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
    public static function format(): array|ValidatorProvider;

    /*
     * Apply the filter logic.
     */
    public function apply(Builder $query): Builder;

    /*
     * Set the EloquentContext for the filter method.
     */
    public function setEloquentContext(EloquentContext $eloquentContext): void;

    /*
     * Get the EloquentContext for the filter method.
     */
    public function eloquentContext(): EloquentContext;

    /*
     * Get the filter parser for the filter method.
     */
    public static function customFilterParser(): CustomFilterParser;
}
