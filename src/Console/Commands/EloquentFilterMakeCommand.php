<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'make:eloquent-filter')]
class EloquentFilterMakeCommand extends GeneratorCommand
{
    protected $description = 'Create a new eloquent filter class';

    protected function getArguments(): array
    {
        return [
            ['name', InputOption::VALUE_REQUIRED, 'The name of the filter to make'],
        ];
    }

    protected function getOptions(): array
    {
        return [
            ['type', 't', InputOption::VALUE_REQUIRED, 'The type of filter to make'],
        ];
    }

    protected function replaceClass($stub, $name)
    {
        $stub = parent::replaceClass($stub, $name);

        $name = Str::of($this->getNameInput())->beforeLast('Filter')->lcfirst()->toString();

        return str_replace(['{{ type }}'], $name, $stub);
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace . '\FilterMethods';
    }

    protected function getStub(): string
    {
        $type = $this->option('type');
        return __DIR__ . "/../stubs/{$type}.stub";
    }
}
