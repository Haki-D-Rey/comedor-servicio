<?php

namespace App\Repository\Catalogo\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use App\DTO\DetalleZonasServicioHorarioDTO;
use App\DTO\ServiciosProductosDetallesDTO;
use App\Entity\DetalleZonaServicioHorario;
use App\Entity\Horario;
use App\Entity\ServiciosProductosDetalles;
use App\Entity\ZonaUsuarios;
use App\Repository\Catalogo\Interface\DetalleZonaServicioHorarioRepositoryInterface;
use App\Repository\GenericRepository;
use DateTime;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class DetalleZonaServicioHorarioRepository extends GenericRepository implements DetalleZonaServicioHorarioRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, DetalleZonaServicioHorario::class);
        $this->logger = $loggerInterface;
    }

    public function getAllDetalleZonasServicioHorario(): array
    {
        try {
            $detalleZonasServicioHorario = $this->getAllEntities();
            return array_map(function (DetalleZonaServicioHorario $detalleZonasServicioHorario) {
                return new DetalleZonasServicioHorarioDTO(
                    $detalleZonasServicioHorario->getId(),
                    $detalleZonasServicioHorario->getIdServiciosProductosDetalles(),
                    $detalleZonasServicioHorario->getIdHorario(),
                    $detalleZonasServicioHorario->getIdZonaUsuario(),
                    $detalleZonasServicioHorario->getNombre(),
                    $detalleZonasServicioHorario->getDescripcion(),
                    $detalleZonasServicioHorario->getCodigoInterno(),
                    $detalleZonasServicioHorario->getFechaCreacion(),
                    $detalleZonasServicioHorario->getFechaModificacion(),
                    $detalleZonasServicioHorario->getEstado()
                );
            }, $detalleZonasServicioHorario);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el Catalago del Detalle de los Servicios de Productos: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getAllDetalleZonaServicioHorarioByZonaUsuario($idUsuario): array
    {
        // Buscar todas las zonas asociadas al usuario
        $zonasUsuario = $this->entityManager->getRepository(ZonaUsuarios::class)
            ->findBy(['usuario' => $idUsuario, 'estado' => true]); // Asegúrate de que 'usuario' sea el campo correcto en la entidad ZonaUsuarios.

        if (empty($zonasUsuario)) {
            return [];
        }

        // Extraer los IDs de las zonas
        $zonaIds = array_map(function (ZonaUsuarios $zona) {
            return $zona->getId();
        }, $zonasUsuario);

        // Buscar todos los detalles de zona y servicio horario asociados a las zonas encontradas
        $detalleZonaServicioHorarios = $this->findBy(['zonaUsuario' => $zonaIds]);

        if (empty($detalleZonaServicioHorarios)) {
            return [];
        }

        // Transformar a DTO
        $detalleZonaServicioHorarioDTO = array_map(function (DetalleZonaServicioHorario $detalleZonaServicioHorario) {
            return [
                'id_detalle_zona_servicio_horario' => $detalleZonaServicioHorario->getId(),
                'id_servicio_detalle_producto' => $detalleZonaServicioHorario->getIdServiciosProductosDetalles(),
                'idZonaUsuario' => $detalleZonaServicioHorario->getIdZonaUsuario(),
            ];
        }, $detalleZonaServicioHorarios);

        // Obtener todos los ID de servicio detalle producto
        $idServiciosDetalles = array_map(function ($detalleZonaServicioHorarioDTO) {
            return $detalleZonaServicioHorarioDTO['id_servicio_detalle_producto'];
        }, $detalleZonaServicioHorarioDTO);

        // Buscar todos los servicios producto detalles correspondientes
        $servicioProductoDetalles = $this->entityManager->getRepository(ServiciosProductosDetalles::class)
            ->findBy(['id' => $idServiciosDetalles]);

        if (empty($servicioProductoDetalles)) {
            return [];
        }

        // Preparar el resultado final
        $resultadoFinal = [];

        foreach ($detalleZonaServicioHorarioDTO as $detalle) {
            foreach ($servicioProductoDetalles as $servicioProductoDetalle) {
                if ($detalle['id_servicio_detalle_producto'] == $servicioProductoDetalle->getId()) {
                    $resultadoFinal[] = [
                        'id_detalle_zona_servicio_horario' => $detalle['id_detalle_zona_servicio_horario'],
                        'id_servicio_detalle_producto' => $detalle['id_servicio_detalle_producto'],
                        'idZonaUsuario' => $detalle['idZonaUsuario'],
                        'idSistemas' => $servicioProductoDetalle->getIdSistemas(),
                        'idTipoServicios' => $servicioProductoDetalle->getIdTipoServicios(),
                        'idServiciosProductos' => $servicioProductoDetalle->getIdServiciosProductos(),
                        'nombre' => $servicioProductoDetalle->getNombre(),
                        'descripcion' => $servicioProductoDetalle->getDescripcion(),
                        'codigo_interno' => $servicioProductoDetalle->getCodigo_interno(),
                        'fecha_creacion' => $servicioProductoDetalle->getFecha_creacion(),
                        'fecha_modificacion' => $servicioProductoDetalle->getFecha_modificacion(),
                        'estado' => $servicioProductoDetalle->getEstado(),
                    ];
                }
            }
        }

        return $resultadoFinal;
    }

    public function getAllDetalleZonaServicioHorarioByIdZonaUsuario($id): array
    {
        $zonasUsuario = $this->entityManager->getRepository(ZonaUsuarios::class)->findBy(['codigo_interno' => $id, 'estado' => true]);

        if (empty($zonasUsuario)) {
            return [];
        }

        $detalleZonaServicioHorarios = $this->findBy(['zonaUsuario' => $zonasUsuario[0]->getId()]);

        if (empty($detalleZonaServicioHorarios)) {
            return [];
        }

        $detalleZonaServicioHorarioDTO = array_map(function (DetalleZonaServicioHorario $detalleZonaServicioHorario) {
            return [
                'id_detalle_zona_servicio_horario' => $detalleZonaServicioHorario->getId(),
                'id_servicio_detalle_producto' => $detalleZonaServicioHorario->getIdServiciosProductosDetalles(),
                'idZonaUsuario' => $detalleZonaServicioHorario->getIdZonaUsuario(),
                'id_horario' => $detalleZonaServicioHorario->getIdHorario(),
            ];
        }, $detalleZonaServicioHorarios);

        $idServiciosDetalles = array_map(function ($detalleZonaServicioHorarioDTO) {
            return $detalleZonaServicioHorarioDTO['id_servicio_detalle_producto'];
        }, $detalleZonaServicioHorarioDTO);

        $servicioProductoDetalles = $this->entityManager->getRepository(ServiciosProductosDetalles::class)->findBy(['id' => $idServiciosDetalles]);

        if (empty($servicioProductoDetalles)) {
            return [];
        }

        $resultadoFinal = [];

        $horario = $this->entityManager->getRepository(Horario::class);

        foreach ($detalleZonaServicioHorarioDTO as $detalle) {
            foreach ($servicioProductoDetalles as $servicioProductoDetalle) {
                if ($detalle['id_servicio_detalle_producto'] == $servicioProductoDetalle->getId()) {
                    $findHorario = $horario->findOneBy(['id' => $detalle['id_horario'], 'estado'  => true]);
                    $perido_inicio = ($findHorario->getPeriodoInicio())->format('H:i:s');
                    $perido_fin = ($findHorario->getPeriodoFin())->format('H:i:s');
                    $codigo_horario = $findHorario->getcodigo_interno();

                    $resultadoFinal[] = [
                        'id_detalle_zona_servicio_horario' => $detalle['id_detalle_zona_servicio_horario'],
                        'id_servicio_detalle_producto' => $detalle['id_servicio_detalle_producto'],
                        'horario' => (object) [
                            'id' => $detalle['id_horario'],
                            'codigo_interno' => $codigo_horario,
                            'periodo_inicio' => $perido_inicio,
                            'periodo_final' => $perido_fin
                        ],
                        'idZonaUsuario' => $detalle['idZonaUsuario'],
                        'idSistemas' => $servicioProductoDetalle->getIdSistemas(),
                        'idTipoServicios' => $servicioProductoDetalle->getIdTipoServicios(),
                        'idServiciosProductos' => $servicioProductoDetalle->getIdServiciosProductos(),
                        'nombre' => $servicioProductoDetalle->getNombre(),
                        'descripcion' => $servicioProductoDetalle->getDescripcion(),
                        'codigo_interno' => $servicioProductoDetalle->getcodigo_interno(),
                        'fecha_creacion' => $servicioProductoDetalle->getFecha_creacion(),
                        'fecha_modificacion' => $servicioProductoDetalle->getFecha_modificacion(),
                        'estado' => $servicioProductoDetalle->getEstado()
                    ];
                }
            }
        }

        return $resultadoFinal;
    }

    public function getDetalleZonasServicioHorarioById(int $id): ?DetalleZonasServicioHorarioDTO
    {
        try {
            $detalleZonasServicioHorario = $this->getEntityById($id);
            if (!$detalleZonasServicioHorario) {
                return null;
            }

            return new DetalleZonasServicioHorarioDTO(
                $detalleZonasServicioHorario->getId(),
                $detalleZonasServicioHorario->getIdServiciosProductosDetalles(),
                $detalleZonasServicioHorario->getIdHorario(),
                $detalleZonasServicioHorario->getIdZonaUsuario(),
                $detalleZonasServicioHorario->getNombre(),
                $detalleZonasServicioHorario->getDescripcion(),
                $detalleZonasServicioHorario->getcodigo_interno(),
                $detalleZonasServicioHorario->getFecha_creacion(),
                $detalleZonasServicioHorario->getFecha_modificacion(),
                $detalleZonasServicioHorario->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el Servicio del Producto por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createDetalleZonasServicioHorario(array $detalleZonasServicioHorarioDTOs): void
    {
        $batchSize = 20;  // Tamaño del lote de inserciones/actualizaciones
        $i = 0;
        $valuesInsert = [];
        $paramsInsert = [];
        $updateCasesCodigoInterno = [];
        $updateCasesNombre = [];
        $updateCasesDescripcion = [];
        $updateCasesFechaModificacion = [];
        $updateCasesEstado = [];
        $paramsUpdate = [];
        $idsToUpdate = [];

        $this->entityManager->beginTransaction();

        try {
            foreach ($detalleZonasServicioHorarioDTOs as $detalleZonasServicioHorarioDTO) {
                // Verificar si el codigo_interno ya está en uso
                $existingCodigoInterno = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)
                    ->findOneBy(['codigo_interno' => $detalleZonasServicioHorarioDTO->getcodigo_interno()]);

                if ($existingCodigoInterno) {
                    throw new \RuntimeException("El Codigo Interno '{$detalleZonasServicioHorarioDTO->getcodigo_interno()}' ya está en uso.");
                }

                // Verificar si el nombre ya está en uso
                $existingNombre = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)
                    ->findOneBy(['nombre' => $detalleZonasServicioHorarioDTO->getNombre()]);

                if ($existingNombre) {
                    throw new \RuntimeException("El Nombre '{$detalleZonasServicioHorarioDTO->getNombre()}' ya está en uso.");
                }

                // Buscar las entidades relacionadas (id_servicios_productos_detalles, id_horario, id_zona_usuario)
                $idServiciosProductoDetalles = $this->entityManager->getRepository(ServiciosProductosDetalles::class)->findOneBy(['id' => $detalleZonasServicioHorarioDTO->getIdServiciosProductoDetalles()]);
                $idHorario = $this->entityManager->getRepository(Horario::class)->findOneBy(['id' => $detalleZonasServicioHorarioDTO->getIdHorario()]);
                $idZonaUsuario = $this->entityManager->getRepository(ZonaUsuarios::class)->findOneBy(['id' => $detalleZonasServicioHorarioDTO->getIdZonaUsuario()]);

                if (!$idServiciosProductoDetalles) {
                    throw new \RuntimeException("ServiciosProductoDetalles con ID {$detalleZonasServicioHorarioDTO->getIdServiciosProductoDetalles()} no encontrado.");
                }
                if (!$idHorario) {
                    throw new \RuntimeException("Horario con ID {$detalleZonasServicioHorarioDTO->getIdHorario()} no encontrado.");
                }
                if (!$idZonaUsuario) {
                    throw new \RuntimeException("ZonaUsuario con ID {$detalleZonasServicioHorarioDTO->getIdZonaUsuario()} no encontrado.");
                }

                // Si ya existe un registro con las mismas claves, preparar para actualización
                $existingRecord = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)
                    ->findOneBy([
                        'codigo_interno' => $detalleZonasServicioHorarioDTO->getcodigo_interno(),
                        'nombre' => $detalleZonasServicioHorarioDTO->getNombre()
                    ]);

                if ($existingRecord) {
                    // Preparar los valores de la actualización
                    $this->prepareUpdateValues(
                        $i,
                        $detalleZonasServicioHorarioDTO,
                        $existingRecord,
                        $updateCasesCodigoInterno,
                        $updateCasesNombre,
                        $updateCasesDescripcion,
                        $updateCasesFechaModificacion,
                        $updateCasesEstado,
                        $paramsUpdate,
                        $idsToUpdate
                    );
                } else {
                    // Preparar los valores para la inserción
                    $this->prepareInsertValues($i, $detalleZonasServicioHorarioDTO, $paramsInsert, $valuesInsert);
                }

                // Controlar el tamaño del batch: cada 20 registros
                if (($i + 1) % $batchSize === 0) {
                    if (!empty($valuesInsert)) {
                        $this->insertDetalleZonaServicioHorario($valuesInsert, $paramsInsert);
                    }

                    if (!empty($paramsUpdate)) {
                        $this->updateDetalleZonaServicioHorarioData(
                            $paramsUpdate,
                            $updateCasesCodigoInterno,
                            $updateCasesNombre,
                            $updateCasesDescripcion,
                            $updateCasesFechaModificacion,
                            $updateCasesEstado,
                            $idsToUpdate
                        );
                    }

                    // Reset para el siguiente lote
                    $valuesInsert = [];
                    $paramsInsert = [];
                    $idsToUpdate = [];
                }

                ++$i;
            }

            // Realizar las inserciones y actualizaciones restantes si hay registros pendientes
            if (!empty($valuesInsert)) {
                $this->insertDetalleZonaServicioHorario($valuesInsert, $paramsInsert);
            }

            if (!empty($paramsUpdate)) {
                $this->updateDetalleZonaServicioHorarioData(
                    $paramsUpdate,
                    $updateCasesCodigoInterno,
                    $updateCasesNombre,
                    $updateCasesDescripcion,
                    $updateCasesFechaModificacion,
                    $updateCasesEstado,
                    $idsToUpdate
                );
            }

            // Commit la transacción
            $this->entityManager->commit();
        } catch (ORMException | DBALException $e) {
            // Manejo de excepciones de base de datos
            $this->entityManager->rollback();
            $this->logger->error('Error al crear el Detalle Zonas Servicio Horario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear el Detalle Zonas Servicio Horario.');
        } catch (\Throwable $e) {
            // Manejo de otras excepciones
            $this->entityManager->rollback();
            $this->logger->error('Error inesperado al crear el Detalle Zonas Servicio Horario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    private function prepareInsertValues($i, $detalleZonasServicioHorarioDTO, &$paramsInsert, &$valuesInsert)
    {
        $codigo_interno = $this->generateInternalCode('DZSH-', 4, 0, DetalleZonaServicioHorario::class);
        $valuesInsert[] = '(:id_servicios_productos_detalles' . $i . ', :id_horario' . $i . ', :id_zona_usuario' . $i . ', :nombre' . $i . ', :descripcion' . $i . ', :codigo_interno' . $i . ', :fecha_creacion' . $i . ', :fecha_modificacion' . $i . ', :estado' . $i . ')';
        $paramsInsert['id_servicios_productos_detalles' . $i] = $detalleZonasServicioHorarioDTO->getIdServiciosProductoDetalles();
        $paramsInsert['id_horario' . $i] = $detalleZonasServicioHorarioDTO->getIdHorario();
        $paramsInsert['id_zona_usuario' . $i] = $detalleZonasServicioHorarioDTO->getIdZonaUsuario();
        $paramsInsert['nombre' . $i] = $detalleZonasServicioHorarioDTO->getNombre();
        $paramsInsert['descripcion' . $i] = $detalleZonasServicioHorarioDTO->getDescripcion();
        $paramsInsert['codigo_interno' . $i] = $codigo_interno;
        $paramsInsert['fecha_creacion' . $i] = $detalleZonasServicioHorarioDTO->getfecha_creacion()->format('Y-m-d H:i:s');
        $paramsInsert['fecha_modificacion' . $i] = $detalleZonasServicioHorarioDTO->getfecha_modificacion() ? $detalleZonasServicioHorarioDTO->getFechaModificacion()->format('Y-m-d H:i:s') : null;
        $paramsInsert['estado' . $i] = $detalleZonasServicioHorarioDTO->getEstado();
    }

    private function prepareUpdateValues(
        $i,
        $detalleZonasServicioHorarioDTO,
        $existingRecord,
        &$updateCasesCodigoInterno,
        &$updateCasesNombre,
        &$updateCasesDescripcion,
        &$updateCasesFechaModificacion,
        &$updateCasesEstado,
        &$paramsUpdate,
        &$idsToUpdate
    ) {
        // Preparar los casos de actualización para cada campo
        $updateCasesCodigoInterno[] = "WHEN id = :id_detalle_zona_servicio_horario" . $i . " THEN :codigo_interno" . $i;
        $updateCasesNombre[] = "WHEN id = :id_detalle_zona_servicio_horario" . $i . " THEN :nombre" . $i;
        $updateCasesDescripcion[] = "WHEN id = :id_detalle_zona_servicio_horario" . $i . " THEN :descripcion" . $i;
        $updateCasesFechaModificacion[] = "WHEN id = :id_detalle_zona_servicio_horario" . $i . " THEN :fecha_modificacion" . $i;
        $updateCasesEstado[] = "WHEN id = :id_detalle_zona_servicio_horario" . $i . " THEN :estado" . $i;

        // Agregar parámetros para la actualización
        $paramsUpdate["id_detalle_zona_servicio_horario" . $i] = $existingRecord->getId();
        $paramsUpdate["codigo_interno" . $i] = $existingRecord->getCodigoInterno();
        $paramsUpdate["nombre" . $i] = $detalleZonasServicioHorarioDTO->getNombre();
        $paramsUpdate["descripcion" . $i] = $detalleZonasServicioHorarioDTO->getDescripcion();
        $paramsUpdate["fecha_modificacion" . $i] = (new \DateTime())->format('Y-m-d H:i:s');
        $paramsUpdate["estado" . $i] = $detalleZonasServicioHorarioDTO->getEstado();

        // Agregar el id a la lista de ids a actualizar
        $idsToUpdate[] = $existingRecord->getId();
    }

    private function insertDetalleZonaServicioHorario(array $valuesInsert, array $paramsInsert): void
    {
        $sql = "INSERT INTO catalogo.detalle_zona_servicio_horario (id_servicios_productos_detalles, id_horario, id_zona_usuario, nombre, descripcion, codigo_interno, fecha_creacion, fecha_modificacion, estado) 
            VALUES " . implode(', ', $valuesInsert);
        $this->entityManager->getConnection()->executeQuery($sql, $paramsInsert);
    }

    private function updateDetalleZonaServicioHorarioData(
        $paramsUpdate,
        $updateCasesCodigoInterno,
        $updateCasesNombre,
        $updateCasesDescripcion,
        $updateCasesFechaModificacion,
        $updateCasesEstado,
        $idsToUpdate
    ): void {
        $whereClause = "WHERE id IN (" . implode(', ', $idsToUpdate) . ")";

        $sqlUpdateFinal = "UPDATE catalogo.detalle_zona_servicio_horario SET 
        codigo_interno = CASE " . rtrim(implode(' ', $updateCasesCodigoInterno), ', ') . " END,
        nombre = CASE " . rtrim(implode(' ', $updateCasesNombre), ', ') . " END,
        descripcion = CASE " . rtrim(implode(' ', $updateCasesDescripcion), ', ') . " END,
        fecha_modificacion = CASE " . rtrim(implode(' ', $updateCasesFechaModificacion), ', ') . " END,
        estado = CASE " . rtrim(implode(' ', $updateCasesEstado), ', ') . " END " . $whereClause;

        $this->entityManager->getConnection()->executeQuery($sqlUpdateFinal, $paramsUpdate);
    }


    public function updateDetalleZonasServicioHorario(int $id, DetalleZonasServicioHorarioDTO $detalleZonasServicioHorarioDTO): void
    {
        try {
            $detalleZonasServicioHorario = $this->getEntityById($id);
            if (!$detalleZonasServicioHorario) {
                throw new \RuntimeException('El Servicio del Producto no encontrado.');
            }

            $existingcodigo_interno = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)
                ->findOneBy(['codigo_interno' => $detalleZonasServicioHorarioDTO->getcodigo_interno()]);

            if ($existingcodigo_interno) {
                throw new \RuntimeException('El Codigo Interno ya está en uso.');
            }


            $existingNombre = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)
                ->findOneBy(['nombre' => $detalleZonasServicioHorarioDTO->getNombre()]);


            if ($existingNombre) {
                throw new \RuntimeException('El Nombre ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($detalleZonasServicioHorario, $detalleZonasServicioHorarioDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteDetalleZonasServicioHorario(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar el Servicio del Producto: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
