<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod\Targetable;
use IndexZer0\EloquentFiltering\Filter\Traits\FilterMethod\FilterContext\FieldFilter;
use IndexZer0\EloquentFiltering\Rules\TargetRules;

class KebabCaseFilter implements FilterMethod, Targetable
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
        return '$kebabCase';
    }

    public static function format(): array
    {
        return [
            ...TargetRules::get(),
            'value' => ['required', 'string'],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->where(
            DB::raw("LOWER(REPLACE({$this->eloquentContext->qualifyColumn($this->target)}, ' ', '-'))"),
            strtolower(str_replace(' ', '-', $this->value)),
        );
    }
}
