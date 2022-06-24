<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Factory;

use Cycle\Database\Config\DatabaseConfig;
use Pandawa\Cycle\Adapter\DatabaseConfigManager;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DatabaseConfigFactory
{
    public function __construct(private readonly DatabaseConfigManager $configManager)
    {
    }

    public function createFromLaravelConfig(array $laravelDatabaseConfig): DatabaseConfig
    {
        return new DatabaseConfig([
            'databases'   => [
                'default' => [
                    'connection' => $laravelDatabaseConfig['default'],
                    'prefix'     => $laravelDatabaseConfig['prefix'] ?? '',
                ],
            ],
            'connections' => $this->getConnections($laravelDatabaseConfig['connections']),
        ]);
    }

    private function getConnections(array $laravelConnections): array
    {
        $cycleConnections = [];

        foreach ($laravelConnections as $key => $config) {
            if (!$this->configManager->has($driver = $config['driver'])) {
                continue;
            }

            $cycleConnections[$key] = $this->configManager->getConfig($driver, $config);
        }

        return $cycleConnections;
    }
}
