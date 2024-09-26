<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\BelongsToMany;

use Illuminate\Database\Eloquent\Relations\Pivot;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;

class RoleUser extends Pivot implements IsFilterable
{
    use Filterable;

    protected $guarded = [];

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::field('assigned_by', [FilterType::EQUAL])
                ->pivot(Role::class, User::class),
        );
    }
}
