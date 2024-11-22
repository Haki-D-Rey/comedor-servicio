<?php

namespace App\Repository\Catalogo\Repository;

use App\DTO\Catalogo\DepartamentosDTO;
use App\Entity\Catalogo\Departamento;
use App\Repository\Catalogo\Interface\DepartamentosRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class DepartamentosRepository extends GenericRepository implements DepartamentosRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, Departamento::class);
        $this->logger = $loggerInterface;
    }

    public function getAllDepartamentos(): array
    {
        try {
            $departamento = $this->getAllEntities();
            return array_map(function (Departamento $departamento) {
                return new DepartamentosDTO(
                    $departamento->getId(),
                    $departamento->getNombre(),
                    $departamento->getDescripcion(),
                    $departamento->getCodigoInterno(),
                    $departamento->getFechaCreacion(),
                    $departamento->getFechaModificacion(),
                    $departamento->getEstado()
                );
            }, $departamento);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuarios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getDepartamentoById(int $id): ?DepartamentosDTO
    {
        try {
            $departamento = $this->getEntityById($id);
            if (!$departamento) {
                return null;
            }

            return new DepartamentosDTO(
                $departamento->getId(),
                $departamento->getNombre(),
                $departamento->getDescripcion(),
                $departamento->getCodigoInterno(),
                $departamento->getFechaCreacion(),
                $departamento->getFechaModificacion(),
                $departamento->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuario por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createDepartamento(DepartamentosDTO $cargosDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(Departamento::class)
                ->findOneBy(['codigo_interno' => $cargosDTO->getCodigoInterno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(Departamento::class)
                ->findOneBy(['nombre' => $cargosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $departamento = new Departamento();
            $departamento->setNombre($cargosDTO->getNombre());
            $departamento->setDescripcion($cargosDTO->getDescripcion());
            $departamento->setCodigoInterno($cargosDTO->getCodigoInterno());
            $departamento->setFechaCreacion($cargosDTO->getFechaCreacion());
            $departamento->setFechaModificacion($cargosDTO->getFechaModificacion());
            $departamento->setEstado($cargosDTO->getEstado());

            $this->entityManager->persist($departamento);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear usuario.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateDepartamento(int $id, DepartamentosDTO $cargosDTO): void
    {
        try {
            $departamento = $this->getEntityById($id);
            if (!$departamento) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(Departamento::class)
                ->findOneBy(['codigo_interno' => $cargosDTO->getCodigoInterno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(Departamento::class)
                ->findOneBy(['nombre' => $cargosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($departamento, $cargosDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteDepartamento(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
