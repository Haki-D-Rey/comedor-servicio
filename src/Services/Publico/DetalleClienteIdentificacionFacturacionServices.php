<?php

namespace App\Services\Publico;

use App\DTO\Publico\DetalleClienteIdentificacionFacturacionDTO;
use App\Repository\Publico\Interface\DetalleClienteIdentificacionFacturacionRepositoryInterface;

class DetalleClienteIdentificacionFacturacionServices
{
    private $detalleClienteIdentificacionFacturacionRepository;

    public function __construct(DetalleClienteIdentificacionFacturacionRepositoryInterface $detalleClienteIdentificacionFacturacionRepository)
    {
        $this->detalleClienteIdentificacionFacturacionRepository = $detalleClienteIdentificacionFacturacionRepository;
    }

    /**
     * Obtener todos los detalles de cliente identificación facturación.
     *
     * @return DetalleClienteIdentificacionFacturacionDTO[]
     */
    public function getAllDetalleClienteIdentificacionFacturacion(): array
    {
        try {
            return $this->detalleClienteIdentificacionFacturacionRepository->getAllDetalleClienteIdentificacionFacturacion();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener los detalles de cliente identificación facturación: ' . $e->getMessage());
        }
    }

    /**
     * Obtener un detalle de cliente identificación facturación por ID.
     *
     * @param int $id
     * @return DetalleClienteIdentificacionFacturacionDTO|null
     */
    public function getDetalleClienteIdentificacionFacturacionById(int $id): ?DetalleClienteIdentificacionFacturacionDTO
    {
        try {
            return $this->detalleClienteIdentificacionFacturacionRepository->getDetalleClienteIdentificacionFacturacionById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el detalle de cliente identificación facturación: ' . $e->getMessage());
        }
    }

    /**
     * Crear un nuevo detalle de cliente identificación facturación.
     *
     * @param DetalleClienteIdentificacionFacturacionDTO $detalleDTO
     * @return DetalleClienteIdentificacionFacturacionDTO
     */
    public function createDetalleClienteIdentificacionFacturacion(DetalleClienteIdentificacionFacturacionDTO $detalleDTO): void
    {
        try {
            $this->detalleClienteIdentificacionFacturacionRepository->createDetalleClienteIdentificacionFacturacion($detalleDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el detalle de cliente identificación facturación: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar un detalle de cliente identificación facturación.
     *
     * @param int $id
     * @param DetalleClienteIdentificacionFacturacionDTO $detalleDTO
     */
    public function updateDetalleClienteIdentificacionFacturacion(int $id, DetalleClienteIdentificacionFacturacionDTO $detalleDTO): void
    {
        try {
            $this->detalleClienteIdentificacionFacturacionRepository->updateDetalleClienteIdentificacionFacturacion($id, $detalleDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el detalle de cliente identificación facturación: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un detalle de cliente identificación facturación.
     *
     * @param int $id
     */
    public function deleteDetalleClienteIdentificacionFacturacion(int $id): void
    {
        try {
            $this->detalleClienteIdentificacionFacturacionRepository->deleteDetalleClienteIdentificacionFacturacion($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el detalle de cliente identificación facturación: ' . $e->getMessage());
        }
    }
}
