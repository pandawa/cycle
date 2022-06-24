<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Contract;

use Cycle\ORM\Schema;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface SchemaManager
{
    public function createSchema(): Schema;

    public function flushSchemaCache(): void;
}
