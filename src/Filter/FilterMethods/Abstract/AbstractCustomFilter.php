<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract;

use IndexZer0\EloquentFiltering\Filter\Context\FilterContext;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterMethod;
use IndexZer0\EloquentFiltering\Filter\Filterable\ApprovedFilter;
use IndexZer0\EloquentFiltering\Filter\Traits\HasModifiers;

abstract class AbstractCustomFilter implements FilterMethod
{
    use HasModifiers;

    final public function __construct(protected array  $modifiers)
    {
    }

    /*
     * -----------------------------
     * Interface methods
     * -----------------------------
     */

    public static function context(): FilterContext
    {
        return FilterContext::CUSTOM;
    }

    public static function format(): array
    {
        return [];
    }

    public static function from(ApprovedFilter $approvedFilter): static
    {
        return new static($approvedFilter->modifiers());
    }
}
