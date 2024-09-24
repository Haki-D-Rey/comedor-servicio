<?php

namespace App\Repository;

use App\DTO\ServiciosProductosDetallesDTO;

interface ServiciosProductosDetallesRepositoryInterface
{
    /**
     * @return ServiciosProductosDetallesDTO[]
     */
    public function getAllServiciosProductosDetalles(): array;

    /**
     * @param int $id
     * @return ServiciosProductosDetallesDTO|null
     */
    public function getServiciosProductosDetallesById(int $id): ?ServiciosProductosDetallesDTO;

    /**
     * @param ServiciosProductosDetallesDTO $ServiciosProductosDTO
     * @return bool
     */
    public function createServiciosProductosDetalles(ServiciosProductosDetallesDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @param ServiciosProductosDetallesDTO $ServiciosProductosDTO
     * @return bool
     */
    public function updateServiciosProductosDetalles(int $id, ServiciosProductosDetallesDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteServiciosProductosDetalles(int $id): void;
}
