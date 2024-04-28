<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface FilterMethod
{
    public static function type(): string;

    public static function format(): array;

    public function apply(Builder $query): Builder;
}
