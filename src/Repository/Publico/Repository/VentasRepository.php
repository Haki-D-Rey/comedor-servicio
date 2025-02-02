<?php

namespace App\Repository\Publico\Repository;

use App\DTO\Publico\VentasDTO;
use App\Entity\DetalleZonaServicioHorario;
use App\Entity\Horario;
use App\Entity\ListaCatalogo;
use App\Entity\ListaCatalogoDetalle;
use App\Entity\Publico\ClientesCreditoPeriodico;
use App\Entity\Publico\DetalleZonaServicioHorarioClienteFacturacion;
use App\Entity\Publico\Ventas;
use App\Entity\ZonaUsuarios;
use App\Repository\GenericRepository;
use App\Repository\Publico\Interface\VentasRepositoryInterface;
use App\Services\Publico\ClientesServices;
use App\Services\Utils\UtilServices;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use InvalidArgumentException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class VentasRepository extends GenericRepository implements VentasRepositoryInterface
{
    private $logger;
    private ClientesServices $clientesServices;
    private UtilServices $utilServices;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface, ClientesServices $clientesServices, UtilServices $utilServices)
    {
        parent::__construct($entityManager, Ventas::class);
        $this->logger = $loggerInterface;
        $this->clientesServices = $clientesServices;
        $this->utilServices = $utilServices;
    }

    public function getAllVentas(): array
    {
        try {
            $clientesCredito = $this->getAllEntities();
            return array_map(function (Ventas $ventas) {
                return new VentasDTO(
                    $ventas->getId(),
                    $ventas->getUuid(),
                    $ventas->getDetalleZonaServicioHorarioClienteFacturacionId(),
                    $ventas->getClienteId(),
                    $ventas->getCantidadFacturada(),
                    $ventas->isTicketAnulado(),
                    $ventas->getCantidadAnulada(),
                    $ventas->getFechaEmision(),
                    $ventas->getFechaModificacion(),
                    $ventas->getEstadoVentaId(),
                    $ventas->isEstado()
                );
            }, $clientesCredito);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener clientes con crédito periódico: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getVentaById(int $id): ?VentasDTO
    {
        try {
            $ventas = $this->getEntityById($id);
            if (!$ventas) {
                return null;
            }

            return new VentasDTO(
                $ventas->getId(),
                $ventas->getUuid(),
                $ventas->getDetalleZonaServicioHorarioClienteFacturacionId(),
                $ventas->getClienteId(),
                $ventas->getCantidadFacturada(),
                $ventas->isTicketAnulado(),
                $ventas->getCantidadAnulada(),
                $ventas->getFechaEmision(),
                $ventas->getFechaModificacion(),
                $ventas->getEstadoVentaId(),
                $ventas->isEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener cliente crédito periódico por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createVenta(array $ventasDTOs): array
    {
        $batchSize = 20;
        $i = 0;
        $valuesInsert = [];
        $paramsInsert = [];
        $paramsUpdate = [];
        $updateCasesCreditoUsado = [];
        $updateCasesCreditoDisponible = [];
        $updateCasesModificacion = [];
        $idsToUpdate = [];
        $horaActual = new \DateTime();

        $this->entityManager->beginTransaction();

        try {
            $idCatalogoBusqueda = $this->getCatalogoBusqueda();
            $catalogoEstadoVentas = $this->getEstadoVenta($idCatalogoBusqueda);
            $idUsuario = $_SESSION['user_id'];
            $filtroValidaciones = [];

            foreach ($ventasDTOs as $ventasDTO) {
                $response = $this->validateVenta($ventasDTO, $idUsuario, $horaActual, $filtroValidaciones);
                $filtroErrores = $response->filtroErrores;
                $data = $response->data;
                array_push($filtroValidaciones, $filtroErrores);
                if (
                    $filtroErrores["error_punto_venta"]
                    || $filtroErrores["error_horarios"]
                    || $filtroErrores["error_validar_credencial"]
                    || $filtroErrores["error_creditos_disponibles"]
                    || $filtroErrores["error_limite_credito"]
                ) {
                    continue;
                }

                $this->prepareInsertValues($ventasDTO, $i, $paramsInsert, $valuesInsert, $catalogoEstadoVentas, $horaActual, $data->id_cliente, $data->idDetalleZonaServicioHorarioClienteIdentificacion);
                $this->prepareUpdateValues(
                    $i,
                    $data->idDetalleZonaServicioHorarioClienteIdentificacion,
                    $ventasDTO->getCantidadFacturada(),
                    $horaActual,
                    $updateCasesCreditoUsado,
                    $updateCasesCreditoDisponible,
                    $updateCasesModificacion,
                    $paramsUpdate,
                    $idsToUpdate
                );

                // Batch control
                if (($i + 1) % $batchSize === 0) {
                    $this->executeBatchInsert($valuesInsert, $paramsInsert);
                    $valuesInsert = [];
                    $paramsInsert = [];
                }

                ++$i;
            }

            // Insert remaining records
            if (!empty($valuesInsert)) {
                $this->executeBatchInsert($valuesInsert, $paramsInsert);
            }

            // Execute mass update for credits
            if (!empty($idsToUpdate)) {
                $this->updateClienteCredito($paramsUpdate, $updateCasesCreditoUsado, $updateCasesCreditoDisponible, $updateCasesModificacion, $idsToUpdate, $horaActual);
            }

            $this->entityManager->commit();
            return $filtroValidaciones;
        } catch (\RuntimeException | ORMException | DBALException | \Throwable $e) {
            $this->handleException($e, $filtroValidaciones);
        } catch (ORMException | DBALException $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error al crear clientes crédito periódico en la base de datos: ' . $e->getMessage(), ['exception' => $e, 'errores' => $filtroValidaciones]);
            throw new \RuntimeException('Error al crear clientes crédito periódico.');
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error inesperado al crear clientes crédito periódico: ' . $e->getMessage(), ['exception' => $e, 'errores' => $filtroValidaciones]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    private function getCatalogoBusqueda(): ListaCatalogo
    {
        return $this->entityManager->getRepository(ListaCatalogo::class)->findOneBy(['codigo_interno' => 'LCU-004']);
    }

    private function getEstadoVenta(ListaCatalogo $idCatalogoBusqueda): ListaCatalogoDetalle
    {
        return $this->entityManager->getRepository(ListaCatalogoDetalle::class)->findOneBy(['id_lista_catalogo' => $idCatalogoBusqueda->getId(), 'estado' => true]);
    }

    private function validateVenta($ventasDTO, $idUsuario, $horaActual, &$filtroValidaciones)
    {
        $filtroErrores = [
            "idDetalleZonaServicioHorario" => "",
            "cod_identificacion" => "",
            "codigo_internoIF" => "",
            "error_punto_venta" => "",
            "error_horarios" => "",
            "error_validar_credencial" => "",
            "error_creditos_disponibles" => "",
            "error_limite_credito" => ""
        ];

        $filtroErrores['idDetalleZonaServicioHorario'] = $ventasDTO->getIdDetalleZonaServicioHorario() || 0;
        $filtroErrores['cod_identificacion'] = $ventasDTO->getCodIdentificacion() || "";
        $filtroErrores['codigo_internoIF'] = $ventasDTO->getCodigoInternoIF() || "";

        // Validating point of sale
        $this->validatePuntoVenta($ventasDTO, $idUsuario, $filtroErrores);

        // Validating horario
        $this->validateHorarios($ventasDTO, $horaActual, $filtroErrores);

        // Validating credenciales
        if ($ventasDTO->getCodigoInternoIF() == 'ITF-001') {
            $findValueJson = $this->validateCredencial($ventasDTO, $filtroErrores);
        } else {
            $filtro = [
                "id_cliente" => (int) $ventasDTO->getCodIdentificacion(),
                "id_detalle_zona_servicio_horario" =>  $ventasDTO->getIdDetalleZonaServicioHorario()
            ];
            $clienteIdentificacion = $this->clientesServices->getClientsRelationalIdentification($filtro);

            $findValueJson = (object)[
                'status' => true,
                'cod_identificacion' => json_decode($clienteIdentificacion[0]['json_identificacion'])[0]->codigo_identificador,
                'id_detalle_cliente_identificacion_facturacion' => $clienteIdentificacion[0]['id_detalle_cliente_identificacion_facturacion'],
                'id_cliente' => $ventasDTO->getCodIdentificacion(),
            ];
        }

        // Validating credits
        $idDetalleZonaServicioHorarioClienteIdentificacion = $this->validateCreditosDisponibles($ventasDTO, $filtroErrores, $horaActual, $findValueJson);
        if ($idDetalleZonaServicioHorarioClienteIdentificacion) {
            $findValueJson->idDetalleZonaServicioHorarioClienteIdentificacion = $idDetalleZonaServicioHorarioClienteIdentificacion;
        }

        return (object) ["filtroErrores" => $filtroErrores, "data" => $findValueJson];
    }

    private function validatePuntoVenta($ventasDTO, $idUsuario, &$filtroErrores)
    {
        $DetalleZonaServicioHorario = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)->findOneBy(['id' => $ventasDTO->getIdDetalleZonaServicioHorario(), 'estado' => true]);
        $zonaUsuario = $this->entityManager->getRepository(ZonaUsuarios::class)->findOneBy(['id' => $DetalleZonaServicioHorario->getZonaUsuario()->getId(), 'estado' => true]);

        if (!(isset($zonaUsuario) && $zonaUsuario->getUsuario()->getId() == $idUsuario)) {
            $filtroErrores["error_punto_venta"] = 'El Cliente no tiene puntos de Créditos Válidos para este Evento';
        }
    }

    private function validateHorarios($ventasDTO, $horaActual, &$filtroErrores)
    {
        $DetalleZonaServicioHorario = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)->findOneBy(['id' => $ventasDTO->getIdDetalleZonaServicioHorario(), 'estado' => true]);
        $horario = $this->entityManager->getRepository(Horario::class)->findOneBy(['id' => $DetalleZonaServicioHorario->getHorario()->getId(), 'estado' => true]);

        if (!$horario) {
            $filtroErrores["error_horarios"] = 'No se encontró el horario para este evento';
        } else {
            if ($horario->getcodigo_interno() != 'HR-005') {
                $horaInicio = $horario->getPeriodoInicio();
                $horaFin = $horario->getPeriodoFin();

                $horaActualFormatted = $horaActual->format('H:i');
                $horaInicioFormatted = $horaInicio->format('H:i');
                $horaFinFormatted = $horaFin->format('H:i');

                if ($horaActualFormatted < $horaInicioFormatted || $horaActualFormatted > $horaFinFormatted) {
                    $filtroErrores["error_horarios"] = "Este Servicio no está dentro del rango del horario Establecido.";
                }
            }
        }
    }

    private function validateCredencial($ventasDTO, &$filtroErrores): object
    {
        // $listaClientes = $this->entityManager->getRepository(DetalleClienteIdentificacionFacturacion::class)->findAll();
        // Definir la consulta SQL
        $query = "
                    SELECT
                        id,
                        id_cliente,
                        jsonb_extract_path_text(value, 'codigo_identificador') AS codigo_identificador,
                        jsonb_extract_path_text(value, 'codigo_interno') AS codigo_interno
                    FROM 
                        public.detalle_cliente_identificacion_facturacion,
                        jsonb_array_elements(json_identificacion) AS value
                    WHERE 
                        jsonb_extract_path_text(value, 'codigo_interno') = :codigo_interno
                        AND jsonb_extract_path_text(value, 'codigo_identificador') = :codigo_identificador
                        AND estado = true
                ";

        // Definir los parámetros para la consulta
        $params = [
            'codigo_interno' => 'ITF-001', // Cambié estos valores a los que se obtienen del DTO
            'codigo_identificador' => str_pad($ventasDTO->getCodIdentificacion(), 10, '0', STR_PAD_LEFT)
        ];

        // Ejecutar la consulta usando Doctrine DBAL
        $result = $this->entityManager->getConnection()->executeQuery($query, $params);

        // Obtener el resultado
        $rows = $result->fetchAllAssociative(); // Devuelve un array asociativo con los resultados

        // Verificar si la consulta devolvió resultados
        if (count($rows) > 0) {
            // Si se encontró el cliente, asignamos los datos al objeto de retorno
            $findValueJson = (object)[
                'status' => true,
                'cod_identificacion' => $rows[0]['codigo_identificador'],
                'id_detalle_cliente_identificacion_facturacion' => $rows[0]['id'],
                'id_cliente' => $rows[0]['id_cliente'],
            ];
        } else {
            // Si no se encontró el cliente, establecemos el status como false
            $findValueJson = (object)[
                'status' => false,
                'value' => null
            ];
        }

        // Si el cliente no fue encontrado, agregar error al filtro
        if (!$findValueJson->status) {
            $filtroErrores["error_validar_credencial"] = "Cliente con el número de identificación " . $ventasDTO->getCodIdentificacion() . " no fue encontrado";
        }

        return $findValueJson;
    }

    private function validateCreditosDisponibles($ventasDTO, &$filtroErrores, $horaActual, $findValueJson): int
    {
        $idDetalleClienteIdentifiacionFacturacion = $findValueJson->id_detalle_cliente_identificacion_facturacion;

        $DetalleZonaServicioHorarioClienteIdentificacion = $this->entityManager->getRepository(DetalleZonaServicioHorarioClienteFacturacion::class)
            ->findOneBy([
                'idDetalleClienteIdentificacionFacturacion' => $idDetalleClienteIdentifiacionFacturacion,
                'idDetalleZonaServicioHorario' => $ventasDTO->getIdDetalleZonaServicioHorario(),
                'estado' => true
            ]);

        if (empty($DetalleZonaServicioHorarioClienteIdentificacion)) {
            $filtroErrores["error_creditos_disponibles"] = "Cliente no tiene creditos asignado a este Evento";
            return 0;
        }

        $ClienteCredito = $this->entityManager->getRepository(ClientesCreditoPeriodico::class)
            ->findOneBy(['detalleZonaServicioHorarioClienteFacturacion' => $DetalleZonaServicioHorarioClienteIdentificacion->getId(), 'estado' => true]);

        if (empty($ClienteCredito)) {
            $filtroErrores["error_creditos_disponibles"] = "Cliente no tiene creditos asignado a este Evento";
            return 0;
        }

        $cantidadFacturada = $ventasDTO->getCantidadFacturada();
        $servicio = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)->findOneBy(['id' => $ventasDTO->getIdDetalleZonaServicioHorario(), 'estado' => true])->getServiciosProductosDetalles();

        if (!$ClienteCredito->getExisteCantidadDisponible($cantidadFacturada)) {
            $ultimaVentas = $this->entityManager->getRepository(Ventas::class)
                ->findOneBy(
                    ['detalleZonaServicioHorarioClienteFacturacion' => $DetalleZonaServicioHorarioClienteIdentificacion->getId(), 'estado' => true],
                    ['fechaEmision' => 'DESC']
                );
            $servicio = $this->entityManager->getRepository(DetalleZonaServicioHorario::class)->findOneBy(['id' => $ventasDTO->getIdDetalleZonaServicioHorario()])->getServiciosProductosDetalles();
            $filtroErrores["error_creditos_disponibles"] = "No se puede realizar la facturación porque el cliente no cuenta con crédito disponible. La última venta registrada fue el " . $ultimaVentas->getFechaEmision()->format('d/m/Y H:i:s') . ", donde se facturó una cantidad de " . $ultimaVentas->getCantidadFacturada() . " del servicio " . $servicio->getNombre() . ".";
            return 0;
        }

        $fechaInicio = $ClienteCredito->getPeriodoInicial();
        $fechaFin = $ClienteCredito->getPeriodoFinal();
        $fechaActualFormatted = $horaActual->format('Y-m-d');
        $fechaInicioFormatted = $fechaInicio->format('Y-m-d');
        $fechaFinFormatted = $fechaFin->format('Y-m-d');

        if ($fechaActualFormatted < $fechaInicioFormatted || $fechaActualFormatted > $fechaFinFormatted) {
            $filtroErrores["error_limite_credito"] = "Este Servicio no está dentro del rango de fecha permitido.";
            return 0;
        }

        return $DetalleZonaServicioHorarioClienteIdentificacion->getId();
    }

    private function prepareInsertValues($ventasDTO, $i, &$paramsInsert, &$valuesInsert, $catalogoEstadoVentas, $horaActual, $idCliente, $idDetalleZonaServicioHorarioClienteIdentificacion)
    {
        $ventas = new Ventas();
        $uuid = $ventas->getUuid();
        $cantidadFacturada = $ventasDTO->getCantidadFacturada();

        $valuesInsert[] = '(:uuid' . $i . ', :id_detalle_zona_servicio_horario_cliente_facturacion' . $i . ', :id_cliente' . $i . ', :cantidad_facturada' . $i . ', :cantidad_anulada' . $i . ', :id_estado_venta' . $i . ', :fecha_emision' . $i . ', :fecha_modificacion' . $i . ')';
        $paramsInsert['uuid' . $i] = $uuid;
        $paramsInsert['id_detalle_zona_servicio_horario_cliente_facturacion' . $i] = $idDetalleZonaServicioHorarioClienteIdentificacion;
        $paramsInsert['id_cliente' . $i] = $idCliente;
        $paramsInsert['cantidad_facturada' . $i] = $cantidadFacturada;
        $paramsInsert['cantidad_anulada' . $i] = 0;
        $paramsInsert['id_estado_venta' . $i] = $catalogoEstadoVentas->getId();
        $paramsInsert['fecha_emision' . $i] = $horaActual->format('Y-m-d H:i:s');
        $paramsInsert['fecha_modificacion' . $i] = null;
    }

    private function prepareUpdateValues(
        $i,
        $idDetalleZonaServicioHorarioClienteIdentificacion,
        $cantidadFacturada,
        $horaActual,
        &$updateCasesCreditoUsado,
        &$updateCasesCreditoDisponible,
        &$updateCasesModificacion,
        &$paramsUpdate,
        &$idsToUpdate
    ) {
        // Prepare the update cases
        $updateCasesCreditoUsado[] = "WHEN id_detalle_zona_servicio_horario_cliente_facturacion = :id_credito_{$i} THEN cantidad_credito_usado + :cantidad_facturada_{$i}";
        $updateCasesCreditoDisponible[] = "WHEN id_detalle_zona_servicio_horario_cliente_facturacion = :id_credito_{$i} THEN cantidad_credito_limite - (cantidad_credito_usado + :cantidad_facturada_{$i})";
        $updateCasesModificacion[] = "WHEN id_detalle_zona_servicio_horario_cliente_facturacion = :id_credito_{$i} THEN :fecha_modificacion";

        // Add parameters for the update
        $paramsUpdate["id_credito_{$i}"] = $idDetalleZonaServicioHorarioClienteIdentificacion;
        $paramsUpdate["cantidad_facturada_{$i}"] = $cantidadFacturada;
        $paramsUpdate["fecha_modificacion"] = $horaActual->format('Y-m-d H:i:s');

        // Add the id to the list of ids to update
        $idsToUpdate[] = $idDetalleZonaServicioHorarioClienteIdentificacion;
    }


    private function executeBatchInsert($valuesInsert, $paramsInsert)
    {
        if (!empty($valuesInsert)) {
            $this->insertVentasFacturacion($valuesInsert, $paramsInsert);
        }
    }

    private function updateClienteCredito($paramsUpdate, $updateCasesCreditoUsado, $updateCasesCreditoDisponible, $updateCasesModificacion, $idsToUpdate, $horaActual)
    {
        $updateQuery = "
        UPDATE public.cliente_credito_periodico
        SET
            cantidad_credito_usado = CASE 
                " . implode(' ', $updateCasesCreditoUsado) . " 
            END,
            cantidad_credito_disponible = CASE 
                " . implode(' ', $updateCasesCreditoDisponible) . " 
            END,
            fecha_modificacion = CASE 
                " . implode(' ', $updateCasesModificacion) . " 
            END::TIMESTAMP
        WHERE id_detalle_zona_servicio_horario_cliente_facturacion IN (" . implode(', ', $idsToUpdate) . ")";

        $this->entityManager->getConnection()->executeQuery($updateQuery, [
            'fecha_modificacion' => (new \DateTime())->format('Y-m-d H:i:s')
        ] + $paramsUpdate);
    }

    private function handleException($e, $filtroValidaciones)
    {
        $this->entityManager->rollback();
        $this->logger->error('Error al crear Ventas del Evento: ' . $e->getMessage(), ['exception' => $e, 'errores' => $filtroValidaciones]);
        throw new \RuntimeException($e->getMessage());
    }

    private function prepareUpdateParams(int $i, $clienteCreditoDTO, $existingRecord): array
    {
        // Creamos el objeto stdClass para almacenar las partes del SQL
        $updateParams = new \stdClass();

        // Inicializar las partes del UPDATE en el stdClass
        $updateParams->sqlUpdatePI = "";
        $updateParams->sqlUpdatePF = "";
        $updateParams->sqlUpdateCCL = "";
        $updateParams->sqlUpdateCCU = "";
        $updateParams->sqlUpdateCCD = "";
        $updateParams->sqlUpdateFM = "";

        // Preparar las partes de la consulta para cada campo
        $updateParams->sqlUpdatePI .= " WHEN id_detalle_zona_servicio_horario_cliente_facturacion = :id_detalle_zona_servicio_horario_cliente_facturacion" . $i . " THEN :periodo_inicial" . $i . " ";
        $updateParams->sqlUpdatePF .= " WHEN id_detalle_zona_servicio_horario_cliente_facturacion = :id_detalle_zona_servicio_horario_cliente_facturacion" . $i . " THEN :periodo_final" . $i . " ";
        $updateParams->sqlUpdateCCL .= " WHEN id_detalle_zona_servicio_horario_cliente_facturacion = :id_detalle_zona_servicio_horario_cliente_facturacion" . $i . " THEN :cantidad_credito_limite" . $i . " ";
        $updateParams->sqlUpdateCCU .= " WHEN id_detalle_zona_servicio_horario_cliente_facturacion = :id_detalle_zona_servicio_horario_cliente_facturacion" . $i . " THEN :cantidad_credito_usado" . $i . " ";
        $updateParams->sqlUpdateCCD .= " WHEN id_detalle_zona_servicio_horario_cliente_facturacion = :id_detalle_zona_servicio_horario_cliente_facturacion" . $i . " THEN :cantidad_credito_disponible" . $i . " ";
        $updateParams->sqlUpdateFM .= " WHEN id_detalle_zona_servicio_horario_cliente_facturacion = :id_detalle_zona_servicio_horario_cliente_facturacion" . $i . " THEN :fecha_modificacion" . $i . " ";

        // Almacenar los parámetros para la actualización
        $paramsUpdate = [];
        $paramsUpdate['id' . $i] = $existingRecord->getId();
        $paramsUpdate['id_detalle_zona_servicio_horario_cliente_facturacion' . $i] = $clienteCreditoDTO->getIdDetalleZonaServicioHorarioClienteFacturacion();
        $paramsUpdate['periodo_inicial' . $i] = $clienteCreditoDTO->getPeriodoInicial()->format('Y-m-d');
        $paramsUpdate['periodo_final' . $i] = $clienteCreditoDTO->getPeriodoFinal()->format('Y-m-d');
        $paramsUpdate['cantidad_credito_limite' . $i] = $clienteCreditoDTO->getCantidadCreditoLimite();
        $paramsUpdate['cantidad_credito_usado' . $i] = $clienteCreditoDTO->getCantidadCreditoUsado();
        $paramsUpdate['cantidad_credito_disponible' . $i] = $clienteCreditoDTO->getCantidadCreditoDisponible();
        $paramsUpdate['fecha_modificacion' . $i] = (new \DateTime())->format('Y-m-d H:i:s');

        // Retornar ambos el stdClass y los parámetros en un array
        return [
            'updateParams' => $updateParams,
            'paramsUpdate' => $paramsUpdate
        ];
    }

    public function updateClienteCreditoPeriodicoData(array $paramsUpdate, object $updateParams, array $ids): void
    {
        $whereClause = "WHERE id IN (" . implode(', ', $ids) . ")";

        $sqlUpdateFinal = "UPDATE cliente_credito_periodico SET 
        periodo_inicial = CASE " . rtrim($updateParams->sqlUpdatePI, ', ') . " END::DATE,
        periodo_final = CASE " . rtrim($updateParams->sqlUpdatePF, ', ') . " END::DATE,
        cantidad_credito_limite = CASE " . rtrim($updateParams->sqlUpdateCCL, ', ') . " END::INTEGER,
        cantidad_credito_usado = CASE " . rtrim($updateParams->sqlUpdateCCU, ', ') . " END::INTEGER,
        cantidad_credito_disponible = CASE " . rtrim($updateParams->sqlUpdateCCD, ', ') . " END::INTEGER,
        fecha_modificacion = CASE " . rtrim($updateParams->sqlUpdateFM, ', ') . " END::TIMESTAMP " . $whereClause;

        // Ejecutar la actualización masiva
        $this->entityManager->getConnection()->executeStatement($sqlUpdateFinal, $paramsUpdate);
    }

    public function updateVenta(int $id, VentasDTO $ventasDTOs): void
    {
        $batchSize = 20;
        $i = 0;
        $paramsUpdate = [];
        $updateParams = new \stdClass();
        $ids = [];

        $this->entityManager->beginTransaction();

        try {
            foreach ($ventasDTOs as $ventaDTO) {
                $cliente = $this->entityManager->getRepository(DetalleZonaServicioHorarioClienteFacturacion::class)
                    ->find($ventaDTO->getIdDetalleZonaServicioHorarioClienteFacturacion());

                if (!$cliente) {
                    throw new \RuntimeException("Cliente con ID {$ventaDTO->getIdDetalleZonaServicioHorarioClienteFacturacion()} no encontrado.");
                }

                // Verificar si ya existe un registro para este cliente
                $existingRecord = $this->entityManager->getRepository(ClientesCreditoPeriodico::class)
                    ->findOneBy([
                        'idDetalleZonaServicioHorarioClienteFacturacion' => $cliente
                    ]);

                // Si existe un registro, procedemos a actualizarlo
                if ($existingRecord) {
                    $result = $this->prepareUpdateParams($i, $ventaDTO, $existingRecord);
                    $updateParamsPart = $result['updateParams'];

                    // Acumulamos las partes del SQL en el objeto updateParams
                    $updateParams->sqlUpdatePI .= $updateParamsPart->sqlUpdatePI;  // Concatenamos
                    $updateParams->sqlUpdatePF .= $updateParamsPart->sqlUpdatePF;
                    $updateParams->sqlUpdateCCL .= $updateParamsPart->sqlUpdateCCL;
                    $updateParams->sqlUpdateCCU .= $updateParamsPart->sqlUpdateCCU;
                    $updateParams->sqlUpdateCCD .= $updateParamsPart->sqlUpdateCCD;
                    $updateParams->sqlUpdateFM .= $updateParamsPart->sqlUpdateFM;

                    // Acumulamos los parámetros para la actualización
                    $paramsUpdate = array_merge($paramsUpdate, $result['paramsUpdate']);
                    $ids[] = ":id" . $i;  // Almacenamos los ID de las actualizaciones
                } else {
                    // Si no existe el registro, lanzar un error
                    throw new \RuntimeException("No se encuentra un registro de crédito periódico para el cliente con ID {$ventaDTO->getIdDetalleZonaServicioHorarioClienteFacturacion()}.");
                }

                // Controlar el tamaño del batch para actualizar
                if (($i + 1) % $batchSize === 0) {
                    // Si hay registros para actualizar, realizar la actualización masiva
                    if (!empty($paramsUpdate)) {
                        $this->updateClienteCreditoPeriodicoData($paramsUpdate, $updateParams, $ids);
                    }

                    // Resetear valores y parámetros para el siguiente batch
                    $updateParams = new \stdClass();
                    $paramsUpdate = [];
                    $ids = [];
                }

                ++$i;
            }

            // Realizar la actualización final si hay registros pendientes
            if (!empty($paramsUpdate)) {
                $this->updateClienteCreditoPeriodicoData($paramsUpdate, $updateParams, $ids);
            }

            // Commit la transacción
            $this->entityManager->commit();
        } catch (\RuntimeException $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error al actualizar clientes crédito periódico: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        } catch (ORMException | DBALException $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error al actualizar clientes crédito periódico en la base de datos: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al actualizar clientes crédito periódico.');
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error inesperado al actualizar clientes crédito periódico: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteVenta(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0); // Marcar como eliminado
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar cliente crédito periódico: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function FindValueCodIdentificacion($listaClientes, $cod_identificacion)
    {
        $cliente = [];
        $filteredCliente = array_filter(
            $listaClientes,
            fn($item) => isset($item->getJsonIdentificacion()[0]['codigo_identificador']) &&
                $item->getJsonIdentificacion()[0]['codigo_identificador'] == $cod_identificacion
        );

        if (!empty($filteredCliente)) {
            $cliente[0] = reset($filteredCliente);
        }

        if (!empty($cliente)) {
            $codigoIdentificador = $cliente[0]->getJsonIdentificacion()[0]['codigo_identificador'];
            $id_dzshcf = $cliente[0]->getId();
            return (object)[
                'status' => true,
                'cod_identificacion' => $codigoIdentificador,
                'id_detalle_cliente_identificacion_facturacion' => $id_dzshcf,
                'id_cliente' => $cliente[0]->getIdCliente()
            ];
        }

        // Si no se encuentra, retornar un objeto con status false
        return (object)['status' => false, 'value' => null];
    }


    public function insertVentasFacturacion(array $valuesInsert, array $paramsInsert): void
    {

        try {

            $sqlInsert = "INSERT INTO public.ventas(uuid, id_detalle_zona_servicio_horario_cliente_facturacion, id_cliente, cantidad_facturada, cantidad_anulada, id_estado_venta ,fecha_emision ,fecha_modificacion) 
                          VALUES " . implode(', ', $valuesInsert);
            $this->entityManager->getConnection()->executeQuery($sqlInsert, $paramsInsert);
        } catch (\Exception $e) {
            $this->logger->error('Error al insertar clientes crédito periódico: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al insertar clientes crédito periódico: ' . $e->getMessage());
        }
    }

    public function getReportVentas(array $filtros): array
    {
        $tipo = $filtros['reporttype'];

        $templateBase = [
            "id_zona" => "id_zona",
            "fecha_emision" => "fecha_emision",
            "evento" => "evento",
            "id_usuario" => "id_usuario",
            "facturador" => "facturador",
            "id" => "id",
            "nombre_servicio" => "nombre_servicio",
            "orden" => "orden",
            "cantidad_total_facturada" => "cantidad_total_facturada"
        ];

        if ($tipo === 'details') {
            $templateBase['cliente'] = 'cliente';
            $templateBase['clie_docnum'] = 'clie_docnum';
        }

        $reportFunctions = [
            'consolidated' => 'getConsolidatedReportByZone',
            'details' => 'getDetailedReportByClient',
        ];

        if (!isset($reportFunctions[$tipo])) {
            throw new InvalidArgumentException("Tipo de reporte no válido: $tipo");
        }

        $result = $this->{$reportFunctions[$tipo]}($filtros);
        $mutatedArray = $this->utilServices->transformArray($result, $templateBase, true, ['id_zona', 'fecha_emision']);

        return $mutatedArray;
    }


    public function getDetailedReportByClient(array $filtros): array
    {
        try {
            // Extract the filters
            $id_zona = $filtros['id_zone'];
            $fecha_inicio = $filtros['datestart'];
            $fecha_fin = $filtros['dateend'];

            // Generate dynamic placeholders for the "IN" clause
            $placeholders = [];
            foreach ($id_zona as $index => $value) {
                $placeholders[] = ":id_zone{$index}";
            }

            // Build the SQL query
            $query = "
                        SELECT zu.id_zona, 
                            z.nombre as Evento, 
                            zu.id_usuario, 
                            CONCAT(u.nombres, ' ' ,u.apellidos) AS Facturador, 
                            CONCAT(c.nombres, ' ', c.apellidos) AS Cliente, 
                            c.clie_docnum, 
                            spd.nombre AS nombre_servicio, 
                            SUM(v.cantidad_facturada) AS cantidad_total_facturada, 
                            DATE(v.fecha_emision) AS fecha_emision
                        FROM seguridad.zona_usuario zu
                        INNER JOIN catalogo.detalle_zona_servicio_horario dzsh
                            ON zu.id = dzsh.id_zona_usuario
                        INNER JOIN catalogo.servicios_productos_detalles spd
                            ON spd.id = dzsh.id_servicios_productos_detalles
                        INNER JOIN public.detalle_zona_servicio_horario_cliente_facturacion dzshcf
                            ON dzsh.id = dzshcf.id_detalle_zona_servicio_horario
                        INNER JOIN public.ventas v
                            ON dzshcf.id = v.id_detalle_zona_servicio_horario_cliente_facturacion
                        INNER JOIN catalogo.zona z
                            ON zu.id_zona = z.id
                        INNER JOIN seguridad.usuarios u
                            ON zu.id_usuario = u.id
                        INNER JOIN public.cliente c
                            ON v.id_cliente = c.id
                        WHERE zu.id_zona IN (" . implode(',', $placeholders) . ")
                            AND DATE(v.fecha_emision) BETWEEN :fecha_inicio AND :fecha_fin
                            AND v.estado = true
                            AND v.id_estado_venta = 6
                            AND v.ticket_anulado = false
                            AND v.cantidad_anulada = 0
                        GROUP BY  zu.id_zona, zu.id_usuario, z.nombre, u.nombres, u.apellidos, c.nombres, c.apellidos, c.clie_docnum, spd.nombre, DATE(v.fecha_emision), zu.id_usuario, dzsh.id
                        ORDER BY DATE(v.fecha_emision), zu.id_usuario, dzsh.id";

            // Prepare the parameters
            $params = [];
            foreach ($id_zona as $index => $value) {
                $params["id_zone{$index}"] = $value;
            }

            $params['fecha_inicio'] = $fecha_inicio;
            $params['fecha_fin'] = $fecha_fin;

            // Get the Doctrine connection and execute the query
            $connection = $this->entityManager->getConnection();
            $stmt = $connection->executeQuery($query, $params);

            // Fetch the results as an associative array
            return $stmt->fetchAllAssociative();
        } catch (\Exception $e) {
            $this->logger->error('Error while fetching detailed report by client: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error while fetching detailed report by client: ' . $e->getMessage());
        }
    }


    public function getConsolidatedReportByZone(array $filtros): array
    {
        try {
            // Extract the filters
            $id_zona = $filtros['id_zone'];  // Asumiendo que esto es un array
            $fecha_inicio = $filtros['datestart'];
            $fecha_fin = $filtros['dateend'];

            // Generate dynamic placeholders for the "IN" clause
            $placeholders = [];
            foreach ($id_zona as $index => $value) {
                $placeholders[] = ":id_zone{$index}";
            }

            // Build the SQL query
            $query = "
                        SELECT zu.id_zona, 
                            z.nombre as Evento, 
                            zu.id_usuario, 
                            CONCAT(u.nombres, ' ' ,u.apellidos) as Facturador, 
                            dzsh.id, 
                            spd.nombre as nombre_servicio, 
                            SUM(v.cantidad_facturada) AS cantidad_total_facturada,
                            DATE(v.fecha_emision) AS fecha_emision,
                            spd.orden
                        FROM seguridad.zona_usuario zu
                        INNER JOIN catalogo.detalle_zona_servicio_horario dzsh
                            ON zu.id = dzsh.id_zona_usuario
                        INNER JOIN catalogo.servicios_productos_detalles spd
                            ON spd.id = dzsh.id_servicios_productos_detalles
                        INNER JOIN public.detalle_zona_servicio_horario_cliente_facturacion dzshcf
                            ON dzsh.id = dzshcf.id_detalle_zona_servicio_horario
                        INNER JOIN public.ventas v
                            ON dzshcf.id = v.id_detalle_zona_servicio_horario_cliente_facturacion
                        INNER JOIN catalogo.zona z
                            ON zu.id_zona = z.id
                        INNER JOIN seguridad.usuarios u
                            ON zu.id_usuario = u.id
                        WHERE zu.id_zona IN (" . implode(',', $placeholders) . ") 
                        AND DATE(v.fecha_emision) BETWEEN :fecha_inicio AND :fecha_fin
                        AND v.estado = true
                        AND v.id_estado_venta = 6
                        AND v.ticket_anulado = false
                        AND v.cantidad_anulada = 0
                        GROUP BY zu.id_usuario, dzsh.id, zu.id_zona, z.nombre, u.nombres, u.apellidos, spd.nombre, DATE(v.fecha_emision), spd.orden
                        ORDER BY zu.id_usuario, dzsh.id_servicios_productos_detalles, DATE(v.fecha_emision)";

            // Prepare the parameters
            $params = [];
            foreach ($id_zona as $index => $value) {
                $params["id_zone{$index}"] = $value;
            }

            $params['fecha_inicio'] = $fecha_inicio;
            $params['fecha_fin'] = $fecha_fin;

            // Get the Doctrine connection and execute the query
            $connection = $this->entityManager->getConnection();
            $stmt = $connection->executeQuery($query, $params);

            // Fetch the results as an associative array
            return $stmt->fetchAllAssociative();
        } catch (\Exception $e) {
            $this->logger->error('Error while fetching consolidated report: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error while fetching consolidated report: ' . $e->getMessage());
        }
    }
}
