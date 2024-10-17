<?php

namespace App\Repository\Catalogo\Interface;

use App\DTO\SistemasDTO;

interface SistemasRepositoryInterface
{
    /**
     * @return SistemasDTO[]
     */
    public function getAllSistemas(): array;

    /**
     * @param int $id
     * @return SistemasDTO|null
     */
    public function getSistemaById(int $id): ?SistemasDTO;

    /**
     * @param SistemasDTO $sistemasDTO
     * @return bool
     */
    public function createSistema(SistemasDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @param SistemasDTO $sistemasDTO
     * @return bool
     */
    public function updateSistema(int $id, SistemasDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteSistema(int $id): void;
}
