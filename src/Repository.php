<?php

declare(strict_types=1);

namespace Pandawa\Cycle;

use Cycle\ORM\EntityManager;
use Cycle\ORM\EntityManagerInterface;
use Cycle\ORM\ORMInterface;
use Cycle\ORM\Select;
use Cycle\ORM\Select\Repository as CycleRepository;
use Illuminate\Support\Collection;
use Pandawa\Cycle\Contract\Repository as RepositoryContract;
use Throwable;

/**
 * @template T
 *
 * @author   Iqbal Maulana <iq.bluejack@gmail.com>
 */
abstract class Repository extends CycleRepository implements RepositoryContract
{
    protected EntityManagerInterface $em;

    public function __construct(Select $select, ORMInterface $orm)
    {
        parent::__construct($select);

        $this->em = new EntityManager($orm);
    }

    /**
     * @param mixed $id
     *
     * @return T|null
     */
    public function findByPK(mixed $id): ?object
    {
        return parent::findByPK($id);
    }

    /**
     * @param array $scope
     *
     * @return T|null
     */
    public function findOne(array $scope = []): ?object
    {
        return parent::findOne($scope);
    }

    /**
     * @param array $scope
     * @param array $orderBy
     *
     * @return Collection<int, T>
     */
    public function findAll(array $scope = [], array $orderBy = []): Collection
    {
        return $this->newCollection(
            parent::findAll($scope, $orderBy)
        );
    }

    /**
     * @param T    $entity
     * @param bool $cascade
     * @param bool $run
     *
     * @return void
     * @throws Throwable
     */
    public function save(mixed $entity, bool $cascade = true, bool $run = true): void
    {
        $this->em->persist($entity, $cascade);

        if ($run) {
            $this->em->run();
        }
    }

    /**
     * @param T    $entity
     * @param bool $cascade
     * @param bool $run
     *
     * @return void
     * @throws Throwable
     */
    public function delete(mixed $entity, bool $cascade = true, bool $run = true): void
    {
        $this->em->delete($entity, $cascade);

        if ($run) {
            $this->em->run();
        }
    }

    protected function newCollection(iterable $items): Collection
    {
        return new Collection($items);
    }
}
