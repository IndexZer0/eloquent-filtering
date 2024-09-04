<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ManyToManyMorph;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;

class Label extends Model implements IsFilterable
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    public function epics(): MorphToMany
    {
        return $this->morphedByMany(Epic::class, 'labelable');
    }

    public function issues(): MorphToMany
    {
        return $this->morphedByMany(Issue::class, 'labelable');
    }
}
