<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\BelongsToMany;

use Illuminate\Database\Eloquent\Relations\Pivot;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\IncludeRelationFields\BelongsToMany\AllowedFilters\WithoutPivot;

class RoleUser extends Pivot implements IsFilterable
{
    use Filterable;

    public static string $allowedFiltersClass = WithoutPivot::class;

    protected $guarded = [];

    public function allowedFilters(): AllowedFilterList
    {
        return (new self::$allowedFiltersClass())();
    }
}
