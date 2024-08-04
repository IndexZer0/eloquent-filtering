<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\RequiredFilterException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters\LatestFilter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('throws RequiredFilterException when required filters have not been matched', function (): void {

    try {
        Author::filter(
            [], // Filters intentionally empty.
            Filter::only(
                Filter::field('name', ['$like'])->required(),
                Filter::relation(
                    'books',
                    ['$has'],
                    Filter::only(
                        Filter::field('title', ['$like'])->required()
                    )
                )->required(),
                Filter::custom(['$latest'])->required()
            )
        );

        $this->fail('Should have thrown exception');

    } catch (RequiredFilterException $rfe) {
        expect($rfe->getMessage())->toBe('"name" filter is required. (and 3 more errors)')
            ->and($rfe->errors())->toBe([
                '"name" filter' => [
                    '"name" filter is required.',
                ],
                '"books" filter' => [
                    '"books" filter is required.',
                ],
                '"title" filter' => [
                    '"title" filter is required.',
                ],
                'custom: TODO' => [
                    'custom: TODO is required.',
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
                    'type' => '$latest',
                ],
            ],
            Filter::only(
                Filter::field('name', ['$like'])->required(),
                Filter::relation(
                    'books',
                    ['$has'],
                    Filter::only(
                        Filter::field('title', ['$like'])->required()
                    )
                )->required(),
                Filter::custom(['$latest'])->required()
            )
        );

        $this->assertTrue(true);

    } catch (RequiredFilterException $rfe) {
        $this->fail('Should not have thrown exception');
    }

});
