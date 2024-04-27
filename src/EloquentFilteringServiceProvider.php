<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering;

use IndexZer0\EloquentFiltering\Filter\AvailableFilters;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use IndexZer0\EloquentFiltering\Commands\EloquentFilteringCommand;
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
            ->publishesServiceProvider('EloquentFilteringServiceProvider')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command->publishConfigFile()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('IndexZer0/eloquent-filtering');
            })
            /*->hasCommand(EloquentFilteringCommand::class)*/; // TODO custom filters command.
    }

    public function registeringPackage(): void
    {
        $this->app->bind(FilterParserContract::class, FilterParser::class);
        $this->app->bind(FilterApplierContract::class, FilterApplier::class);
        $this->app->singleton(AvailableFilters::class, AvailableFilters::class);
    }
}
