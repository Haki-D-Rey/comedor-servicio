<?php

namespace App\Repository\Publico\Interface;

use App\DTO\Publico\DetalleZonaServicioHorarioClienteFacturacionDTO;

interface DetalleZonaServicioHorarioClienteFacturacionRepositoryInterface
{
    /**
     * @return DetalleZonaServicioHorarioClienteFacturacionDTO[]
     */
    public function getAllDetalleZonaServicioHorarioClienteFacturacion(): array;

    /**
     * @param int $id
     * @return DetalleZonaServicioHorarioClienteFacturacionDTO|null
     */
    public function getDetalleZonaServicioHorarioClienteFacturacionById(int $id): ?DetalleZonaServicioHorarioClienteFacturacionDTO;

    /**
     * @param DetalleZonaServicioHorarioClienteFacturacionDTO $detalleZonaServicioHorarioClienteFacturacionDTO
     * @return void
     */
    public function createDetalleZonaServicioHorarioClienteFacturacion(DetalleZonaServicioHorarioClienteFacturacionDTO $detalleZonaServicioHorarioClienteFacturacionDTO): void;

    /**
     * @param int $id
     * @param DetalleZonaServicioHorarioClienteFacturacionDTO $detalleZonaServicioHorarioClienteFacturacionDTO
     * @return void
     */
    public function updateDetalleZonaServicioHorarioClienteFacturacion(int $id, DetalleZonaServicioHorarioClienteFacturacionDTO $detalleZonaServicioHorarioClienteFacturacionDTO): void;

    /**
     * @param int $id
     * @return void
     */
    public function deleteDetalleZonaServicioHorarioClienteFacturacion(int $id): void;
}
