<?php

namespace App\Repository\Catalogo\Interface;

use App\DTO\Catalogo\CargosDTO;

interface CargosRepositoryInterface
{
    /**
     * @return CargosDTO[]
     */
    public function getAllCargos(): array;

    /**
     * @param int $id
     * @return CargosDTO|null
     */
    public function getCargoById(int $id): ?CargosDTO;

    /**
     * @param CargosDTO $CargosDTO
     * @return bool
     */
    public function createCargo(CargosDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @param CargosDTO $CargosDTO
     * @return bool
     */
    public function updateCargo(int $id, CargosDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteCargo(int $id): void;
}
