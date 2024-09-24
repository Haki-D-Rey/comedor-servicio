<?php

namespace App\Services;

use App\Repository\AuthRepositoryInterface;

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

}