<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractFieldFilter;

class KebabCaseFilter extends AbstractFieldFilter
{
    final public function __construct(
        protected string $target,
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
            'target' => ['required', 'string'],
            'value'  => ['required', 'string'],
        ];
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static(
            $approvedFilter->target()->getReal(),
            $approvedFilter->data_get('value'),
        );
    }

    public function apply(Builder $query): Builder
    {
        return $query->where(DB::raw($this->target()), $this->value());
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    private function target(): string
    {
        return "LOWER(REPLACE({$this->target}, ' ', '-'))";
    }

    private function value(): string
    {
        return strtolower(str_replace(' ', '-', $this->value));
    }
}
