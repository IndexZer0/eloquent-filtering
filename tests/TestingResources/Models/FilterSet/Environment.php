<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\FilterSet;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterSets\FilterSets;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;
use IndexZer0\EloquentFiltering\Tests\TestingResources\FilterSets\EnvironmentAdminFieldFilters;
use IndexZer0\EloquentFiltering\Tests\TestingResources\FilterSets\EnvironmentBasicFieldFilters;
use IndexZer0\EloquentFiltering\Tests\TestingResources\FilterSets\EnvironmentRelationFilters;

class Environment extends Model implements IsFilterable
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    /*
     * ----------------------------------
     * IsFilterable interface methods
     * ----------------------------------
     */

    public function filterSets(): FilterSets
    {
        return Filter::sets(
            Filter::classSet(EnvironmentBasicFieldFilters::class),
            Filter::classSet(EnvironmentAdminFieldFilters::class)->extends(EnvironmentBasicFieldFilters::class),
            Filter::classSet(EnvironmentRelationFilters::class),

            Filter::set('admin')->extends([
                EnvironmentAdminFieldFilters::class,
                EnvironmentRelationFilters::class,
            ]),
        );
    }

    /*
     * ----------------------------------
     * Relations
     * ----------------------------------
     */

    public function secrets(): HasMany
    {
        return $this->hasMany(Secret::class);
    }
}
