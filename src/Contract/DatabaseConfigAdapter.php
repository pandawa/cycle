<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Contract;

use Cycle\Database\Config\DriverConfig;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface DatabaseConfigAdapter
{
    public function getDriverName(): string;

    public function getConfig(array $config): DriverConfig;
}
