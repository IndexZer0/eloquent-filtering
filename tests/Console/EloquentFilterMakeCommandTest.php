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
        'class WhereFilter implements FilterMethod, Targetable',
        'use FieldFilter;',
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
        'class SpecialCustomFilter implements FilterMethod',
        'return \'$specialCustom\';',
    ], 'app/FilterMethods/SpecialCustomFilter.php');

});
