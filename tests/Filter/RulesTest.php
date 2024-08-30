<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Filter\FilterType;
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
        )
    );

})->throws(MalformedFilterFormatException::class, '"$between" filter does not match required format.');

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
            )
        );

        $this->fail('Should have thrown exception');

    } catch (MalformedFilterFormatException $mffe) {
        expect($mffe->getMessage())->toBe('"$between" filter does not match required format. (and 2 more errors)')
            ->and($mffe->errors())->toBe([
                'filter' => [
                    '"$between" filter does not match required format.',
                ],
                'value.0' => [
                    'first date MUST BE A DATE',
                ],
                'value.1' => [
                    'second date MUST BE A DATE',
                ],
            ]);
    }

});
