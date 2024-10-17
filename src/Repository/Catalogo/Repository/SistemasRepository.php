<?php

namespace App\Repository\Catalogo\Repository;

use App\DTO\SistemasDTO;
use App\Entity\Sistemas;
use App\Repository\Catalogo\Interface\SistemasRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class SistemasRepository extends GenericRepository implements SistemasRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, Sistemas::class);
        $this->logger = $loggerInterface;
    }

    public function getAllSistemas(): array
    {
        try {
            $sistemas = $this->getAllEntities();
            return array_map(function (Sistemas $sistemas) {
                return new SistemasDTO(
                    $sistemas->getId(),
                    $sistemas->getNombre(),
                    $sistemas->getDescripcion(),
                    $sistemas->getcodigo_interno(),
                    $sistemas->getFecha_creacion(),
                    $sistemas->getFecha_modificacion(),
                    $sistemas->getEstado()
                );
            }, $sistemas);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuarios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getSistemaById(int $id): ?SistemasDTO
    {
        try {
            $sistemas = $this->getEntityById($id);
            if (!$sistemas) {
                return null;
            }

            return new SistemasDTO(
                $sistemas->getId(),
                $sistemas->getNombre(),
                $sistemas->getDescripcion(),
                $sistemas->getcodigo_interno(),
                $sistemas->getFecha_creacion(),
                $sistemas->getFecha_modificacion(),
                $sistemas->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuario por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createSistema(SistemasDTO $sistemasDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(Sistemas::class)
                ->findOneBy(['codigo_interno' => $sistemasDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(Sistemas::class)
                ->findOneBy(['nombre' => $sistemasDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $sistemas = new Sistemas();
            $sistemas->setNombre($sistemasDTO->getNombre());
            $sistemas->setDescripcion($sistemasDTO->getDescripcion());
            $sistemas->setcodigo_interno($sistemasDTO->getcodigo_interno());
            $sistemas->setFecha_creacion($sistemasDTO->getFecha_creacion());
            $sistemas->setFecha_modificacion($sistemasDTO->getFecha_modificacion());
            $sistemas->setEstado($sistemasDTO->getEstado());

            $this->entityManager->persist($sistemas);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear usuario.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateSistema(int $id, SistemasDTO $sistemasDTO): void
    {
        try {
            $sistemas = $this->getEntityById($id);
            if (!$sistemas) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(Sistemas::class)
                ->findOneBy(['codigo_interno' => $sistemasDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(Sistemas::class)
                ->findOneBy(['nombre' => $sistemasDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($sistemas, $sistemasDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteSistema(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
