<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

abstract class GenericRepository
{
    protected $entityManager;
    protected $repository;

    public function __construct(EntityManagerInterface $entityManager, string $entityClass)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($entityClass);
    }

    public function getAllEntities(): array
    {
        return $this->repository->findAll();
    }

    public function getEntityById(int $id)
    {
        return $this->repository->find($id);
    }
}
