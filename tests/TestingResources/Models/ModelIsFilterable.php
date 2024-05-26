<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;

class ModelIsFilterable extends Model implements IsFilterable
{
    use Filterable;

    public function notFilterable(): BelongsTo
    {
        return $this->belongsTo(ModelIsNotFilterable::class);
    }
}
