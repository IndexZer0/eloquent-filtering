<?php

declare(strict_types=1);

use IndexZer0\EloquentFiltering\Console\Commands\EloquentFilterMakeCommand;

it('can create "field" filter', function (): void {

    $this->artisan(EloquentFilterMakeCommand::class, [
        'name'   => 'WhereFilter',
        '--type' => 'field',
    ])->assertSuccessful();

    $this->assertFileContains([
        'namespace App\FilterMethods;',
        'use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractFieldFilter;',
        'class WhereFilter extends AbstractFieldFilter',
        'return \'$where\';',
    ], 'app/FilterMethods/WhereFilter.php');

});

it('can create "custom" filter', function (): void {

    $this->artisan(EloquentFilterMakeCommand::class, [
        'name'   => 'SpecialCustomFilter',
        '--type' => 'custom',
    ])->assertSuccessful();

    $this->assertFileContains([
        'namespace App\FilterMethods;',
        'use IndexZer0\EloquentFiltering\Filter\FilterMethods\Abstract\AbstractCustomFilter;',
        'class SpecialCustomFilter extends AbstractCustomFilter',
        'return \'$specialCustom\';',
    ], 'app/FilterMethods/SpecialCustomFilter.php');

});
