<?php

declare(strict_types=1);

use Illuminate\Database\Query\JoinClause;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Target\Target;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

beforeEach(function (): void {
    $this->createAuthors();
});

it('can filter by field with dot notation', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'authors.name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field('authors.name', [FilterType::EQUAL]),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" = 'George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1);

});

it('can filter by field with dot notation with alias', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'name',
                'type'   => '$eq',
                'value'  => 'George Raymond Richard Martin',
            ],
        ],
        Filter::only(
            Filter::field(Target::alias('name', 'authors.name'), [FilterType::EQUAL]),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" where "authors"."name" = 'George Raymond Richard Martin'
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1);

});

it('can filter by field with dot notation on a join column', function (): void {
    $query = Author::filter(
        [
            [
                'target' => 'author_profiles.age',
                'type'   => '$eq',
                'value'  => 20,
            ],
        ],
        Filter::only(
            Filter::field('author_profiles.age', [FilterType::EQUAL]),
        )
    )->join('author_profiles', function (JoinClause $join): void {
        $join->on('authors.id', '=', 'author_profiles.author_id');
    });

    $expectedSql = <<< SQL
        select * from "authors" inner join "author_profiles" on "authors"."id" = "author_profiles"."author_id" where "author_profiles"."age" = 20
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->id)->toBe(1)
        ->and($models->first()->name)->toBe('George Raymond Richard Martin');

});
