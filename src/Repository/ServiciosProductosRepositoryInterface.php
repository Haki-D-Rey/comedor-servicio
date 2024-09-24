<?php

namespace App\Repository;

use App\DTO\ServiciosProductosDTO;

interface ServiciosProductosRepositoryInterface
{
    /**
     * @return ServiciosProductosDTO[]
     */
    public function getAllServiciosProductos(): array;

    /**
     * @param int $id
     * @return ServiciosProductosDTO|null
     */
    public function getServiciosProductoById(int $id): ?ServiciosProductosDTO;

    /**
     * @param ServiciosProductosDTO $ServiciosProductosDTO
     * @return bool
     */
    public function createServiciosProducto(ServiciosProductosDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @param ServiciosProductosDTO $ServiciosProductosDTO
     * @return bool
     */
    public function updateServiciosProducto(int $id, ServiciosProductosDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteServiciosProducto(int $id): void;
}
