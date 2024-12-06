<?php

namespace App\Repository\Seguridad\Interface;

use App\DTO\ZonaUsuarioDTO;

interface ZonaUsuarioRepositoryInterface
{
    /**
     * @return ZonaUsuarioDTO[]
     */
    public function getAllZonaUsuarios(): array;

    /**
     * @param int $id
     * @return array|null
     */
    public function getRelationalZonaUsuarioById(int $id): array;

    /**
     * @param object $filtroCode
     * @return array|null
     */
    public function getStadisticZone(object $filtro): array;

    /**
     * @param int $id
     * @return ZonaUsuarioDTO|null
     */
    public function getZonaUsuarioById(int $id): ?ZonaUsuarioDTO;

    /**
     * @param array $zonaUsuarioDTO
     * @return bool
     */
    public function createZonaUsuario(array $zonaUsuarioDTO): void;

    /**
     * @param int $id
     * @param ZonaUsuarioDTO $zonaUsuarioDTO
     * @return bool
     */
    public function updateZonaUsuario(int $id, ZonaUsuarioDTO $zonaUsuarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteZonaUsuario(int $id): void;
}
