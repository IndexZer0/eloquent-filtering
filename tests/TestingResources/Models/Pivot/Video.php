<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;

class Video extends Model implements IsFilterable
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}
