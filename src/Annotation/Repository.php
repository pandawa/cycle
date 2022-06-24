<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Annotation;

use Doctrine\Common\Annotations\Annotation\NamedArgumentConstructor;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
#[\Attribute(\Attribute::TARGET_CLASS), NamedArgumentConstructor]
final class Repository
{
    public function __construct(
        private readonly array|string $alias = [],
    ) {
    }

    public function getAliases(): array
    {
        return (array) $this->alias;
    }
}
