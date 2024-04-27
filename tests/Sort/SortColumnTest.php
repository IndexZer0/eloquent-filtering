<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Sort\Exceptions\DeniedSortException;
use IndexZer0\EloquentFiltering\Sort\Sortable\Sort;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\AuthorProfile;

beforeEach(function (): void {
    Author::create([
        'id'   => 1,
        'name' => 'Name 1',
    ]);
    AuthorProfile::create([
        'age'       => 20,
        'author_id' => 1,
    ]);

    Author::create([
        'id'   => 2,
        'name' => 'Name 2',
    ]);
    AuthorProfile::create([
        'age'       => 30,
        'author_id' => 2,
    ]);

    Author::create([
        'id'   => 3,
        'name' => 'Name 3',
    ]);
    AuthorProfile::create([
        'age'       => 40,
        'author_id' => 3,
    ]);
});

it('can sort by column when allowed', function (): void {
    $query = Author::sort(
        [
            [
                'target' => 'name',
                'value'  => 'desc',
            ],
        ],
        Sort::allow(
            Sort::column('name'),
        )
    );

    $expectedSql = <<< SQL
        select * from "authors" order by "name" desc
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(3)
        ->and($models->pluck('id')->toArray())->toBe([3, 2, 1]);

});

it('can sort by column when no sort list supplied', function (): void {
    $query = Author::sort(
        [
            [
                'target' => 'name',
                'value'  => 'desc',
            ],
        ],
    );

    $expectedSql = <<< SQL
        select * from "authors" order by "name" desc
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(3)
        ->and($models->pluck('id')->toArray())->toBe([3, 2, 1]);

});

it('can sort by column with "Sort::all()"', function (): void {
    $query = Author::sort(
        [
            [
                'target' => 'name',
                'value'  => 'desc',
            ],
        ],
        Sort::all()
    );

    $expectedSql = <<< SQL
        select * from "authors" order by "name" desc
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(3)
        ->and($models->pluck('id')->toArray())->toBe([3, 2, 1]);

});

it('can not sort by column when not explicitly allowed | not suppressed', function (): void {

    $query = Author::sort(
        [
            [
                'target' => 'name',
                'value'  => 'desc',
            ],
        ],
        Sort::allow(),
    );

})->throws(DeniedSortException::class, '"name" sort is not allowed');

it('can not filter by column when not explicitly allowed | suppressed', function (): void {

    config()->set('eloquent-filtering.suppress.sort.denied', true);

    $query = Author::sort(
        [
            [
                'target' => 'name',
                'value'  => 'desc',
            ],
        ],
        Sort::allow(),
    );

    $expectedSql = <<< SQL
        select * from "authors"
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(3);

});

// TODO - how to sort on relation column ?

/*it('can sort by relationship column when allowed', function (): void {
    $query = Author::sort(
        [
            [
                'target' => 'author_profiles.age',
                'value'  => 'desc',
            ],
        ],
        Sort::allow(
            Sort::column('author_profiles.age'),
        )
    )->leftJoin('author_profiles', 'authors.id', '=', 'author_profiles.author_id');

    $expectedSql = <<< SQL
        select * from "authors" order by "name" desc
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(3)
        ->and($models->pluck('id')->toArray())->toBe([3, 2, 1]);

});*/
