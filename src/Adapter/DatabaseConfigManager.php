<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Adapter;

use Cycle\Database\Config\DriverConfig;
use InvalidArgumentException;
use Pandawa\Cycle\Contract\DatabaseConfigAdapter;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
class DatabaseConfigManager
{
    /**
     * @var DatabaseConfigAdapter[]
     */
    protected array $configAdapters = [];

    public function __construct($configAdapters = [])
    {
        foreach($configAdapters as $configAdapter) {
            $this->add($configAdapter);
        }
    }

    public function add(DatabaseConfigAdapter $configAdapter): void
    {
        $this->configAdapters[$configAdapter->getDriverName()] = $configAdapter;
    }

    public function has(string $driverName): bool
    {
        return array_key_exists($driverName, $this->configAdapters);
    }

    public function getConfig(string $driverName, array $config): DriverConfig
    {
        if (!$this->has($driverName)) {
            throw new InvalidArgumentException(sprintf('Config adapter "%s" is not found.', $driverName));
        }

        return $this->configAdapters[$driverName]->getConfig($config);
    }
}
