<?php

declare(strict_types=1);

namespace Pandawa\Cycle;

use Cycle\Annotated;
use Cycle\Database\DatabaseManager;
use Cycle\ORM\Schema;
use Cycle\Schema\Compiler;
use Cycle\Schema\Generator;
use Cycle\Schema\Registry;
use Illuminate\Contracts\Cache\Repository;
use Pandawa\Cycle\Contract\SchemaManager as SchemaManagerContract;
use Pandawa\Cycle\Contract\SchemaResourceLocator;
use Spiral\Tokenizer\ClassLocator;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class SchemaManager implements SchemaManagerContract
{
    const SCHEMA_CACHE_KEY = 'cycle.schema';

    public function __construct(
        private readonly ClassLocator $classLocator,
        private readonly DatabaseManager $databaseManager,
        private readonly Repository $cache,
        private readonly SchemaResourceLocator $resourceLocator,
        private readonly array $schemaDefaults,
        private readonly bool $schemaCached,
    ) {
    }

    public function createSchema(): Schema
    {
        return new Schema($this->getSchema());
    }

    public function flushSchemaCache(): void
    {
        $this->cache->forget(self::SCHEMA_CACHE_KEY);
    }

    protected function getSchema(): array
    {
        if (!$this->schemaCached) {
            return $this->createSchemaData();
        }

        return $this->cache->rememberForever(
            self::SCHEMA_CACHE_KEY,
            fn() => $this->createSchemaData(),
        );
    }

    protected function createSchemaData(): array
    {
        return [
            ...(new Compiler())->compile(
                new Registry($this->databaseManager),
                $this->getSchemaGenerators(),
                $this->schemaDefaults,
            ),
            ...($this->resourceLocator->compile($this->schemaDefaults)),
        ];
    }

    protected function getSchemaGenerators(): array
    {
        return [
            new Generator\ResetTables(),
            new Annotated\Embeddings($this->classLocator),
            new Annotated\Entities($this->classLocator),
            new Annotated\TableInheritance(),
            new Annotated\MergeColumns(),
            new Generator\GenerateRelations(),
            new Generator\GenerateModifiers(),
            new Generator\ValidateEntities(),
            new Generator\RenderTables(),
            new Generator\RenderRelations(),
            new Generator\RenderModifiers(),
            new Annotated\MergeIndexes(),
            new Generator\SyncTables(),
            new Generator\GenerateTypecast(),
        ];
    }
}
