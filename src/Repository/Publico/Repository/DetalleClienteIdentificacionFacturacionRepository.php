<?php

namespace App\Repository\Publico\Repository;

use App\DTO\Publico\DetalleClienteIdentificacionFacturacionDTO;
use App\Entity\ListaCatalogo;
use App\Entity\ListaCatalogoDetalle;
use App\Entity\Publico\Clientes;
use App\Entity\Publico\DetalleClienteIdentificacionFacturacion;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use App\Repository\GenericRepository;
use App\Repository\Publico\Interface\DetalleClienteIdentificacionFacturacionRepositoryInterface;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class DetalleClienteIdentificacionFacturacionRepository extends GenericRepository implements DetalleClienteIdentificacionFacturacionRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, DetalleClienteIdentificacionFacturacion::class);
        $this->logger = $loggerInterface;
    }

    public function getAllDetalleClienteIdentificacionFacturacion(): array
    {
        try {
            $detalleClienteIdentificacionFacturacion = $this->getAllEntities();
            return array_map(function (DetalleClienteIdentificacionFacturacion $detalle) {
                return new DetalleClienteIdentificacionFacturacionDTO(
                    $detalle->getId(),
                    $detalle->getCliente()->getId(),
                    $detalle->getIdentificacionFacturacion()->getId(),
                    $detalle->getJsonIdentificacion(),
                    $detalle->getFechaCreacion(),
                    $detalle->getFechaModificacion(),
                    $detalle->getEstado()
                );
            }, $detalleClienteIdentificacionFacturacion);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener los detalles de Cliente Identificación Facturación: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getDetalleClienteIdentificacionFacturacionById(int $id): ?DetalleClienteIdentificacionFacturacionDTO
    {
        try {
            $detalle = $this->getEntityById($id);
            if (!$detalle) {
                return null;
            }

            return new DetalleClienteIdentificacionFacturacionDTO(
                $detalle->getId(),
                $detalle->getCliente()->getId(),
                $detalle->getIdentificacionFacturacion()->getId(),
                $detalle->getJsonIdentificacion(),
                $detalle->getFechaCreacion(),
                $detalle->getFechaModificacion(),
                $detalle->getEstado()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener el detalle de Cliente Identificación Facturación por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createDetalleClienteIdentificacionFacturacion(DetalleClienteIdentificacionFacturacionDTO $detalleClienteIdentificacionFacturacionDTO): void
    {
        try {
            $detalle = new DetalleClienteIdentificacionFacturacion();

            $listaCatalogoRepository = $this->entityManager->getRepository(ListaCatalogo::class);
            $listaCatalogo = $listaCatalogoRepository->findBy([
                'codigo_interno' => ['LCU-003']
            ]);

            if (count($listaCatalogo) < 1) {
                throw new \Exception('No se encontraron los catálogos necesarios.');
            }

            $idIdentificacionFacturacionCatalogo = $listaCatalogo[0]->getId();
            $identificacionFacturacion = $this->findMatchingDetalle($idIdentificacionFacturacionCatalogo, $detalleClienteIdentificacionFacturacionDTO->getIdIdentificacionFacturacion(), ListaCatalogoDetalle::class);

            if (!$identificacionFacturacion) {
                throw new \Exception('Departamento o Cargo no encontrado.');
            }

            $cliente = $this->entityManager->getRepository(Clientes::class);
            $clienteResult = $cliente->find($detalleClienteIdentificacionFacturacionDTO->getIdCliente());

            $detalle->setCliente($clienteResult);
            $detalle->setIdentificacionFacturacion($identificacionFacturacion);
            $detalle->setJsonIdentificacion($detalleClienteIdentificacionFacturacionDTO->getJsonIdentificacion());
            $detalle->setEstado($detalleClienteIdentificacionFacturacionDTO->getEstado());
            $detalle->setFechaCreacion($detalleClienteIdentificacionFacturacionDTO->getFechaCreacion());
            // Persistir el detalle
            $this->entityManager->persist($detalle);
            $this->entityManager->flush();

        } catch (\InvalidArgumentException $e) {
            $this->logger->error('Error en los datos proporcionados: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error en los datos proporcionados.');
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear el detalle de Cliente Identificación Facturación: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear el registro.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear el detalle de Cliente Identificación Facturación: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateDetalleClienteIdentificacionFacturacion(int $id, DetalleClienteIdentificacionFacturacionDTO $detalleClienteIdentificacionFacturacionDTO): void
    {
        try {
            $detalle = $this->getEntityById($id);
            if (!$detalle) {
                throw new \RuntimeException('El detalle de Cliente Identificación Facturación no encontrado.');
            }

            // Actualizar propiedades
            $excludeProperties = ['FechaCreacion'];
            $this->updateEntityFromDTO($detalle, $detalleClienteIdentificacionFacturacionDTO, $excludeProperties);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar el detalle de Cliente Identificación Facturación: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteDetalleClienteIdentificacionFacturacion(int $id): void
    {
        try {
            $this->markAsDeleted($id, 0);
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar el detalle de Cliente Identificación Facturación: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }
}
