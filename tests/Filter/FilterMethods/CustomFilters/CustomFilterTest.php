<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters\LatestFilter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Book;

beforeEach(function (): void {
    Author::create([
        'id'   => 1,
        'name' => 'George Raymond Richard Martin',
    ]);
    Book::create([
        'id'          => 1,
        'author_id'   => 1,
        'title'       => 'A Game of Thrones',
        'description' => 'A Game of Thrones',
    ]);
});

it('can perform a custom "custom" filter | $latest', function (): void {

    config()->set('eloquent-filtering.custom_filters', [LatestFilter::class]);

    $query = Book::filter(
        [
            [
                'type' => '$latest',
            ],
        ],
        Filter::only(
            Filter::custom(['$latest']),
        )
    );

    $expectedSql = <<< SQL
        select * from "books" order by "created_at" desc
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1)
        ->and($models->first()->title)->toBe($models->first()->description);

});
