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
        expect($rfe->getMessage())->toBe('Name filter is required. (and 6 more errors)')
            ->and($rfe->errors())->toBe([
                'name' => [
                    'Name filter is required.',
                ],
                'books' => [
                    'Books filter is required.',
                ],
                'books.title' => [
                    'Title filter is required.',
                ],
                'imageable' => [
                    'Imageable filter is required.',
                ],
                'imageable.articles' => [
                    'Articles filter is required.',
                ],
                'imageable.articles.title' => [
                    'Title filter is required.',
                ],
                '$latest' => [
                    '$latest filter is required.',
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
        expect($rfe->getMessage())->toBe('Name filter is required. (and 4 more errors)')
            ->and($rfe->errors())->toBe([
                'name' => [
                    'Name filter is required.',
                ],
                'books' => [
                    'Books filter is required.',
                ],
                'books2.title' => [
                    'Title filter is required.',
                ],
                'imageable' => [
                    'Imageable filter is required.',
                ],
                '$latest' => [
                    '$latest filter is required.',
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

it('allows specifying required failed validation message', function (): void {

    config()->set('eloquent-filtering.custom_filters', [LatestFilter::class]);

    try {
        Author::filter(
            [], // Filters intentionally empty.
            Filter::only(
                Filter::field('name', [FilterType::LIKE])->required(message: '1 is required'),
                Filter::relation(
                    'books',
                    [FilterType::HAS],
                    Filter::only(
                        Filter::field('title', [FilterType::LIKE])->required(message: '2 is required'),
                    )
                )->required(message: '3 is required'),
                Filter::morphRelation(
                    'imageable',
                    [FilterType::HAS_MORPH],
                    Filter::morphType(
                        'articles',
                        Filter::only(
                            Filter::field('title', [FilterType::LIKE])->required(message: '4 is required')
                        )
                    )->required(message: '5 is required')
                )->required(message: '6 is required'),
                Filter::custom('$latest')->required(message: '7 is required')
            )
        );

        $this->fail('Should have thrown exception');

    } catch (RequiredFilterException $rfe) {
        expect($rfe->getMessage())->toBe('1 is required (and 6 more errors)')
            ->and($rfe->errors())->toBe([
                'name' => [
                    '1 is required',
                ],
                'books' => [
                    '3 is required',
                ],
                'books.title' => [
                    '2 is required',
                ],
                'imageable' => [
                    '6 is required',
                ],
                'imageable.articles' => [
                    '5 is required',
                ],
                'imageable.articles.title' => [
                    '4 is required',
                ],
                '$latest' => [
                    '7 is required',
                ],
            ]);
    }

});

it('suppressed exceptions still cause RequiredFilterException to be thrown', function (): void {

    $this->setSuppression("filter.malformed_format", true);

    try {
        Author::filter(
            [
                [
                    'target' => 'name',
                    'type'   => '$like',
                    'value'  => 'George',
                ]
            ],
            Filter::only(
                Filter::field('name', [FilterType::LIKE->withValidation([
                    'value' => ['size:100']
                ])])->required(),
            )
        );

        $this->fail('Should have thrown exception');

    } catch (RequiredFilterException $rfe) {
        expect($rfe->getMessage())->toBe('Name filter is required.')
            ->and($rfe->errors())->toBe([
                'name' => [
                    'Name filter is required.',
                ],
            ]);
    }

});
