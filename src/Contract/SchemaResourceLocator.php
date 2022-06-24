<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Contract;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface SchemaResourceLocator
{
    public function compile(array $defaults = []): array;
}
