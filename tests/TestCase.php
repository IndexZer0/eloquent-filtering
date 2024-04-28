<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use IndexZer0\EloquentFiltering\EloquentFilteringServiceProvider;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\AuthorProfile;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Book;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Comment;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Manufacturer;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Product;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'IndexZer0\\EloquentFiltering\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
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

        // Author, Profile, Books, Comments.
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

        // Manufacturers, Products
        $schema->create('manufacturers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('products', function (Blueprint $table): void {
            $table->id();
            $table->foreignIdFor(Manufacturer::class);
            $table->string('name');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function createAuthors(): void
    {
        Author::create([
            'id'   => 1,
            'name' => 'George Raymond Richard Martin',
        ]);
        AuthorProfile::create([
            'author_id' => 1,
            'age'       => 20,
        ]);
        Book::create([
            'id'          => 1,
            'author_id'   => 1,
            'title'       => 'A Game of Thrones',
            'description' => 'A Game of Thrones is the first novel in A Song of Ice and Fire, a series of fantasy novels by the American author George R. R. Martin.',
        ]);
        Comment::create([
            'book_id' => 1,
            'content' => 'Thanks D&D :S',
        ]);

        Author::create([
            'id'   => 2,
            'name' => 'J. R. R. Tolkien',
        ]);
        AuthorProfile::create([
            'author_id' => 2,
            'age'       => 30,
        ]);
        Book::create([
            'id'          => 2,
            'author_id'   => 2,
            'title'       => 'The Lord of the Rings',
            'description' => 'The Lord of the Rings is an epic high-fantasy novel by the English author and scholar J. R. R. Tolkien.',
        ]);
        Comment::create([
            'book_id' => 2,
            'content' => 'Did you know viggo broke his toe?',
        ]);
    }

    public function createManufacturers(): void
    {
        Manufacturer::create([
            'id'   => 1,
            'name' => 'Manufacturer 1',
        ]);
        Product::create([
            'id'              => 1,
            'manufacturer_id' => 1,
            'name'            => 'Product 1',
        ]);

        Manufacturer::create([
            'id'   => 2,
            'name' => 'Manufacturer 2',
        ]);
        Product::create([
            'id'              => 2,
            'manufacturer_id' => 2,
            'name'            => 'Product 2',
        ]);
    }
}
