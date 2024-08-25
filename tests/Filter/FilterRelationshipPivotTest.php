<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\Post;

beforeEach(function (): void {
    $this->createPostTagAndPivotRecords();
});

it('can filter by pivot field when allowed', function (): void {

    $query = Post::filter([
        [
            'target' => 'tags',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'tag-name-1',
                ],
                [
                    'target' => 'tagged_by',
                    'type'   => '$eq',
                    'value'  => 'tagged-by-user-1',
                ],
            ],
        ],
    ], Filter::only(
        Filter::relation(
            'tags',
            [FilterType::HAS],
            allowedFilters: Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(),
            ),
        )
    ));

    $expectedSql = <<< SQL
        select * from "posts" where exists (select * from "tags" inner join "post_tag" on "tags"."id" = "post_tag"."tag_id" where "posts"."id" = "post_tag"."post_id" and "tags"."name" = 'tag-name-1' and "post_tag"."tagged_by" = 'tagged-by-user-1')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->title)->toBe('post-title-1');

});

it('ors with pivot', function (): void {

    $query = Post::filter([
        [
            'target' => 'tags',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'name',
                    'type'   => '$eq',
                    'value'  => 'tag-name-1',
                ],
                [
                    'type'  => '$or',
                    'value' => [
                        [
                            'target' => 'tagged_by',
                            'type'   => '$eq',
                            'value'  => 'tagged-by-user-1',
                        ],
                        [
                            'target' => 'tagged_by',
                            'type'   => '$eq',
                            'value'  => 'tagged-by-user-2',
                        ],
                    ],
                ],
            ],
        ],
    ], Filter::only(
        Filter::relation(
            'tags',
            [FilterType::HAS],
            allowedFilters: Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(),
            ),
        )
    ));

    $expectedSql = <<< SQL
        select * from "posts" where exists (select * from "tags" inner join "post_tag" on "tags"."id" = "post_tag"."tag_id" where "posts"."id" = "post_tag"."post_id" and "tags"."name" = 'tag-name-1' and (("post_tag"."tagged_by" = 'tagged-by-user-1') or ("post_tag"."tagged_by" = 'tagged-by-user-2')))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->title)->toBe('post-title-1');

});
