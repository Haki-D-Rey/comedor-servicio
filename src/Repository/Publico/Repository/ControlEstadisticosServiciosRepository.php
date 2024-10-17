<?php

namespace App\Repository\Publico\Repository;

use App\DTO\Publico\ConfiguracionServiciosEstadisticosDTO;
use App\DTO\Publico\ControlEstadisticosServiciosDTO;
use App\Entity\DetalleZonaServicioHorario;
use App\Entity\Publico\ConfiguracionServiciosEstadisticos;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use App\Entity\Publico\ControlEstadisticosServicios;
use App\Repository\GenericRepository;
use App\Repository\Publico\Interface\ControlEstadisticosServiciosRepositoryInterface;
use App\Services\ConfiguracionServiciosEstadisticosServices;
use DateTime;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;
use Ramsey\Uuid\Uuid;

class ControlEstadisticosServiciosRepository extends GenericRepository implements ControlEstadisticosServiciosRepositoryInterface
{
    private $logger;
    private $servicioConfiguracion;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface, ConfiguracionServiciosEstadisticosServices $servicioConfiguracion)
    {
        parent::__construct($entityManager, ControlEstadisticosServicios::class);
        $this->logger = $loggerInterface;
        $this->servicioConfiguracion = $servicioConfiguracion;
    }

    public function getAllControlEstadisticosServicios(): array
    {
        try {
            $estadisticosServicios = $this->getAllEntities();
            return array_map(function (ControlEstadisticosServicios $servicio) {
                return new ControlEstadisticosServiciosDTO(
                    $servicio->getId(),
                    $servicio->getUuid(),
                    $servicio->getDetalleZonaServicioHorarioId(),
                    $servicio->getConfiguracionServiciosEstadisticosId(),
                    $servicio->getCantidadFirmada(),
                    $servicio->getCantidadAnulada(),
                    $servicio->getFechaCorte(),
                    $servicio->getFechaCreacion(),
                    $servicio->getFechaModificacion(),
                    $servicio->getEstado()
                );
            }, $estadisticosServicios);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el catálogo de Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getControlEstadisticosServiciosById(int $id): ?ControlEstadisticosServiciosDTO
    {
        try {
            $servicio = $this->getEntityById($id);
            if (!$servicio) {
                return null;
            }

            return new ControlEstadisticosServiciosDTO(
                $servicio->getId(),
                $servicio->getUuid(),
                $servicio->getIdDetalleZonaServicioHorario(),
                $servicio->getCantidadFirmada(),
                $servicio->getCantidadAnulada(),
                $servicio->getJsonConfiguracion(),
                $servicio->getFechaCorte(),
                $servicio->getFechaCreacion(),
                $servicio->getFechaModificacion(),
                $servicio->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener Control Estadísticos de Servicios por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getControlEstadisticosServiciosByIdForDate(string $date): ?array
    {
        try {

            $fecha = new DateTime($date);
            $fechaStart = $fecha->format('Y-m-d 00:00:00');
            $fechaEnd = $fecha->format('Y-m-d 23:59:59');

            // Crear el QueryBuilder
            $qb = $this->createQueryBuilder('c');

            // Hacer la consulta utilizando un rango de fechas
            $servicios = $qb->andWhere('c.fechaCorte >= :fechaCorteStart')
                ->andWhere('c.fechaCorte <= :fechaCorteEnd')
                ->setParameter('fechaCorteStart', $fechaStart)
                ->setParameter('fechaCorteEnd', $fechaEnd)
                ->andWhere('c.estado = true')
                ->getQuery()
                ->getResult();

            if (empty($servicios)) {
                return [];
            }

            return array_map(function (ControlEstadisticosServicios $servicio) {
                return [
                    "fecha" => $servicio->getFechaCorte()->format('Y-m-d'), // Uso de formato correcto
                    "jsonConfiguracion" => [] //$servicio->getJsonConfiguracion()
                ];
            }, $servicios);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener Control Estadísticos de Servicios por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }


    public function createFormControlEstadisticosServicios(array $controlEstadisticosServicios): void
    {
        $this->entityManager->beginTransaction();
        try {
            $validate = $this->getControlEstadisticosServiciosByIdForDate($controlEstadisticosServicios['fecha_corte']);
            if (!empty($validate)) {
                throw new \RuntimeException('Esta Fecha ' . $controlEstadisticosServicios['fecha_corte'] . ' ya fue registrada para el control Estadistico.', 404);
            }
            $fecha_corte = new \DateTime($controlEstadisticosServicios['fecha_corte']);
            $lista_detalles_servicios = $controlEstadisticosServicios['json_configuracion'];

            $configuracionServiciosEstadisticosDTO = new ConfiguracionServiciosEstadisticosDTO(
                null,
                $lista_detalles_servicios,
                $fecha_corte,
                new \DateTime(),
                null,
                1
            );

            $resultado = $this->servicioConfiguracion->createConfiguracionServiciosEstadisticos($configuracionServiciosEstadisticosDTO);

            $i = 0;
            $batchSize = 100;

            foreach ($lista_detalles_servicios as $lista) {
                $entity = new ControlEstadisticosServicios();
                $uuid = Uuid::uuid4();
                $entity->setIdUuid($uuid);

                $idDetalleZonaServicioHorario = (int)$lista['id_detalle_zona_servicio_horario'];
                $detalleZonaServicioHorario = $this->entityManager->find(DetalleZonaServicioHorario::class, $idDetalleZonaServicioHorario);
                if (!$detalleZonaServicioHorario) {
                    throw new \RuntimeException('No se encontró la entidad DetalleZonaServicioHorario con el ID proporcionado.', 404);
                }

                $idConfiguracionServiciosEstadisticos = $resultado->getId();
                $configuracionServiciosEstadisticos = $this->entityManager->find(ConfiguracionServiciosEstadisticos::class, $idConfiguracionServiciosEstadisticos);
                if (!$configuracionServiciosEstadisticos) {
                    throw new \RuntimeException('No se encontró la entidad ConfiguracionServiciosEstadisticos con el ID proporcionado.', 404);
                }

                $entity->setDetalleZonaServicioHorario($detalleZonaServicioHorario);
                $entity->setConfiguracionServiciosEstadisticos($configuracionServiciosEstadisticos);
                $entity->setCantidadFirmada((int)$lista['value']);
                $entity->setCantidadAnulada(0);
                $entity->setFechaCorte($fecha_corte);
                $entity->setEstado(1);

                $this->entityManager->persist($entity);

                if ((++$i % $batchSize) === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
            }

            // Guardar cualquier registro pendiente
            $this->entityManager->flush();
            $this->entityManager->clear();

            // Confirmar transacción
            $this->entityManager->commit();

            return;
        } catch (\InvalidArgumentException $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error en los datos proporcionados: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error en los datos proporcionados.');
        } catch (ORMException | DBALException $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error al crear Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear el registro.');
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error inesperado al crear Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            if ($e->getCode() === 404) {
                throw new \RuntimeException($e->getMessage(), 404);
            }
            throw new \RuntimeException($e->getMessage());
        }
    }


    public function createControlEstadisticosServicios(ControlEstadisticosServiciosDTO $controlEstadisticosServiciosDTO): void
    {
        try {
            $servicio = new ControlEstadisticosServicios();

            $uuid = Uuid::fromString($servicio->getUuid());
            $servicio->setIdUuid($uuid);

            $idDetalleZonaServicioHorario =  $controlEstadisticosServiciosDTO->getIdDetalleZonaServicioHorario();
            $detalleZonaServicioHorario = $this->entityManager->find(DetalleZonaServicioHorario::class, $idDetalleZonaServicioHorario);

            if (!$detalleZonaServicioHorario) {
                throw new \RuntimeException('No se encontró la entidad DetalleZonaServicioHorario con el ID proporcionado.');
            }

            $idConfiguracionServiciosEstadisticos =  $controlEstadisticosServiciosDTO->getIdConfiguracionServiciosEstadisticos();
            $configuracionServiciosEstadisticos = $this->entityManager->find(ConfiguracionServiciosEstadisticos::class, $idConfiguracionServiciosEstadisticos);

            if (!$configuracionServiciosEstadisticos) {
                throw new \RuntimeException('No se encontró la entidad configuracionServiciosEstadisticos con el ID proporcionado.');
            }

            $servicio->setDetalleZonaServicioHorario($detalleZonaServicioHorario);
            $servicio->setConfiguracionServiciosEstadisticos($configuracionServiciosEstadisticos);
            $servicio->setCantidadFirmada($controlEstadisticosServiciosDTO->getCantidadFirmada());
            $servicio->setCantidadAnulada($controlEstadisticosServiciosDTO->getCantidadAnulada());
            $servicio->setFechaCorte($controlEstadisticosServiciosDTO->getFechaCorte());
            $servicio->setEstado($controlEstadisticosServiciosDTO->getEstado());

            // Persistir el servicio
            $this->entityManager->persist($servicio);
            $this->entityManager->flush();
        } catch (\InvalidArgumentException $e) {
            $this->logger->error('Error en los datos proporcionados: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error en los datos proporcionados.');
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear el registro.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateControlEstadisticosServicios(int $id, ControlEstadisticosServiciosDTO $controlEstadisticosServiciosDTO): void
    {
        try {
            $servicio = $this->getEntityById($id);
            if (!$servicio) {
                throw new \RuntimeException('Control Estadísticos de Servicios no encontrado.');
            }
            $excludeProperties = ['FechaCreacion'];
            $this->updateEntityFromDTO($servicio, $controlEstadisticosServiciosDTO, $excludeProperties);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteControlEstadisticosServicios(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar Control Estadísticos de Servicios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
