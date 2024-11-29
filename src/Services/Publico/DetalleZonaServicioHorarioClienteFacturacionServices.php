<?php

namespace App\Services\Publico;

use App\DTO\Publico\DetalleZonaServicioHorarioClienteFacturacionDTO;
use App\Repository\Publico\Interface\DetalleZonaServicioHorarioClienteFacturacionRepositoryInterface;

class DetalleZonaServicioHorarioClienteFacturacionServices
{
    private $detalleZonaServicioHorarioClienteFacturacionRepository;

    public function __construct(DetalleZonaServicioHorarioClienteFacturacionRepositoryInterface $detalleZonaServicioHorarioClienteFacturacionRepository)
    {
        $this->detalleZonaServicioHorarioClienteFacturacionRepository = $detalleZonaServicioHorarioClienteFacturacionRepository;
    }

    /**
     * Obtener todos los detalles de zona servicio horario cliente facturación.
     *
     * @return DetalleZonaServicioHorarioClienteFacturacionDTO[]
     */
    public function getAllDetalleZonaServicioHorarioClienteFacturacion(): array
    {
        try {
            return $this->detalleZonaServicioHorarioClienteFacturacionRepository->getAllDetalleZonaServicioHorarioClienteFacturacion();
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener los detalles de zona servicio horario cliente facturación: ' . $e->getMessage());
        }
    }

    /**
     * Obtener un detalle de zona servicio horario cliente facturación por ID.
     *
     * @param int $id
     * @return DetalleZonaServicioHorarioClienteFacturacionDTO|null
     */
    public function getDetalleZonaServicioHorarioClienteFacturacionById(int $id): ?DetalleZonaServicioHorarioClienteFacturacionDTO
    {
        try {
            return $this->detalleZonaServicioHorarioClienteFacturacionRepository->getDetalleZonaServicioHorarioClienteFacturacionById($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al obtener el detalle de zona servicio horario cliente facturación: ' . $e->getMessage());
        }
    }

    /**
     * Crear un nuevo detalle de zona servicio horario cliente facturación.
     *
     * @param DetalleZonaServicioHorarioClienteFacturacionDTO $detalleDTO
     */
    public function createDetalleZonaServicioHorarioClienteFacturacion(DetalleZonaServicioHorarioClienteFacturacionDTO $detalleDTO): void
    {
        try {
            $this->detalleZonaServicioHorarioClienteFacturacionRepository->createDetalleZonaServicioHorarioClienteFacturacion($detalleDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al crear el detalle de zona servicio horario cliente facturación: ' . $e->getMessage());
        }
    }

    /**
     * Actualizar un detalle de zona servicio horario cliente facturación.
     *
     * @param int $id
     * @param DetalleZonaServicioHorarioClienteFacturacionDTO $detalleDTO
     */
    public function updateDetalleZonaServicioHorarioClienteFacturacion(int $id, DetalleZonaServicioHorarioClienteFacturacionDTO $detalleDTO): void
    {
        try {
            $this->detalleZonaServicioHorarioClienteFacturacionRepository->updateDetalleZonaServicioHorarioClienteFacturacion($id, $detalleDTO);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al actualizar el detalle de zona servicio horario cliente facturación: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar un detalle de zona servicio horario cliente facturación.
     *
     * @param int $id
     */
    public function deleteDetalleZonaServicioHorarioClienteFacturacion(int $id): void
    {
        try {
            $this->detalleZonaServicioHorarioClienteFacturacionRepository->deleteDetalleZonaServicioHorarioClienteFacturacion($id);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al eliminar el detalle de zona servicio horario cliente facturación: ' . $e->getMessage());
        }
    }
}
