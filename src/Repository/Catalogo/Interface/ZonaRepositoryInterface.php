<?php

namespace App\Repository\Catalogo\Interface;

use App\DTO\ZonaDTO;

interface ZonaRepositoryInterface
{
    /**
     * @return ZonaDTO[]
     */
    public function getAllZona(): array;

    /**
     * @param int $id
     * @return ZonaDTO|null
     */
    public function getZonaById(int $id): ?ZonaDTO;

    /**
     * @param ZonaDTO $ZonaDTO
     * @return bool
     */
    public function createZona(ZonaDTO $zonaDTO): void;

    /**
     * @param int $id
     * @param ZonaDTO $ZonaDTO
     * @return bool
     */
    public function updateZona(int $id, ZonaDTO $zonaDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteZona(int $id): void;
}
