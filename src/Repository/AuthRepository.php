<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\ORM\EntityManagerInterface;
use Firebase\JWT\JWT;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface as LogLoggerInterface;
use RuntimeException;

class AuthRepository implements AuthRepositoryInterface
{
    private $logger;
    private $entityManager;
    private $container;

    public function __construct(EntityManagerInterface $entityManager, LogLoggerInterface $loggerInterface, ContainerInterface $container)
    {
        $this->logger = $loggerInterface;
        $this->entityManager = $entityManager;
        $this->container = $container;
    }

    public function login(array $credenciales): array
    {
        try {
            $username = $credenciales['username'];
            $password = $credenciales['password'];

            $user = $this->entityManager->getRepository(Usuario::class)
                ->findOneBy(['nombreUsuario' => $username]);

            if (!$user) {
                throw new RuntimeException('El nombre de usuario es incorrecto.');
            }

            if (!$this->verifyPassword($password, $user->getContrasenia())) {
                throw new RuntimeException('La contraseña es inválida.');
            }

            // Obtener la clave secreta desde el contenedor de configuraciones
            $key = $this->container->get('settings')['jwt']['secret'];
            $expirationTime = time() + $this->container->get('settings')['jwt']['expiration'];

            $payload = [
                'iss' => 'your-domain.com',
                'aud' => 'your-domain.com',
                'iat' => time(),
                'nbf' => time(),
                'exp' => $expirationTime,
                'sub' => $user->getId(),
            ];

            // Generar el token JWT
            try {
                $token = JWT::encode($payload, $key, 'HS256');
            } catch (\Exception $jwtException) {
                throw new RuntimeException('Error al generar el token JWT: ' . $jwtException->getMessage());
            }

            // Retornar la estructura requerida
            return [
                'usuario' => $user->getNombreUsuario(),
                'token' => $token,
                'expiracion' => $expirationTime
            ];
        } catch (RuntimeException $e) {
            $this->logger->error('Error en el proceso de login: ' . $e->getMessage(), ['exception' => $e]);
            throw new RuntimeException($e->getMessage());
        } catch (\Throwable $e) {
            $this->logger->error('Error inesperado en el proceso de login: ' . $e->getMessage(), ['exception' => $e]);
            throw new RuntimeException('Ocurrió un error inesperado.');
        }
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
