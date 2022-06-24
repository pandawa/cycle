<?php

declare(strict_types=1);

namespace Pandawa\Cycle;

use Cycle\Database\DatabaseManager;
use Cycle\Database\DatabaseProviderInterface;
use Cycle\ORM\Entity\Behavior\EventDrivenCommandGenerator;
use Cycle\ORM\Factory;
use Cycle\ORM\FactoryInterface;
use Cycle\ORM\ORM;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\SchemaInterface;
use Cycle\ORM\Transaction\CommandGeneratorInterface;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Foundation\Application;
use Pandawa\Component\Module\AbstractModule;
use Pandawa\Cycle\Contract\SchemaManager as SchemaManagerContract;
use Pandawa\Cycle\Contract\SchemaResourceLocator as SchemaResourceLocatorContract;
use Pandawa\Cycle\Factory\DatabaseConfigFactory;
use Pandawa\Cycle\Locator\SchemaResourceLocator;
use Spiral\Tokenizer\ClassLocator;
use Spiral\Tokenizer\Config\TokenizerConfig;
use Spiral\Tokenizer\Tokenizer;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class PandawaCycleModule extends AbstractModule
{
    protected function build(): void
    {
        if (!$this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__ . '/../configs/cycle.php', 'cycle');
        }

        $this->publishes([
            __DIR__ . '/../config/cycle.php' => config_path('cycle.php'),
        ], 'config');
    }

    protected function init(): void
    {
        $this->registerDatabaseManager();
        $this->registerClassLocator();
        $this->registerSchemaManager();
        $this->registerORM();
    }

    protected function registerDatabaseManager(): void
    {
        $this->app->singleton(DatabaseProviderInterface::class, fn (Application $app) => new DatabaseManager(
            $app[DatabaseConfigFactory::class]->createFromLaravelConfig(
                $app['config']['database']
            )
        ));

        $this->app->alias(DatabaseProviderInterface::class, DatabaseManager::class);
    }

    protected function registerClassLocator(): void
    {
        $this->app->singleton(ClassLocator::class, function (Application $app) {
            $tokenizer = new Tokenizer(new TokenizerConfig([
                'directories' => $app['config']->get('cycle.directories', [
                    base_path('src/*/*/*/Entity/'),
                ]),
                'exclude' => (array) $app['config']['cycle.exclude'],
                'scopes' => (array) $app['config']['cycle.scopes'],
            ]));

            return $tokenizer->classLocator();
        });
    }

    protected function registerSchemaManager(): void
    {
        $this->app->singleton(SchemaResourceLocatorContract::class, fn (Application $app) => new SchemaResourceLocator(
            $this->loader,
            $app['config']['cycle.schema.resource_path'] ?? base_path('src/*/*/*/Resources/schemas'),
        ));

        $this->app->singleton(SchemaManagerContract::class, fn (Application $app) => new SchemaManager(
            $app[ClassLocator::class],
            $app[DatabaseProviderInterface::class],
            $app[CacheFactory::class]->store($app['config']->get(
                'cycle.scheme.cache.storage',
                $app['config']['cache.default']
            )),
            $app[SchemaResourceLocatorContract::class],
            (array) $app['config']['cycle.schema.defaults'],
            (bool) $app['config']['cycle.schema.cache.enabled'],
        ));
    }

    protected function registerORM(): void
    {
        $this->app->singleton(
            SchemaInterface::class,
            fn(Application $app) => $app[SchemaManagerContract::class]->createSchema(),
        );

        $this->app->singleton(FactoryInterface::class, fn (Application $app) => new Factory(
            $app[DatabaseProviderInterface::class]
        ));

        $this->app->singleton(CommandGeneratorInterface::class, fn($app) => new EventDrivenCommandGenerator(
            $app[SchemaInterface::class],
            $app
        ));

        $this->app->singleton(ORMInterface::class, fn (Application $app) => new ORM(
            $app[FactoryInterface::class],
            $app[SchemaInterface::class],
            $app[CommandGeneratorInterface::class]
        ));
    }
}
