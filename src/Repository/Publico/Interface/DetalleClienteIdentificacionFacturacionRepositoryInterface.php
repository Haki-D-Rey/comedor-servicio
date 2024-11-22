<?php

namespace App\Repository\Publico\Interface;

use App\DTO\Publico\DetalleClienteIdentificacionFacturacionDTO;

interface DetalleClienteIdentificacionFacturacionRepositoryInterface
{
    /**
     * @return DetalleClienteIdentificacionFacturacionDTO[]
     */
    public function getAllDetalleClienteIdentificacionFacturacion(): array;

    /**
     * @param int $id
     * @return DetalleClienteIdentificacionFacturacionDTO|null
     */
    public function getDetalleClienteIdentificacionFacturacionById(int $id): ?DetalleClienteIdentificacionFacturacionDTO;

    /**
     * @param DetalleClienteIdentificacionFacturacionDTO $detalleClienteIdentificacionFacturacionDTO
     * @return void
     */
    public function createDetalleClienteIdentificacionFacturacion(DetalleClienteIdentificacionFacturacionDTO $detalleClienteIdentificacionFacturacionDTO): void;

    /**
     * @param int $id
     * @param DetalleClienteIdentificacionFacturacionDTO $detalleClienteIdentificacionFacturacionDTO
     * @return void
     */
    public function updateDetalleClienteIdentificacionFacturacion(int $id, DetalleClienteIdentificacionFacturacionDTO $detalleClienteIdentificacionFacturacionDTO): void;

    /**
     * @param int $id
     * @return void
     */
    public function deleteDetalleClienteIdentificacionFacturacion(int $id): void;
}
