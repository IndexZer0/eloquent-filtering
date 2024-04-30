<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests\TestingResources\Models;

use Illuminate\Database\Eloquent\Model;
use IndexZer0\EloquentFiltering\Filter\Traits\Filterable;
use IndexZer0\EloquentFiltering\Sort\Traits\Sortable;

class ApiResponse extends Model
{
    use Filterable;
    use Sortable;

    protected $guarded = [];

    protected $casts = [
        'data' => 'json',
    ];
}