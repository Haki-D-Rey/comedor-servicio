<?php

namespace App\Services\Seguridad;

use App\Entity\Usuario;

class AuthorizationService
{
    public function __construct() {}
    public function canAccess(Usuario $usuario, array $tipoUsuarioPermisos, string $accion): bool
    {
        if (empty($tipoUsuarioPermisos)) {
            return false;
        }

        foreach ($tipoUsuarioPermisos as $tipoUsuarioPermiso) {
            $permiso = $tipoUsuarioPermiso->getPermiso();
            if ($permiso && ($permiso->getAccion() === $accion || $permiso->getAccion() === "project_all")) {
                return true;
            }
        }

        return false;
    }
}
