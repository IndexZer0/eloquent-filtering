<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Sort\Sortable\Sort;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can filter and sort together', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$like',
                'value'  => 'R',
            ],
        ],
        Filter::only(
            Filter::field('name', [FilterType::LIKE]),
        ),
    )->sort(
        [
            [
                'target' => 'name',
                'value'  => 'desc',
            ],
        ],
        Sort::only(
            Sort::field('name')
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" LIKE '%R%' order by "name" desc
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
        ->and($models->pluck('id')->toArray())->toBe([2, 1]);

});
