<?php

namespace App\Repository\Catalogo\Repository;

use App\DTO\ServiciosProductosDTO;
use App\Entity\ServiciosProductos;
use App\Repository\Catalogo\Interface\ServiciosProductosRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class ServiciosProductosRepository extends GenericRepository implements ServiciosProductosRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, ServiciosProductos::class);
        $this->logger = $loggerInterface;
    }

    public function getAllServiciosProductos(): array
    {
        try {
            $serviciosProductos = $this->getAllEntities();
            return array_map(function (ServiciosProductos $serviciosProductos) {
                return new ServiciosProductosDTO(
                    $serviciosProductos->getId(),
                    $serviciosProductos->getNombre(),
                    $serviciosProductos->getDescripcion(),
                    $serviciosProductos->getcodigo_interno(),
                    $serviciosProductos->getFecha_creacion(),
                    $serviciosProductos->getFecha_modificacion(),
                    $serviciosProductos->getEstado()
                );
            }, $serviciosProductos);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuarios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getServiciosProductoById(int $id): ?ServiciosProductosDTO
    {
        try {
            $serviciosProducto = $this->getEntityById($id);
            if (!$serviciosProducto) {
                return null;
            }

            return new ServiciosProductosDTO(
                $serviciosProducto->getId(),
                $serviciosProducto->getNombre(),
                $serviciosProducto->getDescripcion(),
                $serviciosProducto->getcodigo_interno(),
                $serviciosProducto->getFecha_creacion(),
                $serviciosProducto->getFecha_modificacion(),
                $serviciosProducto->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el Servicio del Producto por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createServiciosProducto(ServiciosProductosDTO $serviciosProductosDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(ServiciosProductos::class)
                ->findOneBy(['codigo_interno' => $serviciosProductosDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(ServiciosProductos::class)
                ->findOneBy(['nombre' => $serviciosProductosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $serviciosProductos = new ServiciosProductos();
            $serviciosProductos->setNombre($serviciosProductosDTO->getNombre());
            $serviciosProductos->setDescripcion($serviciosProductosDTO->getDescripcion());
            $serviciosProductos->setcodigo_interno($serviciosProductosDTO->getcodigo_interno());
            $serviciosProductos->setFecha_creacion($serviciosProductosDTO->getFecha_creacion());
            $serviciosProductos->setFecha_modificacion($serviciosProductosDTO->getFecha_modificacion());
            $serviciosProductos->setEstado($serviciosProductosDTO->getEstado());

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

    public function updateServiciosProducto(int $id, ServiciosProductosDTO $serviciosProductosDTO): void
    {
        try {
            $serviciosProductos = $this->getEntityById($id);
            if (!$serviciosProductos) {
                throw new \RuntimeException('El Servicio del Producto no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(ServiciosProductos::class)
                ->findOneBy(['codigo_interno' => $serviciosProductosDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(ServiciosProductos::class)
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

    public function deleteServiciosProducto(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
