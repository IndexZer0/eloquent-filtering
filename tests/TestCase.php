<?php

namespace IndexZer0\EloquentFiltering\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use IndexZer0\EloquentFiltering\EloquentFilteringServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'IndexZer0\\EloquentFiltering\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );

        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [
            EloquentFilteringServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('app.env', 'local');
        config()->set('database.default', 'sqlite');
        config()->set('database.connections.sqlite', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        /*
        $migration = include __DIR__.'/../database/migrations/create_eloquent-filtering_table.php.stub';
        $migration->up();
        */
    }

    protected function setUpDatabase($app): void
    {
        $schema = $app['db']->connection()->getSchemaBuilder();

        $schema->create('authors', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        $schema->create('author_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Author::class);
            $table->tinyInteger('age')->nullable();
            $table->timestamps();
        });

        $schema->create('books', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Author::class);
            $table->string('title');
            $table->string('description');
            $table->timestamps();
        });

        $schema->create('comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Book::class);
            $table->string('content');
            $table->timestamps();
        });

        $schema->create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Manufacturer::class);
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        $schema->create('manufacturers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

    }
}
