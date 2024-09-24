<?php

namespace App\Repository;

use App\DTO\TipoServiciosDTO;

interface TipoServiciosRepositoryInterface
{
    /**
     * @return TipoServiciosDTO[]
     */
    public function getAllTipoServicios(): array;

    /**
     * @param int $id
     * @return TipoServiciosDTO|null
     */
    public function getTipoServicioById(int $id): ?TipoServiciosDTO;

    /**
     * @param TipoServiciosDTO $TipoServiciosDTO
     * @return bool
     */
    public function createTipoServicio(TipoServiciosDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @param TipoServiciosDTO $TipoServiciosDTO
     * @return bool
     */
    public function updateTipoServicio(int $id, TipoServiciosDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteTipoServicio(int $id): void;
}
