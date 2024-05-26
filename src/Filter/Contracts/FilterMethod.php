<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;

interface FilterMethod
{
    public const USAGE_FIELD = 'usage_field';
    public const USAGE_RELATION = 'usage_relation';
    public const USAGE_CUSTOM = 'usage_custom';
    public const USAGE_CONDITION = 'usage_condition';

    public static function type(): string;

    public static function usage(): string;

    public static function format(): array;

    public static function from(ApprovedFilter $approvedFilter): static;

    public function apply(Builder $query): Builder;
}
