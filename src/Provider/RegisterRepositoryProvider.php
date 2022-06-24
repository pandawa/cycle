<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Provider;

use Cycle\Annotated\ReaderFactory;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Schema;
use Illuminate\Foundation\Application;
use Pandawa\Component\Loader\ChainLoader;
use Pandawa\Cycle\Annotation\Repository;
use ReflectionClass;
use Spiral\Attributes\ReaderInterface;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

/**
 * @property Application $app
 * @property ChainLoader $loader
 *
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
trait RegisterRepositoryProvider
{
    protected string $entitySchemaPath = 'Resources/schemas';

    protected function registerRegisterRepositoryProvider(): void
    {
        $reader = ReaderFactory::create();

        foreach ($this->getConfigFiles() as $file) {
            $this->registerRepositoryBySchema($this->loadConfig($file), $reader);
        }
    }

    private function loadConfig(SplFileInfo $file): array
    {
        return $this->loader->load($file->getRealPath());
    }

    private function getConfigFiles(): iterable
    {
        if (is_dir($schemaPath = $this->getSchemaPath())) {
            return Finder::create()->in($schemaPath)->files();
        }

        return [];
    }

    private function getSchemaPath(): string
    {
        return $this->getCurrentPath().'/'.trim($this->entitySchemaPath, '/');
    }

    private function registerRepositoryBySchema(array $schema, ReaderInterface $reader): void
    {
        if ($repoClass = $schema[Schema::REPOSITORY] ?? null) {
            $this->app->singleton(
                $repoClass,
                fn(Application $app) => $app[ORMInterface::class]->getRepository($schema[Schema::ENTITY]),
            );

            $ann = $reader->firstClassMetadata(new ReflectionClass($repoClass), Repository::class);

            if (null !== $aliases = $ann->getAliases()) {
                foreach ($aliases as $alias) {
                    $this->app->alias($repoClass, $alias);
                }
            }
        }
    }
}
