<?php

namespace App\Repository\Catalogo\Repository;

use App\DTO\Catalogo\IdentificacionFacturacionDTO;
use App\Entity\Catalogo\IdentificacionFacturacion;
use App\Repository\Catalogo\Interface\IdentificacionFacturacionRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class IdentificacionFacturacionRepository extends GenericRepository implements IdentificacionFacturacionRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, IdentificacionFacturacion::class);
        $this->logger = $loggerInterface;
    }

    public function getAllIdentificacionFacturacion(): array
    {
        try {
            $IdentificacionFacturacion = $this->getAllEntities();
            return array_map(function (IdentificacionFacturacion $IdentificacionFacturacion) {
                return new IdentificacionFacturacionDTO(
                    $IdentificacionFacturacion->getId(),
                    $IdentificacionFacturacion->getNombre(),
                    $IdentificacionFacturacion->getDescripcion(),
                    $IdentificacionFacturacion->getCodigoInterno(),
                    $IdentificacionFacturacion->getFechaCreacion(),
                    $IdentificacionFacturacion->getFechaModificacion(),
                    $IdentificacionFacturacion->getEstado()
                );
            }, $IdentificacionFacturacion);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuarios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getIdentificacionFacturacionById(int $id): ?IdentificacionFacturacionDTO
    {
        try {
            $IdentificacionFacturacion = $this->getEntityById($id);
            if (!$IdentificacionFacturacion) {
                return null;
            }

            return new IdentificacionFacturacionDTO(
                $IdentificacionFacturacion->getId(),
                $IdentificacionFacturacion->getNombre(),
                $IdentificacionFacturacion->getDescripcion(),
                $IdentificacionFacturacion->getCodigoInterno(),
                $IdentificacionFacturacion->getFechaCreacion(),
                $IdentificacionFacturacion->getFechaModificacion(),
                $IdentificacionFacturacion->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuario por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createIdentificacionFacturacion(IdentificacionFacturacionDTO $cargosDTO): void
    {
        try {

            $existingcodigo_interno = $this->entityManager->getRepository(IdentificacionFacturacion::class)
                ->findOneBy(['codigo_interno' => $cargosDTO->getCodigoInterno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(IdentificacionFacturacion::class)
                ->findOneBy(['nombre' => $cargosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $IdentificacionFacturacion = new IdentificacionFacturacion();
            $IdentificacionFacturacion->setNombre($cargosDTO->getNombre());
            $IdentificacionFacturacion->setDescripcion($cargosDTO->getDescripcion());
            $IdentificacionFacturacion->setCodigoInterno($cargosDTO->getCodigoInterno());
            $IdentificacionFacturacion->setFechaCreacion($cargosDTO->getFechaCreacion());
            $IdentificacionFacturacion->setFechaModificacion($cargosDTO->getFechaModificacion());
            $IdentificacionFacturacion->setEstado($cargosDTO->getEstado());

            $this->entityManager->persist($IdentificacionFacturacion);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear usuario.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateIdentificacionFacturacion(int $id, IdentificacionFacturacionDTO $cargosDTO): void
    {
        try {
            $IdentificacionFacturacion = $this->getEntityById($id);
            if (!$IdentificacionFacturacion) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(IdentificacionFacturacion::class)
                ->findOneBy(['codigo_interno' => $cargosDTO->getCodigoInterno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(IdentificacionFacturacion::class)
                ->findOneBy(['nombre' => $cargosDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($IdentificacionFacturacion, $cargosDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteIdentificacionFacturacion(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
