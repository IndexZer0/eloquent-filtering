<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering;

use IndexZer0\EloquentFiltering\Console\Commands\EloquentFilterMakeCommand;
use IndexZer0\EloquentFiltering\Filter\AvailableFilters;
use IndexZer0\EloquentFiltering\Sort\Contracts\SortValidator as SortValidatorContract;
use IndexZer0\EloquentFiltering\Sort\SortValidator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterApplier as FilterApplierContract;
use IndexZer0\EloquentFiltering\Filter\Contracts\FilterParser as FilterParserContract;
use IndexZer0\EloquentFiltering\Filter\FilterApplier;
use IndexZer0\EloquentFiltering\Filter\FilterParser;

class EloquentFilteringServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('eloquent-filtering')
            ->hasConfigFile()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->publishConfigFile()
                    ->askToStarRepoOnGitHub('IndexZer0/eloquent-filtering');
            })
            ->hasCommand(EloquentFilterMakeCommand::class);
    }

    public function registeringPackage(): void
    {
        $this->app->bind(FilterParserContract::class, FilterParser::class);
        $this->app->bind(FilterApplierContract::class, FilterApplier::class);
        $this->app->singleton(AvailableFilters::class, AvailableFilters::class);

        $this->app->bind(SortValidatorContract::class, SortValidator::class);
    }
}
