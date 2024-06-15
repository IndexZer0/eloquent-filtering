<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\FieldFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractFieldFilter;
use IndexZer0\EloquentFiltering\Rules\WhereValue;

class InFilter extends AbstractFieldFilter
{
    final public function __construct(
        protected string $target,
        protected array $value,
    ) {

    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$in';
    }

    public static function format(): array
    {
        return [
            'target'  => ['required', 'string'],
            'value'   => ['required', 'array'],
            'value.*' => ['required', new WhereValue()],
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
        // Maybe be nice to check $this->value for containing null and handle that here
        return $query->whereIn($this->target, $this->value, not: $this->not());
    }

    /*
     * -----------------------------
     * Filter specific methods
     * -----------------------------
     */

    protected function not(): bool
    {
        return false;
    }
}
