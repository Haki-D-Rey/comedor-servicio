<?php

namespace App\Services\Seguridad;

use App\Entity\Seguridad\TipoUsuarioPermisos;
use App\Entity\Usuario;
use App\Repository\Seguridad\Repository\UsuarioRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\Proxy as PersistenceProxy;

class AuthorizationService
{
    private EntityManagerInterface $entityManager;
    private UsuarioRepository $usuarioRepository;
    private TipoUsuarioPermisos $tipoUsuarioPermisos;

    public function __construct(EntityManagerInterface $entityManager, UsuarioRepository $usuarioRepository, TipoUsuarioPermisos $tipoUsuarioPermisos)
    {
        $this->entityManager = $entityManager;
        $this->usuarioRepository = $usuarioRepository;
        $this->tipoUsuarioPermisos = $tipoUsuarioPermisos;
    }


    public function canAccess(Usuario $usuario, string $accion): bool
    {
        $tipoUsuarioPermisos = $this->entityManager->getRepository(TipoUsuarioPermisos::class)
            ->findBy(['usuario' => $usuario->getId()]);

        if (empty($tipoUsuarioPermisos)) {
            return false;
        }
        
        foreach ($tipoUsuarioPermisos as $tipoUsuarioPermiso) {
            $permiso = $tipoUsuarioPermiso->getPermiso();

            if ($permiso instanceof PersistenceProxy) {
                $this->entityManager->initializeObject($permiso);
            }

            if ($permiso && ($permiso->getAccion() === $accion || $permiso->getAccion() === "project_all")) {
                return true;
            }
        }

        return false;
    }
}
