<?php

namespace App\Repository\Catalogo\Interface;

use App\DTO\Catalogo\DepartamentosDTO;

interface DepartamentosRepositoryInterface
{
    /**
     * @return DepartamentosDTO[]
     */
    public function getAllDepartamentos(): array;

    /**
     * @param int $id
     * @return DepartamentosDTO|null
     */
    public function getDepartamentoById(int $id): ?DepartamentosDTO;

    /**
     * @param DepartamentosDTO $DepartamentosDTO
     * @return bool
     */
    public function createDepartamento(DepartamentosDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @param DepartamentosDTO $DepartamentosDTO
     * @return bool
     */
    public function updateDepartamento(int $id, DepartamentosDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteDepartamento(int $id): void;
}
