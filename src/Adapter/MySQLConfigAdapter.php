<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Adapter;

use Cycle\Database\Config\DriverConfig;
use Cycle\Database\Config\MySQL\ConnectionConfig;
use Cycle\Database\Config\MySQL\SocketConnectionConfig;
use Cycle\Database\Config\MySQL\TcpConnectionConfig;
use Cycle\Database\Config\MySQLDriverConfig;
use Pandawa\Cycle\Contract\DatabaseConfigAdapter;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class MySQLConfigAdapter implements DatabaseConfigAdapter
{
    public function getDriverName(): string
    {
        return 'mysql';
    }

    public function getConfig(array $config): DriverConfig
    {
        return new MySQLDriverConfig(
            connection: $this->getConnection($config),
            timezone: $config['timezone'] ?? 'UTC',
            queryCache: $config['query_cache'] ?? false,
        );
    }

    private function getConnection(array $config): ConnectionConfig
    {
        if (!empty($config['unix_socket'])) {
            return new SocketConnectionConfig(
                database: $config['database'],
                socket: $config['unix_socket'],
                charset: $config['charset'],
                user: $config['username'],
                password: $config['password'],
                options: $config['options'] ?? [],
            );
        }

        return new TcpConnectionConfig(
            database: $config['database'],
            host: $config['host'],
            port: (int) $config['port'],
            charset: $config['charset'],
            user: $config['username'],
            password: $config['password'],
            options: $config['options'] ?? [],
        );
    }
}
