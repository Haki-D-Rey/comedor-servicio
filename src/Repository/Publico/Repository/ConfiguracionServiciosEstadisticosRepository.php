<?php

namespace App\Repository\Publico\Repository;

use App\DTO\Publico\ConfiguracionServiciosEstadisticosDTO;
use App\Entity\Publico\ConfiguracionServiciosEstadisticos;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use App\Repository\GenericRepository;
use App\Repository\Publico\Interface\ConfiguracionServiciosEstadisticosRepositoryInterface;
use DateTime;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class ConfiguracionServiciosEstadisticosRepository extends GenericRepository implements ConfiguracionServiciosEstadisticosRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, ConfiguracionServiciosEstadisticos::class);
        $this->logger = $loggerInterface;
    }

    public function getAllConfiguracionServiciosEstadisticos(): array
    {
        try {
            $estadisticosServicios = $this->getAllEntities();
            return array_map(function (ConfiguracionServiciosEstadisticos $servicio) {
                return new ConfiguracionServiciosEstadisticosDTO(
                    $servicio->getId(),
                    $servicio->getJsonConfiguracion(),
                    $servicio->getFechaCorte(),
                    $servicio->getFechaCreacion(),
                    $servicio->getFechaModificacion(),
                    $servicio->getEstado()
                );
            }, $estadisticosServicios);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el catálogo de Configuracion Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getConfiguracionServiciosEstadisticosById(int $id): ?ConfiguracionServiciosEstadisticosDTO
    {
        try {
            $servicio = $this->getEntityById($id);
            if (!$servicio) {
                return null;
            }

            return new ConfiguracionServiciosEstadisticosDTO(
                $servicio->getId(),
                $servicio->getJsonConfiguracion(),
                $servicio->getFechaCorte(),
                $servicio->getFechaCreacion(),
                $servicio->getFechaModificacion(),
                $servicio->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener la Configuracion Control Estadísticos de Servicios por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createConfiguracionServiciosEstadisticos(ConfiguracionServiciosEstadisticosDTO $configuracionServiciosEstadisticosDTO): ConfiguracionServiciosEstadisticosDTO
    {
        try {
            $configuracion = new ConfiguracionServiciosEstadisticos();

            $configuracion->setJsonConfiguracion($configuracionServiciosEstadisticosDTO->getJsonConfiguracion());
            $configuracion->setFechaCorte($configuracionServiciosEstadisticosDTO->getFechaCorte());
            $configuracion->setEstado($configuracionServiciosEstadisticosDTO->getEstado());

            // Persistir el servicio
            $this->entityManager->persist($configuracion);
            $this->entityManager->flush();

            return $configuracionServiciosEstadisticosDTO->fromEntity($configuracion);
        } catch (\InvalidArgumentException $e) {
            $this->logger->error('Error en los datos proporcionados: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error en los datos proporcionados.');
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear la Configuracion Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear el registro.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear la Configuracion Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateConfiguracionServiciosEstadisticos(int $id, ConfiguracionServiciosEstadisticosDTO $configuracionServiciosEstadisticosDTO): void
    {
        try {
            $configuracion = $this->getEntityById($id);
            if (!$configuracion) {
                throw new \RuntimeException('La Configuracion del Control Estadísticos de Servicios no encontrado.');
            }
            $excludeProperties = ['FechaCreacion'];
            $this->updateEntityFromDTO($configuracion, $configuracionServiciosEstadisticosDTO, $excludeProperties);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar la Configuracion del Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteConfiguracionServiciosEstadisticos(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar la Configuracion del Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
