<?php

namespace App\Repository\Catalogo\Interface;

use App\DTO\Catalogo\IdentificacionFacturacionDTO;

interface IdentificacionFacturacionRepositoryInterface
{
    /**
     * @return IdentificacionFacturacionDTO[]
     */
    public function getAllIdentificacionFacturacion(): array;

    /**
     * @param int $id
     * @return IdentificacionFacturacionDTO|null
     */
    public function getIdentificacionFacturacionById(int $id): ?IdentificacionFacturacionDTO;

    /**
     * @param IdentificacionFacturacionDTO $IdentificacionFacturacionDTO
     * @return bool
     */
    public function createIdentificacionFacturacion(IdentificacionFacturacionDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @param IdentificacionFacturacionDTO $IdentificacionFacturacionDTO
     * @return bool
     */
    public function updateIdentificacionFacturacion(int $id, IdentificacionFacturacionDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteIdentificacionFacturacion(int $id): void;
}
