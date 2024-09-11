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
            if ($propertyName === 'contrasenia') {
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


    /**
     * Marks an entity as deleted by setting its status to a specific value.
     *
     * @param int $id The ID of the entity to update.
     * @param int $deletedStatus The status value indicating the entity is deleted (e.g., 0).
     * @return void
     */
    public function markAsDeleted(int $id, int $deletedStatus): void
    {
            $entity = $this->repository->find($id);
            if (!$entity) {
                throw new \RuntimeException('Entidad no encontrada.');
            }

            // Set the status to indicate the entity is deleted
            // Assumes the entity has a method `setEstado` or similar
            $method = 'setEstado';
            if (method_exists($entity, $method)) {
                $entity->$method($deletedStatus);
                $this->entityManager->flush();
            } else {
                throw new \RuntimeException('El método de actualización del estado no existe en la entidad.');
            }
    }
}
