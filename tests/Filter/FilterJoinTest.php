<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can filter by join when allowed', function (): void {

    $query = Author::filter(
        [
            [
                'target' => 'books',
                'type'   => '$join',
                'value'  => [
                    [
                        'type'   => '$like',
                        'target' => 'title',
                        'value'  => 'Game',
                    ],
                ],
            ],
        ],
        Filter::only(
            Filter::relation(
                'books',
                ['$join'],
                Filter::only(
                    Filter::field('title', ['$like'])
                )
            )
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" inner join "books" on "authors"."id" = "books"."author_id" and "title" LIKE '%Game%'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1);
});
