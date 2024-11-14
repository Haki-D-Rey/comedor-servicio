<?php

namespace App\Helpers;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\PersistentCollection;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Persistence\Proxy;

class EntityLoadListener
{
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getObject();
        $entityManager = $args->getObjectManager();

        if ($entityManager instanceof EntityManagerInterface) {
            $this->initializeEntityRelations($entity, $entityManager);
        }
    }

    private function initializeEntityRelations(object $entity, EntityManagerInterface $entityManager): void
    {
        $reflectionClass = new \ReflectionClass($entity);

        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $propertyValue = $property->getValue($entity);

            // Si la propiedad es un proxy, la inicializamos
            if ($propertyValue instanceof Proxy) {
                $entityManager->initializeObject($propertyValue);
                $realEntity = clone $propertyValue;
                $property->setValue($entity, $realEntity);
            }
            // Si la propiedad es una colección persistente (relación)
            elseif ($propertyValue instanceof PersistentCollection && !$propertyValue->isInitialized()) {
                $entityManager->initializeObject($propertyValue);
            }
            // Si la propiedad es una entidad que no es proxy
            elseif (is_object($propertyValue) && !$propertyValue instanceof \Doctrine\Common\Collections\Collection) {
                $entityManager->initializeObject($propertyValue);
            }
        }
    }
}
