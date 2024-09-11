<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use IndexZer0\EloquentFiltering\Contracts\IsFilterable;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;

class Author extends Model implements IsFilterable
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    /*
     * ----------------------------------
     * Relations
     * ----------------------------------
     */

    public function profile(): HasOne
    {
        return $this->hasOne(AuthorProfile::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    // This relationship exists for RequiredFilterTest.
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
