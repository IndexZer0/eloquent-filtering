<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\BlogPost;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\Video;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\Post;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\Tag;

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
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Post::class),
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

    $query = Tag::filter([
        [
            'target' => 'name',
            'type'   => '$eq',
            'value'  => 'tag-name-1',
        ],
        [
            'target' => 'posts',
            'type'   => '$has',
            'value'  => [
                [
                    'target' => 'tagged_by',
                    'type'   => '$eq',
                    'value'  => 'tagged-by-user-1',
                ],
            ],
        ],
    ], Filter::only(
        Filter::field('name', [FilterType::EQUAL]),
        Filter::relation(
            'posts',
            [FilterType::HAS],
            allowedFilters: Filter::only(
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Tag::class),
            ),
        )
    ));

    $expectedSql = <<< SQL
        select * from "tags" where "tags"."name" = 'tag-name-1' and exists (select * from "posts" inner join "post_tag" on "posts"."id" = "post_tag"."post_id" where "tags"."id" = "post_tag"."tag_id" and "post_tag"."tagged_by" = 'tagged-by-user-1')
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(1)
        ->and($models->first()->name)->toBe('tag-name-1');

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
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Post::class),
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

it('can not use pivot filter when not in context of a relationship', function (): void {

    Tag::filter([
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
    ], Filter::only(
        Filter::field('name', [FilterType::EQUAL]),
        Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Post::class),
    ));

})->throws(DeniedFilterException::class, '"$eq" filter for "tagged_by" is not allowed');

it('can not use pivot filter when in context of different relationship (BelongsTo)', function (): void {

    Video::filter([
        [
            'target' => 'tag',
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
            'tag',
            [FilterType::HAS],
            allowedFilters: Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Post::class),
            ),
        )
    ));

})->throws(DeniedFilterException::class, '"$eq" filter for "tagged_by" is not allowed');

it('can not use pivot filter when in context of different relationship (BelongsToMany)', function (): void {

    BlogPost::filter([
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
                Filter::field('tagged_by', [FilterType::EQUAL])->pivot(Post::class),
            ),
        )
    ));

})->throws(DeniedFilterException::class, '"$eq" filter for "tagged_by" is not allowed');
