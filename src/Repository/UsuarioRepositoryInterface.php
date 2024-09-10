<?php

namespace App\Repository;

use App\DTO\UsuarioDTO;

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
    public function createUser(UsuarioDTO $usuarioDTO): bool;

    /**
     * @param int $id
     * @param UsuarioDTO $usuarioDTO
     * @return bool
     */
    public function updateUser(int $id, UsuarioDTO $usuarioDTO): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool;

     /**
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public function verifyPassword(string $password, string $hash): bool;
}
