<?php

namespace App\Services;

use App\Repository\Seguridad\Interface\AuthRepositoryInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

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
