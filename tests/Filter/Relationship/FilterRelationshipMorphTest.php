<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\DeniedFilterException;
use IndexZer0\EloquentFiltering\Filter\Exceptions\InvalidModelFqcnException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\Article;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\Image;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\UserProfile;

beforeEach(function (): void {
    $this->createMorphRecords();
});

it('can filter by all morphs', function (): void {

    $query = Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => '*',
                    'value' => [],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::HAS_MORPH],
            Filter::morphType('*'),
        ),
    ));

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and exists (select * from "articles" where "images"."imageable_id" = "articles"."id")) or ("images"."imageable_type" = 'user_profiles' and exists (select * from "user_profiles" where "images"."imageable_id" = "user_profiles"."id")))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(8)
        ->and($models->pluck('url')->toArray())->toBe([
            'image-1',
            'image-2',
            'image-3',
            'image-4',
            'image-5',
            'image-6',
            'image-7',
            'image-8',
        ]);
});

it('can filter by all morphs with child filters', function (): void {

    $query = Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => '*',
                    'value' => [
                        [
                            'target' => 'created_at',
                            'type'   => '$lte',
                            'value'  => '4000-01-01',
                        ],
                    ],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::HAS_MORPH],
            Filter::morphType(
                '*',
                Filter::only(
                    Filter::field('created_at', [FilterType::LESS_THAN_EQUAL_TO]),
                ),
            ),
        ),
    ));

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and exists (select * from "articles" where "images"."imageable_id" = "articles"."id" and "articles"."created_at" <= '4000-01-01')) or ("images"."imageable_type" = 'user_profiles' and exists (select * from "user_profiles" where "images"."imageable_id" = "user_profiles"."id" and "user_profiles"."created_at" <= '4000-01-01')))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(8)
        ->and($models->pluck('url')->toArray())->toBe([
            'image-1',
            'image-2',
            'image-3',
            'image-4',
            'image-5',
            'image-6',
            'image-7',
            'image-8',
        ]);
});

it('can filter by specific morphs', function (): void {

    $query = Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'articles',
                    'value' => [],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::HAS_MORPH],
            Filter::morphType(
                Article::class,
            ),
        ),
    ));

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and exists (select * from "articles" where "images"."imageable_id" = "articles"."id")))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(4)
        ->and($models->pluck('url')->toArray())->toBe([
            'image-1',
            'image-2',
            'image-3',
            'image-4',
        ]);
});

it('can filter by specific morphs with child filters', function (): void {

    $query = Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'articles',
                    'value' => [
                        [
                            'target' => 'title',
                            'type'   => '$eq',
                            'value'  => 'article-1',
                        ],
                    ],
                ],
                [
                    'type'  => 'user_profiles',
                    'value' => [
                        [
                            'target' => 'name',
                            'type'   => '$eq',
                            'value'  => 'user-profile-1',
                        ],
                    ],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::HAS_MORPH],
            Filter::morphType(Article::class, Filter::only(
                Filter::field('title', [FilterType::EQUAL]),
            )),
            Filter::morphType(UserProfile::class, Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
            )),
        ),
    ));

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and exists (select * from "articles" where "images"."imageable_id" = "articles"."id" and "articles"."title" = 'article-1')) or ("images"."imageable_type" = 'user_profiles' and exists (select * from "user_profiles" where "images"."imageable_id" = "user_profiles"."id" and "user_profiles"."name" = 'user-profile-1')))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(4)
        ->and($models->pluck('url')->toArray())->toBe([
            'image-1',
            'image-2',
            'image-5',
            'image-6',
        ]);
});

it('can filter by nested relation in specific morph', function (): void {

    $query = Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'articles',
                    'value' => [
                        [
                            'target' => 'title',
                            'type'   => '$eq',
                            'value'  => 'article-1',
                        ],
                        [
                            'target' => 'images',
                            'type'   => '$has',
                            'value'  => [
                                [
                                    'target' => 'url',
                                    'type'   => '$eq',
                                    'value'  => 'image-1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::HAS_MORPH],
            Filter::morphType(
                Article::class,
                Filter::only(
                    Filter::field('title', [FilterType::EQUAL]),
                    Filter::relation(
                        'images',
                        [FilterType::HAS],
                        Filter::only(
                            Filter::field('url', [FilterType::EQUAL]),
                        ),
                    ),
                ),
            ),
        ),
    ));

    $expectedSql = <<< SQL
        select * from "images" where (("images"."imageable_type" = 'articles' and exists (select * from "articles" where "images"."imageable_id" = "articles"."id" and "articles"."title" = 'article-1' and exists (select * from "images" where "articles"."id" = "images"."imageable_id" and "images"."imageable_type" = 'articles' and "images"."url" = 'image-1'))))
        SQL;

    expect($query->toRawSql())->toBe($expectedSql);

    $models = $query->get();

    expect($models->count())->toBe(2)
        ->and($models->pluck('url')->toArray())->toBe([
            'image-1',
            'image-2',
        ]);
});

it('throws exception when morphType is not a fqcn of a model', function (): void {

    $query = Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'articles',
                    'value' => [],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::HAS_MORPH],
            Filter::morphType(
                'not-a-fqcn',
            ),
        ),
    ));
})->throws(InvalidModelFqcnException::class, 'Must be an eloquent model fully qualified class name.');

it('throws DeniedFilterException when morphRelation not allowed', function (): void {

    Image::filter([
        [
            'target' => 'imageable2',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => '*',
                    'value' => [],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::HAS_MORPH],
            Filter::morphType('*'),
        ),
    ));

})->throws(DeniedFilterException::class, '"$hasMorph" filter for "imageable2" is not allowed');

it('throws DeniedFilterException when morphType not allowed', function (): void {

    Image::filter([
        [
            'target' => 'imageable',
            'type'   => '$hasMorph',
            'types'  => [
                [
                    'type'  => 'articles',
                    'value' => [
                        [
                            'target' => 'title',
                            'type'   => '$eq',
                            'value'  => 'article-1',
                        ],
                    ],
                ],
            ],
        ],
    ], Filter::only(
        Filter::morphRelation(
            'imageable',
            [FilterType::HAS_MORPH],
            Filter::morphType(UserProfile::class, Filter::only(
                Filter::field('name', [FilterType::EQUAL]),
            )),
        ),
    ));

})->throws(DeniedFilterException::class, '"$hasMorph" filter for "imageable" is not allowed');
