<?php

namespace App\Repository\Seguridad\Repository;

use App\DTO\UsuarioDTO;
use App\Entity\Usuario;
use App\Repository\GenericRepository;
use App\Repository\Seguridad\Interface\UsuarioRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Log\LoggerInterface as LogLoggerInterface;

class UsuarioRepository extends GenericRepository implements UsuarioRepositoryInterface
{
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface)
    {
        parent::__construct($entityManager, Usuario::class);
        $this->logger = $loggerInterface;
    }

    public function getAllUsuarios(): array
    {
        try {
            $usuarios = $this->getAllEntities();
            return array_map(function (Usuario $usuario) {
                return new UsuarioDTO(
                    $usuario->getId(),
                    $usuario->getNombreUsuario(),
                    $usuario->getContrasenia(),
                    $usuario->getNombres(),
                    $usuario->getApellidos(),
                    $usuario->getCorreo(),
                    $usuario->getFechaCreacion(),
                    $usuario->getFechaModificacion(),
                    $usuario->getIsAdmin(),
                    $usuario->getEstado(),
                    $usuario->getIdTipoUsuario()
                );
            }, $usuarios);
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuarios: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function getUsuarioById(int $id): ?UsuarioDTO
    {
        try {
            $usuario = $this->getEntityById($id);
            if (!$usuario) {
                return null;
            }

            return new UsuarioDTO(
                $usuario->getId(),
                $usuario->getNombreUsuario(),
                $usuario->getContrasenia(),
                $usuario->getNombres(),
                $usuario->getApellidos(),
                $usuario->getCorreo(),
                $usuario->getFecha_creacion(),
                $usuario->getFecha_modificacion(),
                $usuario->getIsAdmin(),
                $usuario->getEstado(),
                $usuario->getIdTipoUsuario()
            );
        } catch (\Exception $e) {
            $this->logger->error('Error al obtener usuario por ID: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function createUser(UsuarioDTO $usuarioDTO): void
    {
        try {

            $existingUsuarioByNombre = $this->entityManager->getRepository(Usuario::class)
                ->findOneBy(['nombreUsuario' => $usuarioDTO->getNombreUsuario()]);

            if ($existingUsuarioByNombre) {
                throw new \RuntimeException('El nombre de usuario ya está en uso.');
            }


            $existingUsuarioByCorreo = $this->entityManager->getRepository(Usuario::class)
                ->findOneBy(['correo' => $usuarioDTO->getCorreo()]);

            if ($existingUsuarioByCorreo) {
                throw new \RuntimeException('El correo ya está en uso.');
            }

            $usuario = new Usuario();
            $usuario->setNombreUsuario($usuarioDTO->getNombreUsuario());
            $usuario->setContrasenia($this->hashPassword($usuarioDTO->getContrasenia()));
            $usuario->setNombres($usuarioDTO->getNombres());
            $usuario->setApellidos($usuarioDTO->getApellidos());
            $usuario->setCorreo($usuarioDTO->getCorreo());
            $usuario->setFechaCreacion($usuarioDTO->getFecha_creacion());
            $usuario->setFechaModificacion($usuarioDTO->getFecha_modificacion());
            $usuario->setIsAdmin($usuarioDTO->getIsAdmin());
            $usuario->setEstado($usuarioDTO->getEstado());
            $usuario->setIdTipoUsuario($usuarioDTO->getIdTipoUsuario());

            $this->entityManager->persist($usuario);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException('Error al crear usuario.');
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado al crear usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function updateUser(int $id, UsuarioDTO $usuarioDTO): void
    {
        try {
            $usuario = $this->getEntityById($id);
            if (!$usuario) {
                throw new \RuntimeException('Usuario no encontrado.');
            }

            $existingUsuarioByNombre = $this->entityManager->getRepository(Usuario::class)
                ->findOneBy(['nombreUsuario' => $usuarioDTO->getNombreUsuario()]);

            if ($existingUsuarioByNombre && $usuario->getNombreusuario() != $existingUsuarioByNombre->getNombreusuario()) {
                throw new \RuntimeException('El nombre de usuario ya está en uso.');
            }

            $existingUsuarioByCorreo = $this->entityManager->getRepository(Usuario::class)
                ->findOneBy(['correo' => $usuarioDTO->getCorreo()]);

            if ($existingUsuarioByCorreo && $usuario->getCorreo() != $existingUsuarioByCorreo->getCorreo()) {
                throw new \RuntimeException('El correo ya está en uso.');
            }

            $excludePropertyName = ['fecha_creacion'];
            $this->updateEntityFromDTO($usuario, $usuarioDTO, $excludePropertyName);
            $this->entityManager->flush();
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al actualizar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function deleteUser(int $id): void
    {
        try {
            $this-> markAsDeleted($id, 0);  
        } catch (ORMException | DBALException $e) {
            $this->logger->error('Error al eliminar usuario: ' . $e->getMessage(), ['exception' => $e]);
            throw new \RuntimeException($e->getMessage());
        }
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function findWithTipoUsuarioPermisos($usuarioId)
    {
        $response = $this->createQueryBuilder('u')
            ->innerJoin('u.tipoUsuarioPermisos', 'tup')
            ->innerJoin('tup.tipoUsuario', 'tu')
            ->innerJoin('tup.permiso', 'p')
            ->where('u.id = :id')
            ->setParameter('id', $usuarioId)
            ->getQuery()
            ->getResult();

            return $response;
    }
}
