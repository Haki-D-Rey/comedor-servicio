<?php

namespace App\Repository\Catalogo\Interface;

use App\DTO\Catalogo\TipoUsuarioDTO;
use App\Entity\Seguridad\TipoUsuario;

interface TipoUsuariosRepositoryInterface
{
    /**
     * @return TipoUsuario[]
     */
    public function getAllTipoUsuarios(): array;

    /**
     * @param int $id
     * @return TipoUsuarioDTO|null
     */
    public function getTipoUsuarioById(int $id): ?TipoUsuarioDTO;

    /**
     * @param TipoUsuarioDTO $TipoUsuarioDTO
     * @return bool
     */
    public function createTipoUsuario(TipoUsuarioDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @param TipoUsuarioDTO $TipoUsuarioDTO
     * @return bool
     */
    public function updateTipoUsuario(int $id, TipoUsuarioDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteTipoUsuario(int $id): void;
}
