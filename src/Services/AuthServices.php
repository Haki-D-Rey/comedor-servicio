<?php

namespace App\Services;

use App\Repository\Seguridad\Interface\AuthRepositoryInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Slim\Psr7\Request;

class AuthServices
{
    private $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }


    public function postlogin(array $credenciales): array
    {
        try {
            return $this->authRepository->login($credenciales);
        } catch (\Exception $e) {
            throw new \RuntimeException('Error al Inciar Sesion: ' . $e->getMessage());
        }
    }

    public function postLogout(Request $request): bool 
    {

        session_start();
        $token = $_SESSION['jwt_token'];

        // Obtener el token de las cookies o del header Authorization
        if (!$token) {
            $cookiesHeader = $request->getHeader('cookie')[0] ?? null;

            if ($cookiesHeader) {
                $cookies = explode(';', $cookiesHeader);
                foreach ($cookies as $cookie) {
                    $cookie = trim($cookie);
                    if (strpos($cookie, 'jwt_token=') === 0) {
                        $token = str_replace('jwt_token=', '', $cookie);
                        break;
                    }
                }
            }

            if (!$token) {
                $authHeader = $request->getHeader('authorization')[0] ?? null;
                if ($authHeader) {
                    $token = str_replace('Bearer ', '', $authHeader);
                }
            }
        }
        session_destroy();
        return true;
    }

    public function verifyToken($data, $container): array
    {
        $decoded = JWT::decode($data['token'], new Key($container->get('settings')['jwt']['secret'], 'HS256'));
        $expirationTime = $this->getTimeRemaining($decoded->exp);

        return [
            'valido' => true,
            'token' => $data['token'],
            'tiempoExpiracion' => $expirationTime,
            'user' => $decoded
        ];
    }

    public function getTimeRemaining($expirationTime)
    {
        try {
            date_default_timezone_set('America/Guatemala');
            $currentTime = time();

            if ($currentTime >= $expirationTime) {
                return [
                    'horas' => 0,
                    'minutos' => 0,
                    'segundos' => 0
                ];
            }

            $timeRemaining = $expirationTime - $currentTime;
            $hours = floor($timeRemaining / 3600);
            $minutes = floor(($timeRemaining % 3600) / 60);
            $seconds = $timeRemaining % 60;

            return [
                'horas' => $hours,
                'minutos' => $minutes,
                'segundos' => $seconds,
                'time' => $currentTime
            ];
        } catch (\Exception $e) {
            return [
                'horas' => 0,
                'minutos' => 0,
                'segundos' => 0,
                'time' => 0
            ];
        }
    }
}
