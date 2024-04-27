<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterableList;
use IndexZer0\EloquentFiltering\Filter\Contracts\TargetedFilterMethod;

readonly class KebabCaseFilter implements TargetedFilterMethod
{
    public function __construct(
        public string $target,
        public string $value,
    ) {

    }

    public static function type(): string
    {
        return '$kebabCase';
    }

    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', 'string'],
        ];
    }

    public function apply(Builder $query, FilterableList $filterableList): Builder
    {
        return $query->where(DB::raw($this->getTargetForQuery()), $this->getValueForQuery());
    }

    private function getTargetForQuery(): string
    {
        return "LOWER(REPLACE({$this->target}, ' ', '-'))";
    }

    private function getValueForQuery(): string
    {
        return strtolower(str_replace(' ', '-', $this->value));
    }

    public function target(): string
    {
        return $this->target;
    }

    public function hasTarget(): true
    {
        return true;
    }
}
