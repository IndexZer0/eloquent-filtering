<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\ColumnFilters;

use Illuminate\Database\Eloquent\Builder;
use IndexZer0\EloquentFiltering\Filter\Contracts\Target;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractColumnFilter;

class NullFilter extends AbstractColumnFilter
{
    public function __construct(
        protected Target $target,
        protected bool $value,
    ) {

    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function type(): string
    {
        return '$null';
    }

    public static function format(): array
    {
        return [
            'target' => ['required', 'string'],
            'value'  => ['required', 'boolean'],
        ];
    }

    public function apply(Builder $query): Builder
    {
        return $query->whereNull($this->target->getReal(), not: !$this->value);
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static(
            $approvedFilter->target(),
            $approvedFilter->data_get('value'),
        );
    }
}
