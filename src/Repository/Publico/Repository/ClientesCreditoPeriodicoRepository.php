<?php

namespace App\Repository\Publico\Repository;

use App\DTO\Publico\ClientesCreditoPeriodicoDTO;
use App\Entity\Publico\ClientesCreditoPeriodico;
use App\Entity\Publico\DetalleZonaServicioHorarioClienteFacturacion;
use App\Repository\Publico\Interface\ClientesCreditoPeriodicoRepositoryInterface;
use App\Repository\GenericRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class ClientesCreditoPeriodicoRepository extends GenericRepository implements ClientesCreditoPeriodicoRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, ClientesCreditoPeriodico::class);
        $this->logger = $loggerInterface;
    }

    public function getAllClienteCreditoPeriodico(): array
    {
        try {
            $clientesCredito = $this->getAllEntities();
            return array_map(function (ClientesCreditoPeriodico $clienteCredito) {
                return new ClientesCreditoPeriodicoDTO(
                    $clienteCredito->getId(),
                    $clienteCredito->getDetalleZonaServicioHorarioClienteFacturacion()->getId(),
                    $clienteCredito->getPeriodoInicial(),
                    $clienteCredito->getPeriodoFinal(),
                    $clienteCredito->getCantidadCreditoLimite(),
                    $clienteCredito->getCantidadCreditoUsado(),
                    $clienteCredito->getCantidadCreditoDisponible(),
                    $clienteCredito->getFechaCreacion(),
                    $clienteCredito->getFechaModificacion(),
                    $clienteCredito->getEstado()
                );
            }, $clientesCredito);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener clientes con crédito periódico: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getClienteCreditoPeriodicoById(int $id): ?ClientesCreditoPeriodicoDTO
    {
        try {
            $clienteCredito = $this->getEntityById($id);
            if (!$clienteCredito) {
                return null;
            }

            return new ClientesCreditoPeriodicoDTO(
                $clienteCredito->getId(),
                $clienteCredito->getDetalleZonaServicioHorarioClienteFacturacion()->getId(),
                $clienteCredito->getPeriodoInicial(),
                $clienteCredito->getPeriodoFinal(),
                $clienteCredito->getCantidadCreditoLimite(),
                $clienteCredito->getCantidadCreditoUsado(),
                $clienteCredito->getCantidadCreditoDisponible(),
                $clienteCredito->getFechaCreacion(),
                $clienteCredito->getFechaModificacion(),
                $clienteCredito->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener cliente crédito periódico por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createClienteCreditoPeriodico(array $clienteCreditoPeriodicoDTOs): void
    {
        $batchSize = 20;
        $i = 0;
        $valuesInsert = [];
        $paramsInsert = [];
        $paramsUpdate = [];
        $ids = [];  // Aquí se almacenarán los ID de las actualizaciones

        $this->entityManager->beginTransaction();

        try {

            $updateParams = new \stdClass();
            $paramsUpdate = [];
            $ids = [];
            foreach ($clienteCreditoPeriodicoDTOs as $clienteCreditoDTO) {
                $cliente = $this->entityManager->getRepository(DetalleZonaServicioHorarioClienteFacturacion::class)
                    ->find($clienteCreditoDTO->getIdDetalleZonaServicioHorarioClienteFacturacion());

                if (!$cliente) {
                    throw new \RuntimeException("Cliente con ID {$clienteCreditoDTO->getIdDetalleZonaServicioHorarioClienteFacturacion()} no encontrado.");
                }

                // Verificar si ya existe un registro para este cliente
                $existingRecord = $this->entityManager->getRepository(ClientesCreditoPeriodico::class)
                    ->findOneBy([
                        'idDetalleZonaServicioHorarioClienteFacturacion' => $cliente
                    ]);

                // Si existe, agregar al array de actualizaciones, si no, al de inserciones
                if ($existingRecord) {
                    $result = $this->prepareUpdateParams($i, $clienteCreditoDTO, $existingRecord);
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
                    $ids[] = ":id" . $i;
                } else {
                    // Preparar los valores para la inserción
                    $valuesInsert[] = '(:id_detalle_zona_servicio_horario_cliente_facturacion' . $i . ', :periodo_inicial' . $i . ', :periodo_final' . $i . ', :cantidad_credito_limite' . $i . ', :cantidad_credito_usado' . $i . ', :cantidad_credito_disponible' . $i . ', :fecha_creacion' . $i . ', :fecha_modificacion' . $i . ', :estado' . $i . ')';
                    $paramsInsert['id_detalle_zona_servicio_horario_cliente_facturacion' . $i] = $clienteCreditoDTO->getIdDetalleZonaServicioHorarioClienteFacturacion();
                    $paramsInsert['periodo_inicial' . $i] = $clienteCreditoDTO->getPeriodoInicial()->format('Y-m-d');
                    $paramsInsert['periodo_final' . $i] = $clienteCreditoDTO->getPeriodoFinal()->format('Y-m-d');
                    $paramsInsert['cantidad_credito_limite' . $i] = $clienteCreditoDTO->getCantidadCreditoLimite();
                    $paramsInsert['cantidad_credito_usado' . $i] = $clienteCreditoDTO->getCantidadCreditoUsado();
                    $paramsInsert['cantidad_credito_disponible' . $i] = $clienteCreditoDTO->getCantidadCreditoDisponible();
                    $paramsInsert['fecha_creacion' . $i] = $clienteCreditoDTO->getFechaCreacion()->format('Y-m-d H:i:s');
                    $paramsInsert['fecha_modificacion' . $i] = $clienteCreditoDTO->getFechaModificacion() ? $clienteCreditoDTO->getFechaModificacion()->format('Y-m-d H:i:s') : null;
                    $paramsInsert['estado' . $i] = $clienteCreditoDTO->getEstado();
                }

                // Controlar el tamaño del batch para insert y update
                if (($i + 1) % $batchSize === 0) {
                    // Insertar los registros si hay datos
                    if (!empty($valuesInsert)) {
                        $this->insertClienteCreditoPeriodico($valuesInsert, $paramsInsert);
                    }

                    // Si hay registros para actualizar, realizar la actualización masiva
                    if (!empty($paramsUpdate)) {
                        $this->updateClienteCreditoPeriodicoData($paramsUpdate, $updateParams, $ids);
                    }

                    // Resetear valores y parámetros para el siguiente batch
                    $valuesInsert = [];
                    $paramsInsert = [];
                    $ids = [];
                }

                ++$i;
            }

            if (!empty($valuesInsert)) {
                $this->insertClienteCreditoPeriodico($valuesInsert, $paramsInsert);
            }

            if (!empty($paramsUpdate)) {
                $this->updateClienteCreditoPeriodicoData($paramsUpdate, $updateParams, $ids);
            }

            // Commit la transacción
            $this->entityManager->commit();
        } catch (\RuntimeException $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error al crear clientes crédito periódico: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        } catch (ORMException | DBALException $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error al crear clientes crédito periódico en la base de datos: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear clientes crédito periódico.');
        } catch (\Throwable $e) {
            $this->entityManager->rollback();
            $this->logger->error('Error inesperado al crear clientes crédito periódico: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
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

    public function insertClienteCreditoPeriodico(array $valuesInsert, array $paramsInsert): void
    {

        try {
            $sqlInsert = "INSERT INTO cliente_credito_periodico (id_detalle_zona_servicio_horario_cliente_facturacion, periodo_inicial, periodo_final, cantidad_credito_limite, cantidad_credito_usado, cantidad_credito_disponible, fecha_creacion, fecha_modificacion, estado) 
                          VALUES " . implode(', ', $valuesInsert);
            $this->entityManager->getConnection()->executeStatement($sqlInsert, $paramsInsert);
        } catch (\Exception $e) {
            $this->logger->error('Error al insertar clientes crédito periódico: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al insertar clientes crédito periódico: ' . $e->getMessage());
        }
    }

    public function updateClienteCreditoPeriodico(int $id, ClientesCreditoPeriodicoDTO $clienteCreditoPeriodicoDTO): void
    {
        $batchSize = 20;
        $i = 0;
        $paramsUpdate = [];
        $updateParams = new \stdClass();
        $ids = [];

        $this->entityManager->beginTransaction();

        try {
            foreach ($clienteCreditoPeriodicoDTO as $clienteCreditoDTO) {
                $cliente = $this->entityManager->getRepository(DetalleZonaServicioHorarioClienteFacturacion::class)
                    ->find($clienteCreditoDTO->getIdDetalleZonaServicioHorarioClienteFacturacion());

                if (!$cliente) {
                    throw new \RuntimeException("Cliente con ID {$clienteCreditoDTO->getIdDetalleZonaServicioHorarioClienteFacturacion()} no encontrado.");
                }

                // Verificar si ya existe un registro para este cliente
                $existingRecord = $this->entityManager->getRepository(ClientesCreditoPeriodico::class)
                    ->findOneBy([
                        'idDetalleZonaServicioHorarioClienteFacturacion' => $cliente
                    ]);

                // Si existe un registro, procedemos a actualizarlo
                if ($existingRecord) {
                    $result = $this->prepareUpdateParams($i, $clienteCreditoDTO, $existingRecord);
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
                    throw new \RuntimeException("No se encuentra un registro de crédito periódico para el cliente con ID {$clienteCreditoDTO->getIdDetalleZonaServicioHorarioClienteFacturacion()}.");
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

    public function deleteClienteCreditoPeriodico(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0); // Marcar como eliminado
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar cliente crédito periódico: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
