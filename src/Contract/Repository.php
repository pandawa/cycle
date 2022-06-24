<?php

declare(strict_types=1);

namespace Pandawa\Cycle\Contract;

use Illuminate\Support\Collection;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface Repository
{
    public function findByPK($id): ?object;

    public function findOne(array $criteria): ?object;

    public function findAll(array $scope = [], array $orderBy = []): Collection;

    public function save(mixed $entity, bool $cascade = true, bool $run = true): void;

    public function delete(mixed $entity, bool $cascade = true, bool $run = true): void;
}
