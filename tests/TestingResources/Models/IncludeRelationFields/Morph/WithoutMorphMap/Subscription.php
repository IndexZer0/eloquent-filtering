<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\WithoutMorphMap;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Contracts\AllowedFilterList;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;

class Subscription extends Model implements IsFilterable
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    public function subscribable(): MorphTo
    {
        return $this->morphTo();
    }

    public function allowedFilters(): AllowedFilterList
    {
        return Filter::only(
            Filter::morphRelation(
                'subscribable',
                [FilterType::HAS_MORPH],
            )->includeRelationFields([
                FoodDeliveryService::class,
                Sass::class,
            ]),
        );
    }
}
