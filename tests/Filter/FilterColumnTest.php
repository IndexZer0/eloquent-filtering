<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can filter by column when allowed', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::allow(
            Filter::column('name', ['$eq']),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" = 'George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1);

});

it('can filter by column when no filter list supplied', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" = 'George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1);

});

it('can filter by column with "Filter::all()"', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::all()
    );

    $expectedSql = <<< SQL
        select * from "authors" where "name" = 'George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1);

});

it('can not filter by column when not explicitly allowed | not suppressed', function (): void {

    Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::allow(),
    );

})->throws(DeniedFilterException::class, '"$eq" filter for "name" is not allowed');

it('can not filter by column when not explicitly allowed | suppressed', function (): void {

    config()->set('eloquent-filtering.suppress.filter.denied', true);

    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::allow(),
    );

    $expectedSql = <<< SQL
        select * from "authors"
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});
