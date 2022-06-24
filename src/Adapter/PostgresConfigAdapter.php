<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Adapter;

use Cycle\Database\Config\DriverConfig;
use Cycle\Database\Config\Postgres\TcpConnectionConfig;
use Cycle\Database\Config\PostgresDriverConfig;
use Pandawa\Cycle\Contract\DatabaseConfigAdapter;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class PostgresConfigAdapter implements DatabaseConfigAdapter
{
    public function getDriverName(): string
    {
        return 'pgsql';
    }

    public function getConfig(array $config): DriverConfig
    {
        return new PostgresDriverConfig(
            connection: new TcpConnectionConfig(
                database: $config['database'],
                host: $config['host'],
                port: (int) $config['port'],
                user: $config['username'],
                password: $config['password'],
                options: $config['options'] ?? [],
            ),
            schema: $config['search_path'],
            timezone: $config['timezone'] ?? 'UTC',
            queryCache: $config['query_cache'] ?? false,
        );
    }
}
