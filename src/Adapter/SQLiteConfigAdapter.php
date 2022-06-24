<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Adapter;

use Cycle\Database\Config\DriverConfig;
use Cycle\Database\Config\SQLite\ConnectionConfig;
use Cycle\Database\Config\SQLite\FileConnectionConfig;
use Cycle\Database\Config\SQLite\MemoryConnectionConfig;
use Cycle\Database\Config\SQLiteDriverConfig;
use Pandawa\Cycle\Contract\DatabaseConfigAdapter;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class SQLiteConfigAdapter implements DatabaseConfigAdapter
{
    public function getDriverName(): string
    {
        return 'sqlite';
    }

    public function getConfig(array $config): DriverConfig
    {
        return new SQLiteDriverConfig(
            connection: $this->getConnection($config['database'], $config['options'] ?? []),
            timezone: $config['timezone'] ?? 'UTC',
            queryCache: $config['query_cache'] ?? false,
        );
    }

    private function getConnection(string $database, array $options = []): ConnectionConfig
    {
        return match ($database) {
            ':memory:' => new MemoryConnectionConfig($options),
            default => new FileConnectionConfig(
                database: $database,
                options: $options
            )
        };
    }
}
