<?php

namespace App\Repository\Catalogo\Repository;

use App\DTO\TipoServiciosDTO;
use App\Entity\TipoServicios;
use App\Repository\Catalogo\Interface\TipoServiciosRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class TipoServiciosRepository extends GenericRepository implements TipoServiciosRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, TipoServicios::class);
        $this->logger = $loggerInterface;
    }

    public function getAllTipoServicios(): array
    {
        try {
            $tipoServicios = $this->getAllEntities();
            return array_map(function (TipoServicios $tipoServicios) {
                return new TipoServiciosDTO(
                    $tipoServicios->getId(),
                    $tipoServicios->getNombre(),
                    $tipoServicios->getDescripcion(),
                    $tipoServicios->getcodigo_interno(),
                    $tipoServicios->getFecha_creacion(),
                    $tipoServicios->getFecha_modificacion(),
                    $tipoServicios->getEstado()
                );
            }, $tipoServicios);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuarios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getTipoServicioById(int $id): ?TipoServiciosDTO
    {
        try {
            $tipoServicios = $this->getEntityById($id);
            if (!$tipoServicios) {
                return null;
            }

            return new TipoServiciosDTO(
                $tipoServicios->getId(),
                $tipoServicios->getNombre(),
                $tipoServicios->getDescripcion(),
                $tipoServicios->getcodigo_interno(),
                $tipoServicios->getFecha_creacion(),
                $tipoServicios->getFecha_modificacion(),
                $tipoServicios->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuario por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createTipoServicio(TipoServiciosDTO $tipoServiciosDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(TipoServicios::class)
                ->findOneBy(['codigo_interno' => $tipoServiciosDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(TipoServicios::class)
                ->findOneBy(['nombre' => $tipoServiciosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $tipoServicios = new TipoServicios();
            $tipoServicios->setNombre($tipoServiciosDTO->getNombre());
            $tipoServicios->setDescripcion($tipoServiciosDTO->getDescripcion());
            $tipoServicios->setcodigo_interno($tipoServiciosDTO->getcodigo_interno());
            $tipoServicios->setFecha_creacion($tipoServiciosDTO->getFecha_creacion());
            $tipoServicios->setFecha_modificacion($tipoServiciosDTO->getFecha_modificacion());
            $tipoServicios->setEstado($tipoServiciosDTO->getEstado());

            $this->entityManager->persist($tipoServicios);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear usuario.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateTipoServicio(int $id, TipoServiciosDTO $tipoServiciosDTO): void
    {
        try {
            $tipoServicios = $this->getEntityById($id);
            if (!$tipoServicios) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(TipoServicios::class)
                ->findOneBy(['codigo_interno' => $tipoServiciosDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(TipoServicios::class)
                ->findOneBy(['nombre' => $tipoServiciosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($tipoServicios, $tipoServiciosDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteTipoServicio(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
