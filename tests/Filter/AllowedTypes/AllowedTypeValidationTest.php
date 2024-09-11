<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\AllowedTypes\AllowedType;
use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
use IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters\LatestFilter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Event;

it('can set validation rules', function (): void {

    Event::filter(
        [
            [
                'target' => 'starting_at',
                'type'   => '$between',
                'value'  => [
                    '2024-01-05', '2024-01-01',
                ],
            ],
        ],
        Filter::only(
            Filter::field('starting_at', [FilterType::BETWEEN->withValidation([
                'value.0' => ['date', 'before:value.1'],
                'value.1' => ['date', 'after:value.0'],
            ])]),
        ),
    );

})->throws(MalformedFilterFormatException::class, 'Starting_at filter does not match required format. (and 2 more errors)');

it('can set validation messages and attributes', function (): void {

    try {
        Event::filter(
            [
                [
                    'target' => 'starting_at',
                    'type'   => '$between',
                    'value'  => [
                        '1', '2',
                    ],
                ],
            ],
            Filter::only(
                Filter::field('starting_at', [FilterType::BETWEEN->withValidation([
                    'value.0' => ['date', ],
                    'value.1' => ['date', ],
                ], [
                    'date' => ':attribute MUST BE A DATE',
                ], [
                    'value.0' => 'first date',
                    'value.1' => 'second date',
                ])]),
            ),
        );

        $this->fail('Should have thrown exception');

    } catch (MalformedFilterFormatException $mffe) {
        expect($mffe->getMessage())->toBe('Starting_at filter does not match required format. (and 2 more errors)')
            ->and($mffe->errors())->toBe([
                'starting_at' => [
                    'Starting_at filter does not match required format.',
                ],
                'starting_at.value.0' => [
                    'first date MUST BE A DATE',
                ],
                'starting_at.value.1' => [
                    'second date MUST BE A DATE',
                ],
            ]);
    }

});

it('can set validation rules for custom filter', function (): void {

    config()->set('eloquent-filtering.custom_filters', [LatestFilter::class]);

    Event::filter(
        [
            [
                'type' => '$latest',
            ],
        ],
        Filter::only(
            Filter::custom((new AllowedType('$latest'))->withValidation([
                'value' => ['required'],
            ])),
        ),
    );

})->throws(MalformedFilterFormatException::class, '$latest filter does not match required format. (and 1 more error)');
