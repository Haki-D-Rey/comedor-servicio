<?php

namespace App\Repository\Publico\Repository;

use App\DTO\Publico\DetalleZonaServicioHorarioClienteFacturacionDTO;
use App\Entity\Publico\DetalleZonaServicioHorarioClienteFacturacion;
use App\Entity\DetalleZonaServicioHorario;
use App\Entity\Publico\DetalleClienteIdentificacionFacturacion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use App\Repository\GenericRepository;
use App\Repository\Publico\Interface\DetalleZonaServicioHorarioClienteFacturacionRepositoryInterface;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class DetalleZonaServicioHorarioClienteFacturacionRepository extends GenericRepository implements DetalleZonaServicioHorarioClienteFacturacionRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, DetalleZonaServicioHorarioClienteFacturacion::class);
        $this->logger = $loggerInterface;
    }

    public function getAllDetalleZonaServicioHorarioClienteFacturacion(): array
    {
        try {
            $detalleZonaServicioHorarioClienteFacturacion = $this->getAllEntities();
            return array_map(function (DetalleZonaServicioHorarioClienteFacturacion $detalle) {
                return new DetalleZonaServicioHorarioClienteFacturacionDTO(
                    $detalle->getId(),
                    $detalle->getIdDetalleClienteIdentificacionFacturacion(),
                    $detalle->getIdDetalleZonaServicioHorario(),
                    $detalle->getCodigoInterno(),
                    $detalle->getFechaCreacion(),
                    $detalle->getFechaModificacion(),
                    $detalle->getEstado()
                );
            }, $detalleZonaServicioHorarioClienteFacturacion);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener los detalles de Zona Servicio Horario Cliente Facturación: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getDetalleZonaServicioHorarioClienteFacturacionById(int $id): ?DetalleZonaServicioHorarioClienteFacturacionDTO
    {
        try {
            $detalle = $this->getEntityById($id);
            if (!$detalle) {
                return null;
            }

            return new DetalleZonaServicioHorarioClienteFacturacionDTO(
                $detalle->getId(),
                $detalle->getIdDetalleClienteIdentificacionFacturacion(),
                $detalle->getIdDetalleZonaServicioHorario(),
                $detalle->getCodigoInterno(),
                $detalle->getFechaCreacion(),
                $detalle->getFechaModificacion(),
                $detalle->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el detalle de Zona Servicio Horario Cliente Facturación por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createDetalleZonaServicioHorarioClienteFacturacion(DetalleZonaServicioHorarioClienteFacturacionDTO $detalleZonaServicioHorarioClienteFacturacionDTO): void
    {
        try {
            // Verificar si el id_detalle_cliente_identificacion_facturacion existe en su tabla correspondiente
            $idDetalleClienteIdentificacionFacturacion = $this->entityManager->getRepository(DetalleClienteIdentificacionFacturacion::class)->find($detalleZonaServicioHorarioClienteFacturacionDTO->getIdDetalleClienteIdentificacionFacturacion());
            $idDetalleZonaServicioHorario =  $this->entityManager->getRepository(DetalleZonaServicioHorario::class)->find($detalleZonaServicioHorarioClienteFacturacionDTO->getIdDetalleClienteIdentificacionFacturacion());
            if (!$idDetalleClienteIdentificacionFacturacion) {
                throw new \InvalidArgumentException("El ID de detalle_cliente_identificacion_facturacion no existe en la base de datos.");
            }

            if (!$idDetalleZonaServicioHorario) {
                throw new \InvalidArgumentException("El ID de detalle_zona_servicio_horario no existe en la base de datos.");
            }

            // Verificar que no exista un registro con el mismo id_detalle_zona_servicio_horario y id_detalle_cliente_identificacion_facturacion
            $existingRecord = $this->entityManager->getRepository(DetalleZonaServicioHorarioClienteFacturacion::class)
                ->findOneBy([
                    'idDetalleZonaServicioHorario' => $detalleZonaServicioHorarioClienteFacturacionDTO->getIdDetalleZonaServicioHorario(),
                    'idDetalleClienteIdentificacionFacturacion' => $detalleZonaServicioHorarioClienteFacturacionDTO->getIdDetalleClienteIdentificacionFacturacion()
                ]);

            if ($existingRecord) {
                throw new \InvalidArgumentException("El Cliente con Esta Identificacion ya está asociado a un registro de Servicio de Producto Por evento participante.");
            }

            $codigo_interno = $this->generateInternalCode('DZSHCF-', 4, 0, DetalleZonaServicioHorarioClienteFacturacion::class);

            // Preparar el SQL de inserción
            $sql = "INSERT INTO public.detalle_zona_servicio_horario_cliente_facturacion (id_detalle_cliente_identificacion_facturacion, id_detalle_zona_servicio_horario, codigo_interno, estado, fecha_creacion)
                    VALUES (:id_detalle_cliente_identificacion_facturacion, :id_detalle_zona_servicio_horario, :codigo_interno, :estado, :fecha_creacion)";

            // Definir los parámetros que pasamos a la consulta
            $params = [
                'id_detalle_cliente_identificacion_facturacion' => $detalleZonaServicioHorarioClienteFacturacionDTO->getIdDetalleClienteIdentificacionFacturacion(),
                'id_detalle_zona_servicio_horario' => $detalleZonaServicioHorarioClienteFacturacionDTO->getIdDetalleZonaServicioHorario(),
                'codigo_interno' => $codigo_interno,
                'estado' => $detalleZonaServicioHorarioClienteFacturacionDTO->getEstado(),
                'fecha_creacion' => $detalleZonaServicioHorarioClienteFacturacionDTO->getFechaCreacion()->format('Y-m-d H:i:s')
            ];

            $this->entityManager->getConnection()->executeQuery($sql, $params);
        } catch (\InvalidArgumentException $e) {
            $this->logger->error('Error en los datos proporcionados: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear el detalle de Zona Servicio Horario Cliente Facturación: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear el registro.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear el detalle de Zona Servicio Horario Cliente Facturación: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }


    public function updateDetalleZonaServicioHorarioClienteFacturacion(int $id, DetalleZonaServicioHorarioClienteFacturacionDTO $detalleZonaServicioHorarioClienteFacturacionDTO): void
    {
        try {
            $detalle = $this->getEntityById($id);
            if (!$detalle) {
                throw new \RuntimeException('El detalle de Zona Servicio Horario Cliente Facturación no encontrado.');
            }

            // Actualizar propiedades
            $excludeProperties = ['FechaCreacion'];
            $this->updateEntityFromDTO($detalle, $detalleZonaServicioHorarioClienteFacturacionDTO, $excludeProperties);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar el detalle de Zona Servicio Horario Cliente Facturación: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteDetalleZonaServicioHorarioClienteFacturacion(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar el detalle de Zona Servicio Horario Cliente Facturación: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
