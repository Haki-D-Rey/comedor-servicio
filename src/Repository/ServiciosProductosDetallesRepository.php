<?php

namespace App\Repository;

use App\DTO\ServiciosProductosDetallesDTO;
use App\Entity\ServiciosProductosDetalles;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class ServiciosProductosDetallesRepository extends GenericRepository implements ServiciosProductosDetallesRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, ServiciosProductosDetalles::class);
        $this->logger = $loggerInterface;
    }

    public function getAllServiciosProductosDetalles(): array
    {
        try {
            $serviciosProductosDetalles = $this->getAllEntities();
            return array_map(function (ServiciosProductosDetalles $serviciosProductosDetalles) {
                return new ServiciosProductosDetalles(
                    $serviciosProductosDetalles->getId(),
                    1,
                    1,
                    1,
                    $serviciosProductosDetalles->getNombre(),
                    $serviciosProductosDetalles->getDescripcion(),
                    $serviciosProductosDetalles->getcodigo_interno(),
                    $serviciosProductosDetalles->getFecha_creacion(),
                    $serviciosProductosDetalles->getFecha_modificacion(),
                    $serviciosProductosDetalles->getEstado()
                );
            }, $serviciosProductosDetalles);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el Catalago del Detalle de los Servicios de Productos: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getServiciosProductosDetallesById(int $id): ?ServiciosProductosDetallesDTO
    {
        try {
            $serviciosProductosDetalles = $this->getEntityById($id);
            if (!$serviciosProductosDetalles) {
                return null;
            }

            return new ServiciosProductosDetallesDTO(
                $serviciosProductosDetalles->getId(),
                1,
                1,
                1,
                $serviciosProductosDetalles->getNombre(),
                $serviciosProductosDetalles->getDescripcion(),
                $serviciosProductosDetalles->getcodigo_interno(),
                $serviciosProductosDetalles->getFecha_creacion(),
                $serviciosProductosDetalles->getFecha_modificacion(),
                $serviciosProductosDetalles->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el Servicio del Producto por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createServiciosProductosDetalles(ServiciosProductosDetallesDTO $serviciosProductosDetallesDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(ServiciosProductosDetalles::class)
                ->findOneBy(['codigo_interno' => $serviciosProductosDetallesDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(ServiciosProductosDetalles::class)
                ->findOneBy(['nombre' => $serviciosProductosDetallesDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $serviciosProductos = new ServiciosProductosDetalles();
            $serviciosProductos->setNombre($serviciosProductosDetallesDTO->getNombre());
            $serviciosProductos->setDescripcion($serviciosProductosDetallesDTO->getDescripcion());
            $serviciosProductos->setcodigo_interno($serviciosProductosDetallesDTO->getcodigo_interno());
            $serviciosProductos->setFecha_creacion($serviciosProductosDetallesDTO->getFecha_creacion());
            $serviciosProductos->setFecha_modificacion($serviciosProductosDetallesDTO->getFecha_modificacion());
            $serviciosProductos->setEstado($serviciosProductosDetallesDTO->getEstado());
            $this->entityManager->persist($serviciosProductos);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear usuario.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateServiciosProductosDetalles(int $id, ServiciosProductosDetallesDTO $serviciosProductosDTO): void
    {
        try {
            $serviciosProductos = $this->getEntityById($id);
            if (!$serviciosProductos) {
                throw new \RuntimeException('El Servicio del Producto no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(ServiciosProductosDetalles::class)
                ->findOneBy(['codigo_interno' => $serviciosProductosDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(ServiciosProductosDetalles::class)
                ->findOneBy(['nombre' => $serviciosProductosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($serviciosProductos, $serviciosProductosDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteServiciosProductosDetalles(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
