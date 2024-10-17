<?php

namespace App\Repository\Seguridad\Interface;

interface AuthRepositoryInterface
{
    /**
     * Método para manejar el inicio de sesión de un usuario.
     *
     * @param array $credenciales Las credenciales de inicio de sesión (username y password).
     * @return array Retorna un arreglo con la información del usuario autenticado que incluye:
     * - 'nombreUsuario' => string
     * - 'token' => string
     * - 'expiracion' => int (timestamp de expiración del token)
     *
     * @throws \RuntimeException Si el nombre de usuario o la contraseña son incorrectos.
     */
    public function login(array $credenciales): array;

    /**
     * Método para verificar si una contraseña es válida comparándola con el hash almacenado.
     *
     * @param string $password La contraseña proporcionada.
     * @param string $hash El hash de la contraseña almacenada en la base de datos.
     * @return bool Retorna true si la contraseña es válida, de lo contrario false.
     */
    public function verifyPassword(string $password, string $hash): bool;
}
