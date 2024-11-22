<?php

namespace App\Repository\Catalogo\Repository;

use App\DTO\Catalogo\CargosDTO;
use App\Entity\Catalogo\Cargo;
use App\Repository\Catalogo\Interface\CargosRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class CargosRepository extends GenericRepository implements CargosRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, Cargo::class);
        $this->logger = $loggerInterface;
    }

    public function getAllCargos(): array
    {
        try {
            $cargo = $this->getAllEntities();
            return array_map(function (Cargo $cargo) {
                return new CargosDTO(
                    $cargo->getId(),
                    $cargo->getNombre(),
                    $cargo->getDescripcion(),
                    $cargo->getCodigoInterno(),
                    $cargo->getFechaCreacion(),
                    $cargo->getFechaModificacion(),
                    $cargo->getEstado()
                );
            }, $cargo);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuarios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getCargoById(int $id): ?CargosDTO
    {
        try {
            $cargo = $this->getEntityById($id);
            if (!$cargo) {
                return null;
            }

            return new CargosDTO(
                $cargo->getId(),
                $cargo->getNombre(),
                $cargo->getDescripcion(),
                $cargo->getCodigoInterno(),
                $cargo->getFechaCreacion(),
                $cargo->getFechaModificacion(),
                $cargo->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuario por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createCargo(CargosDTO $cargosDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(Cargo::class)
                ->findOneBy(['codigo_interno' => $cargosDTO->getCodigoInterno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(Cargo::class)
                ->findOneBy(['nombre' => $cargosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $cargo = new Cargo();
            $cargo->setNombre($cargosDTO->getNombre());
            $cargo->setDescripcion($cargosDTO->getDescripcion());
            $cargo->setCodigoInterno($cargosDTO->getCodigoInterno());
            $cargo->setFechaCreacion($cargosDTO->getFechaCreacion());
            $cargo->setFechaModificacion($cargosDTO->getFechaModificacion());
            $cargo->setEstado($cargosDTO->getEstado());

            $this->entityManager->persist($cargo);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear usuario.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateCargo(int $id, CargosDTO $cargosDTO): void
    {
        try {
            $cargo = $this->getEntityById($id);
            if (!$cargo) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(Cargo::class)
                ->findOneBy(['codigo_interno' => $cargosDTO->getCodigoInterno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(Cargo::class)
                ->findOneBy(['nombre' => $cargosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($cargo, $cargosDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteCargo(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
