<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use ReflectionClass;

abstract class GenericRepository extends EntityRepository
{
    protected $entityManager;
    protected $repository;

    public function __construct(EntityManagerInterface $entityManager, string $entityClass)
    {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository($entityClass);
        parent::__construct($entityManager, $entityManager->getClassMetadata($entityClass));
    }

    public function getAllEntities(): array
    {
        return $this->repository->findAll();
    }

    public function getEntityById(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * Updates the entity's attributes based on the DTO values.
     *
     * @param object $entity The entity to update.
     * @param object $dto The DTO containing the new values.
     * @return void
     */
    protected function updateEntityFromDTO($entity, $dto, $excludedProperties): void
    {
        $entityReflection = new ReflectionClass($entity);
        $dtoReflection = new ReflectionClass($dto);

        foreach ($dtoReflection->getProperties() as $dtoProperty) {
            $dtoProperty->setAccessible(true);
            $value = $dtoProperty->getValue($dto);
            $propertyName = $dtoProperty->getName();

            if (in_array($propertyName, $excludedProperties)) {
                continue;
            }
            if($propertyName === 'contrasenia'){
                $value = password_hash($value, PASSWORD_BCRYPT);
            }

            if ($value !== null) {
                $setterMethod = 'set' . ucfirst($propertyName);

                if ($entityReflection->hasMethod($setterMethod)) {
                    $entity->$setterMethod($value);
                }
            }
        }
    }
}
