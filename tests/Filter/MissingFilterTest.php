<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MissingFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    Author::create([
        'id'   => 1,
        'name' => 'Fred',
    ]);
    Author::create([
        'id'   => 2,
        'name' => 'Fred2',
    ]);
});

it('ignores missing filter when suppressed', function (): void {

    config()->set('eloquent-filtering.suppress.filter.missing', true);

    $query = Author::filter(
        [
            [
                'type' => '$this-filter-does-not-exist',
            ],
        ],
        Filter::all()
    );

    $expectedSql = <<< SQL
        select * from "authors"
        SQL;


    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});

it('errors when provided missing filter when not suppressed', function (): void {

    Author::filter(
        [
            [
                'type' => '$this-filter-does-not-exist',
            ],
        ],
        Filter::all()
    );

})->expectException(MissingFilterException::class);
