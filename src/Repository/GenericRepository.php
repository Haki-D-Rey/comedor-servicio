<?php

namespace App\Repository;

use App\Entity\ListaCatalogoDetalle;
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

    public function findMatchingDetalle(int $id_lista_catalogo, int $idValor, string $entityClass): ?ListaCatalogoDetalle
    {
        // Query the entity repository with the provided parameters
        $queryBuilder = $this->entityManager->getRepository($entityClass)->createQueryBuilder('d');

        $queryBuilder->where('d.id_lista_catalogo = :id_lista_catalogo')
            ->andWhere('d.id_valor = :id_valor')
            ->setParameter('id_lista_catalogo', $id_lista_catalogo)
            ->setParameter('id_valor', $idValor);

        $result = $queryBuilder->getQuery()->getOneOrNullResult();

        return $result;
    }

    private function sanitizeString(string $string): string
    {
        return str_replace(' ', '_', trim($string));
    }

    public function generateInternalCode(string $prefix, int $leadingZeros, int $lastValue, string $table = null): string
    {
        if (!$lastValue && $table) {
            $lastValue = $this->getLastValueFromTable($table);
        }

        $newValue = $lastValue + 1;
        $formattedValue = str_pad($newValue, $leadingZeros, '0', STR_PAD_LEFT);
        $newInternalCode = $prefix . $formattedValue;

        return $newInternalCode;
    }

    private function getLastValueFromTable(string $table): int
    {
        $repository = $this->entityManager->getRepository($table);
        $queryBuilder = $repository->createQueryBuilder('e')
            ->select('MAX(e.id)')
            ->getQuery();
    
        $lastValue = $queryBuilder->getSingleScalarResult();
        return $lastValue !== null ? (int)$lastValue : 0;
    }
}
