<?php

namespace App\Repository\Seguridad\Interface;

use App\DTO\UsuarioDTO;
use App\Entity\Usuario;

interface UsuarioRepositoryInterface
{
    /**
     * @return UsuarioDTO[]
     */
    public function getAllUsuarios(): array;

    /**
     * @param int $id
     * @return UsuarioDTO|null
     */
    public function getUsuarioById(int $id): ?UsuarioDTO;

    /**
     * @param UsuarioDTO $usuarioDTO
     * @return bool
     */
    public function createUser(UsuarioDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @param UsuarioDTO $usuarioDTO
     * @return bool
     */
    public function updateUser(int $id, UsuarioDTO $usuarioDTO): void;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): void;

     /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword(string $password, string $hash): bool;

     /**
     * @param int $id
     * @return Usuario
     */
    public function findWithTipoUsuarioPermisos(int $usuarioId);
}
