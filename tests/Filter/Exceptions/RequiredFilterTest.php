<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\RequiredFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Target\Target;
use IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters\LatestFilter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('throws RequiredFilterException when required filters have not been matched', function (): void {

    config()->set('eloquent-filtering.custom_filters', [LatestFilter::class]);

    try {
        Author::filter(
            [], // Filters intentionally empty.
            Filter::only(
                Filter::field('name', [FilterType::LIKE])->required(),
                Filter::relation(
                    'books',
                    [FilterType::HAS],
                    Filter::only(
                        Filter::field('title', [FilterType::LIKE])->required(),
                    )
                )->required(),
                Filter::morphRelation(
                    'imageable',
                    [FilterType::HAS_MORPH],
                    Filter::morphType(
                        'articles',
                        Filter::only(
                            Filter::field('title', [FilterType::LIKE])->required()
                        )
                    )->required()
                )->required(),
                Filter::custom('$latest')->required()
            )
        );

        $this->fail('Should have thrown exception');

    } catch (RequiredFilterException $rfe) {
        expect($rfe->getMessage())->toBe('"name" filter is required. (and 6 more errors)')
            ->and($rfe->errors())->toBe([
                'Missing required filters.' => [
                    '"name" filter is required.',
                    '"books" filter is required.',
                    '"books" -> "title" filter is required.',
                    '"imageable" filter is required.',
                    '"imageable" -> "articles" filter is required.',
                    '"imageable" -> "articles" -> "title" filter is required.',
                    '"$latest" filter is required.',
                ],
            ]);
    }

});

it('only includes required errors when parent has been matched when using scoped', function (): void {

    config()->set('eloquent-filtering.custom_filters', [LatestFilter::class]);

    try {
        Author::filter(
            [
                [
                    'target' => 'books2',
                    'type'   => '$has',
                ],
            ],
            Filter::only(
                Filter::field('name', [FilterType::LIKE])->required(scoped: true),
                Filter::relation(
                    'books',
                    [FilterType::HAS],
                    Filter::only(
                        Filter::field('title', [FilterType::LIKE])->required(scoped: true),
                    )
                )->required(scoped: true),
                Filter::relation(
                    Target::alias('books2', 'books'),
                    [FilterType::HAS],
                    Filter::only(
                        Filter::field('title', [FilterType::LIKE])->required(scoped: true),
                    )
                )->required(scoped: true),
                Filter::morphRelation(
                    'imageable',
                    [FilterType::HAS_MORPH],
                    Filter::morphType(
                        'articles',
                        Filter::only(
                            Filter::field('title', [FilterType::LIKE])->required(scoped: true)
                        )
                    )->required(scoped: true)
                )->required(scoped: true),
                Filter::custom('$latest')->required(scoped: true)
            )
        );

        $this->fail('Should have thrown exception');

    } catch (RequiredFilterException $rfe) {
        expect($rfe->getMessage())->toBe('"name" filter is required. (and 4 more errors)')
            ->and($rfe->errors())->toBe([
                'Missing required filters.' => [
                    '"name" filter is required.',
                    '"books" filter is required.',
                    '"books2" -> "title" filter is required.',
                    '"imageable" filter is required.',
                    '"$latest" filter is required.',
                ],
            ]);
    }

});

it('does not throw RequiredFilterException when required filters have been matched', function (): void {

    config()->set('eloquent-filtering.custom_filters', [LatestFilter::class]);

    try {
        Author::filter(
            [
                [
                    'target' => 'name',
                    'type'   => '$like',
                    'value'  => 'George',
                ],
                [
                    'target' => 'books',
                    'type'   => '$has',
                    'value'  => [
                        [
                            'target' => 'title',
                            'type'   => '$like',
                            'value'  => 'Thrones',
                        ],
                    ],
                ],
                [
                    'target' => 'imageable',
                    'type'   => '$hasMorph',
                    'types'  => [
                        [
                            'type'  => 'articles',
                            'value' => [
                                [
                                    'target' => 'title',
                                    'type'   => '$like',
                                    'value'  => 'article-1',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'type' => '$latest',
                ],
            ],
            Filter::only(
                Filter::field('name', [FilterType::LIKE])->required(),
                Filter::relation(
                    'books',
                    [FilterType::HAS],
                    Filter::only(
                        Filter::field('title', [FilterType::LIKE])->required()
                    )
                )->required(),
                Filter::morphRelation(
                    'imageable',
                    [FilterType::HAS_MORPH],
                    Filter::morphType(
                        'articles',
                        Filter::only(
                            Filter::field('title', [FilterType::LIKE])->required()
                        )
                    )->required()
                )->required(),
                Filter::custom('$latest')->required()
            )
        );

        $this->assertTrue(true);

    } catch (RequiredFilterException $rfe) {
        $this->fail('Should not have thrown exception');
    }

});
