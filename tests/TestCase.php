<?php

declare(strict_types=1);

namespace IndexZer0\EloquentFiltering\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Schema\Blueprint;
use IndexZer0\EloquentFiltering\EloquentFilteringServiceProvider;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Documentation\Package;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ApiResponse;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Author;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\AuthorProfile;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Book;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Comment;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\Contract;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\File;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\Account;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\WithoutMorphMap\FoodDeliveryService;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\WithoutMorphMap\Sass;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\IncludeRelationFields\Morph\WithoutMorphMap\Subscription;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Manufacturer;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ManyToManyMorph\Epic;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ManyToManyMorph\Issue;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\ManyToManyMorph\Label;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\Article;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\Image;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\UserProfile;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Business;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Client;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Morph\WithoutMorphMap\Invoice;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\Post;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Pivot\Tag;
use IndexZer0\EloquentFiltering\Tests\TestingResources\Models\Product;
use Orchestra\Testbench\Concerns\InteractsWithPublishedFiles;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use InteractsWithPublishedFiles;

    protected $files = [
        'app/FilterMethods/WhereFilter.php',
        'app/FilterMethods/SpecialCustomFilter.php',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'IndexZer0\\EloquentFiltering\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );

        Relation::morphMap([
            // Morph
            Article::class,
            UserProfile::class,

            // Morph (Include Relation Fields)
            Contract::class,
            Account::class,

            // Many To Many Morph
            Epic::class,
            Issue::class,
        ]);

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
            $table->integer('price')->nullable();
            $table->integer('min_allowed_price')->nullable();
            $table->integer('max_allowed_price')->nullable();
            $table->timestamps();
        });

        // Api Response
        $schema->create('api_responses', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->json('data')->nullable();
            $table->timestamps();
        });

        // Show
        $schema->create('shows', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('organizer_name')->nullable();
            $table->timestamps();
        });

        // Event
        $schema->create('events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('show_id')->constrained();
            $table->dateTime('starting_at')->nullable();
            $table->dateTime('finishing_at')->nullable();
            $table->smallInteger('audience_limit');
            $table->timestamps();
        });

        // Ticket
        $schema->create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained();
            $table->string('type')->nullable(); // Standard, Premium
            $table->integer('price')->nullable();
            $table->timestamps();
        });

        // Pivot
        $schema->create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
        $schema->create('posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
        $schema->create('tags', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('post_tag', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('post_id')->constrained();
            $table->foreignId('tag_id')->constrained();
            $table->string('tagged_by');
            $table->timestamps();
        });
        $schema->create('blog_post_tag', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('blog_post_id')->constrained();
            $table->foreignId('tag_id')->constrained();
            $table->string('tagged_by');
            $table->timestamps();
        });

        // Morph
        $schema->create('articles', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
        $schema->create('user_profiles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('images', function (Blueprint $table): void {
            $table->id();
            $table->string('url');
            $table->morphs('imageable');
            $table->timestamps();
        });

        // Morph (Include Relation Fields)
        $schema->create('contracts', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->timestamps();
        });
        $schema->create('accounts', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('files', function (Blueprint $table): void {
            $table->id();
            $table->string('url');
            $table->morphs('fileable');
            $table->timestamps();
        });

        // Morph (Without Morph Map)
        $schema->create('clients', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('businesses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('amount');
            $table->morphs('invoiceable');
            $table->timestamps();
        });

        // Morph (Without Morph Map & Include Relation Fields)
        $schema->create('sasses', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('food_delivery_services', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('amount');
            $table->morphs('subscribable');
            $table->timestamps();
        });

        // Many To Many Morph
        $schema->create('epics', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('issues', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('labels', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });
        $schema->create('labelables', function (Blueprint $table): void {
            $table->foreignId('label_id');
            $table->morphs('labelable');
            $table->string('labeled_by');
        });

        // Package
        $schema->create('packages', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('version');
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
            'id'                => 1,
            'manufacturer_id'   => 1,
            'name'              => 'Product 1',
            'price'             => 20,
            'min_allowed_price' => 10,
            'max_allowed_price' => 30,
        ]);

        Manufacturer::create([
            'id'   => 2,
            'name' => 'Manufacturer 2',
        ]);
        Product::create([
            'id'                => 2,
            'manufacturer_id'   => 2,
            'name'              => 'Product 2',
            'price'             => 25,
            'min_allowed_price' => 10,
            'max_allowed_price' => 30,
        ]);
    }

    public function createApiResponses(): void
    {
        ApiResponse::create([
            'id'   => 1,
            'name' => 'Api 1',
            'data' => [
                'array' => [
                    'shared-array-value-1',
                    'shared-array-value-2',
                    'own-array-value-1',
                    'own-array-value-2',
                ],

                'shared-key-1' => 'shared-value-1',
                'shared-key-2' => 'shared-value-2',
                'shared-key-3' => 'Api 1',

                'own-key-1' => 'own-value-1',
                'own-key-2' => 'own-value-2',
                'own-key-3' => 'own-value-3',
            ],
        ]);
        ApiResponse::create([
            'id'   => 2,
            'name' => 'Api 2',
            'data' => [
                'array' => [
                    'shared-array-value-1',
                    'shared-array-value-2',
                    'own-array-value-3',
                    'own-array-value-4',
                ],

                'shared-key-1' => 'shared-value-1',
                'shared-key-2' => 'shared-value-2',
                'shared-key-3' => 'Api 2',

                'own-key-4' => 'own-value-4',
                'own-key-5' => 'own-value-5',
                'own-key-6' => 'own-value-6',
            ],
        ]);
    }

    public function createPostTagAndPivotRecords(): void
    {
        $p1 = new Post(['title' => 'post-title-1']);
        $p1->save();
        $t1 = new Tag(['name' => 'tag-name-1']);
        $t1->save();
        $p1->tags()->save($t1, ['tagged_by' => 'tagged-by-user-1']);

        $p2 = new Post(['title' => 'post-title-2']);
        $p2->save();
        $t2 = new Tag(['name' => 'tag-name-2']);
        $t2->save();
        $p2->tags()->save($t2, ['tagged_by' => 'tagged-by-user-2']);
    }

    public function createMorphRecords(): void
    {
        $article1 = Article::create([
            'title' => 'article-1',
        ]);
        $article1->images()->save(
            $image1 = new Image([
                'url' => 'image-1',
            ])
        );
        $article1->images()->save(
            $image2 = new Image([
                'url' => 'image-2',
            ])
        );

        $article2 = Article::create([
            'title' => 'article-2',
        ]);
        $article2->images()->save(
            $image3 = new Image([
                'url' => 'image-3',
            ])
        );
        $article2->images()->save(
            $image4 = new Image([
                'url' => 'image-4',
            ])
        );

        $userProfile1 = UserProfile::create([
            'name' => 'user-profile-1',
        ]);
        $userProfile1->images()->save(
            $image5 = new Image([
                'url' => 'image-5',
            ])
        );
        $userProfile1->images()->save(
            $image6 = new Image([
                'url' => 'image-6',
            ])
        );

        $userProfile2 = UserProfile::create([
            'name' => 'user-profile-2',
        ]);
        $userProfile2->images()->save(
            $image7 = new Image([
                'url' => 'image-7',
            ])
        );
        $userProfile2->images()->save(
            $image8 = new Image([
                'url' => 'image-8',
            ])
        );
    }

    public function createMorphRecordsForIncludeRelationFields(): void
    {
        $contract1 = Contract::create([
            'title' => 'contract-1',
        ]);
        $contract1->files()->save(
            $file1 = new File([
                'url' => 'file-1',
            ])
        );
        $contract1->files()->save(
            $file2 = new File([
                'url' => 'file-2',
            ])
        );

        $contract2 = Contract::create([
            'title' => 'contract-2',
        ]);
        $contract2->files()->save(
            $file3 = new File([
                'url' => 'file-3',
            ])
        );
        $contract2->files()->save(
            $file4 = new File([
                'url' => 'file-4',
            ])
        );

        $account1 = Account::create([
            'name' => 'account-1',
        ]);
        $account1->files()->save(
            $file5 = new File([
                'url' => 'file-5',
            ])
        );
        $account1->files()->save(
            $file6 = new File([
                'url' => 'file-6',
            ])
        );

        $account2 = Account::create([
            'name' => 'account-2',
        ]);
        $account2->files()->save(
            $file7 = new File([
                'url' => 'file-7',
            ])
        );
        $account2->files()->save(
            $file8 = new File([
                'url' => 'file-8',
            ])
        );
    }

    public function createMorphRecordsForWithoutMorphMap(): void
    {
        $business1 = Business::create([
            'name' => 'business-1',
        ]);
        $business1->invoices()->save(
            $invoice1 = new Invoice([
                'amount' => 1,
            ])
        );
        $business1->invoices()->save(
            $invoice2 = new Invoice([
                'amount' => 2,
            ])
        );

        $business2 = Business::create([
            'name' => 'business-2',
        ]);
        $business2->invoices()->save(
            $invoice3 = new Invoice([
                'amount' => 3,
            ])
        );
        $business2->invoices()->save(
            $invoice4 = new Invoice([
                'amount' => 4,
            ])
        );

        $client1 = Client::create([
            'name' => 'client-1',
        ]);
        $client1->invoices()->save(
            $invoice5 = new Invoice([
                'amount' => 5,
            ])
        );
        $client1->invoices()->save(
            $invoice6 = new Invoice([
                'amount' => 6,
            ])
        );

        $client2 = Client::create([
            'name' => 'client-2',
        ]);
        $client2->invoices()->save(
            $invoice7 = new Invoice([
                'amount' => 7,
            ])
        );
        $client2->invoices()->save(
            $invoice8 = new Invoice([
                'amount' => 8,
            ])
        );
    }

    public function createMorphRecordsForWithoutMorphMapAndIncludeRelationFields(): void
    {
        $foodDeliveryService1 = FoodDeliveryService::create([
            'name' => 'food-delivery-service-1',
        ]);
        $foodDeliveryService1->subscriptions()->save(
            $subscription1 = new Subscription([
                'amount' => 1,
            ])
        );
        $foodDeliveryService1->subscriptions()->save(
            $subscription2 = new Subscription([
                'amount' => 2,
            ])
        );

        $foodDeliveryService2 = FoodDeliveryService::create([
            'name' => 'food-delivery-service-2',
        ]);
        $foodDeliveryService2->subscriptions()->save(
            $subscription3 = new Subscription([
                'amount' => 3,
            ])
        );
        $foodDeliveryService2->subscriptions()->save(
            $subscription4 = new Subscription([
                'amount' => 4,
            ])
        );

        $sass1 = Sass::create([
            'name' => 'sass-1',
        ]);
        $sass1->subscriptions()->save(
            $subscription5 = new Subscription([
                'amount' => 5,
            ])
        );
        $sass1->subscriptions()->save(
            $subscription6 = new Subscription([
                'amount' => 6,
            ])
        );

        $sass2 = Sass::create([
            'name' => 'sass-2',
        ]);
        $sass2->subscriptions()->save(
            $subscription7 = new Subscription([
                'amount' => 7,
            ])
        );
        $sass2->subscriptions()->save(
            $subscription8 = new Subscription([
                'amount' => 8,
            ])
        );
    }

    public function createManyToManyMorphRecords(): void
    {
        $epic1 = Epic::create([
            'name' => 'epic-1',
        ]);
        $epic2 = Epic::create([
            'name' => 'epic-2',
        ]);
        $issue1 = Issue::create([
            'name' => 'issue-1',
        ]);
        $issue2 = Issue::create([
            'name' => 'issue-2',
        ]);
        $label1 = Label::create([
            'name' => 'label-1',
        ]);
        $label2 = Label::create([
            'name' => 'label-2',
        ]);

        $epic1->labels()->save($label1, ['labeled_by' => 'user-1']);
        $issue1->labels()->save($label1, ['labeled_by' => 'user-2']);
        $epic2->labels()->save($label2, ['labeled_by' => 'user-3']);
        $issue2->labels()->save($label2, ['labeled_by' => 'user-4']);
    }

    public function createPackages(): void
    {
        Package::create([
            'id'          => 1,
            'name'        => 'eloquent-filtering',
            'description' => 'Easily filter eloquent models using arrays',
            'version'     => '1.0.0',
        ]);
    }

    protected function setSuppression(string $type, bool $value): void
    {
        config()->set("eloquent-filtering.suppress.{$type}", $value);
    }
}
