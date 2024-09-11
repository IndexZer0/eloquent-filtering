<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can filter by relationship when allowed', function (): void {

    $query = Author::filter(
        [
            [
                'target' => 'books',
                'type'   => '$has',
                'value'  => [],
            ],
        ],
        Filter::only(
            Filter::relation(
                'books',
                [FilterType::HAS],
            ),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where exists (select * from "books" where "authors"."id" = "books"."author_id")
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
        ->and($models->first()->id)->toBe(1);
});

it('can not filter by relationship when no filter list supplied', function (): void {

    Author::filter(
        [
            [
                'target' => 'books',
                'type'   => '$has',
                'value'  => [],
            ],
        ],
    );

})->throws(DeniedFilterException::class, '"$has" filter for "books" is not allowed');

it('can not filter by relationship when not explicitly allowed | not suppressed', function (): void {

    Author::filter(
        [
            [
                'target' => 'books',
                'type'   => '$has',
                'value'  => [],
            ],
        ],
        Filter::only(),
    );

})->throws(DeniedFilterException::class, '"$has" filter for "books" is not allowed');

it('can not filter by relationship when not explicitly allowed | suppressed', function (): void {

    $this->setSuppression('filter.denied', true);

    $query = Author::filter(
        [
            [
                'target' => 'books',
                'type'   => '$has',
                'value'  => [],
            ],
        ],
        Filter::only(),
    );

    $expectedSql = <<< SQL
        select * from "authors"
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2);

});

it('can filter by relationship with no child filters', function (): void {

    $query = Author::filter(
        [
            [
                'target' => 'books',
                'type'   => '$has',
                // intentionally commented out value below.
                // 'value' => [],
            ],
        ],
        Filter::only(
            Filter::relation(
                'books',
                [FilterType::HAS],
            ),
        ),
    );

    $expectedSql = <<< SQL
        select * from "authors" where exists (select * from "books" where "authors"."id" = "books"."author_id")
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
        ->and($models->first()->id)->toBe(1);
});
