<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Filter\Exceptions\MalformedFilterFormatException;
use IndexZer0\EloquentFiltering\Filter\Filterable\Filter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\CustomFilters\ValidatorProviderFilter;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;

it('works when ::format() returns a ValidatorProvider', function (): void {

    config()->set('eloquent-filtering.custom_filters', [ValidatorProviderFilter::class]);

    try {
        Author::filter(
            [
                [
                    'type'  => '$validatorProvider',
                    'value' => 1,
                ],
            ],
            Filter::only(
                Filter::field('name', ['$validatorProvider']),
            )
        );

        $this->fail('Should have thrown exception');

    } catch (MalformedFilterFormatException $mffe) {

        expect($mffe->getMessage())->toBe('"$validatorProvider" filter does not match required format. (and 2 more errors)')
            ->and($mffe->errors())->toBe([
                'filter' => [
                    '"$validatorProvider" filter does not match required format.',
                ],
                'value' => [
                    'attribute_value IS NOT A STRING',
                ],
                'target' => [
                    'The target field is required.',
                ],
            ]);
    }

});
