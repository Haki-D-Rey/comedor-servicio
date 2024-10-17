<?php

namespace App\Repository\Catalogo\Repository;

use App\DTO\ZonaDTO;
use App\Entity\Zona;
use App\Repository\Catalogo\Interface\ZonaRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class ZonaRepository extends GenericRepository implements ZonaRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, Zona::class);
        $this->logger = $loggerInterface;
    }

    public function getAllZona(): array
    {
        try {
            $zona = $this->getAllEntities();
            return array_map(function (Zona $zona) {
                return new ZonaDTO(
                    $zona->getId(),
                    $zona->getNombre(),
                    $zona->getDescripcion(),
                    $zona->getcodigo_interno(),
                    $zona->getFecha_creacion(),
                    $zona->getFecha_modificacion(),
                    $zona->getEstado()
                );
            }, $zona);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener la lista de zonas: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getZonaById(int $id): ?ZonaDTO
    {
        try {
            $zona = $this->getEntityById($id);
            if (!$zona) {
                return null;
            }

            return new ZonaDTO(
                $zona->getId(),
                $zona->getNombre(),
                $zona->getDescripcion(),
                $zona->getcodigo_interno(),
                $zona->getFecha_creacion(),
                $zona->getFecha_modificacion(),
                $zona->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener la lista de zonas por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createZona(ZonaDTO $tipoServiciosDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(Zona::class)
                ->findOneBy(['codigo_interno' => $tipoServiciosDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(Zona::class)
                ->findOneBy(['nombre' => $tipoServiciosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $zona = new Zona();
            $zona->setNombre($tipoServiciosDTO->getNombre());
            $zona->setDescripcion($tipoServiciosDTO->getDescripcion());
            $zona->setcodigo_interno($tipoServiciosDTO->getcodigo_interno());
            $zona->setFecha_creacion($tipoServiciosDTO->getFecha_creacion());
            $zona->setFecha_modificacion($tipoServiciosDTO->getFecha_modificacion());
            $zona->setEstado($tipoServiciosDTO->getEstado());

            $this->entityManager->persist($zona);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear la zona: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear la zona.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear la zona:: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateZona(int $id, ZonaDTO $tipoServiciosDTO): void
    {
        try {
            $zona = $this->getEntityById($id);
            if (!$zona) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(Zona::class)
                ->findOneBy(['codigo_interno' => $tipoServiciosDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(Zona::class)
                ->findOneBy(['nombre' => $tipoServiciosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($zona, $tipoServiciosDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar la zona: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteZona(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar la zona: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
