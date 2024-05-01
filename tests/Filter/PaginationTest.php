<?php

declare(strict_types=1);

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('works with pagination', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::allowOnly(
            Filter::column('name', ['$eq']),
        )
    );

    DB::enableQueryLog();

    /** @var LengthAwarePaginator $paginator */
    $paginator = $query->paginate();

    $queryLog = collect(DB::getQueryLog())->map(fn ($query) => collect($query)->except('time')->toArray());

    expect($queryLog->toArray())->toBe([
        [
            "query"    => "select count(*) as aggregate from \"authors\" where \"name\" = ?",
            "bindings" => [
                "George Raymond Richard Martin",
            ],
        ],
        [
            "query"    => "select * from \"authors\" where \"name\" = ? limit 15 offset 0",
            "bindings" => [
                "George Raymond Richard Martin",
            ],
        ],
    ])
        ->and($paginator)->toBeInstanceOf(LengthAwarePaginator::class)
        ->and($paginator->count())->toBe(1)
        ->and($model = $paginator->getCollection()->first())->toBeInstanceOf(Author::class)
        ->and($model->name)->toBe('George Raymond Richard Martin');

    DB::disableQueryLog();

});
