<?php

namespace App\DTO;

class UsuarioDTO
{
    private $id;
    private $nombreUsuario;
    private $contrasenia;
    private $nombres;
    private $apellidos;
    private $correo;
    private $fechaCreacion;
    private $fechaModificacion;
    private $estado;

    public function __construct(
        int $id,
        string $nombreUsuario,
        string $contrasenia,
        string $nombres,
        string $apellidos,
        string $correo,
        \DateTime $fechaCreacion,
        ?\DateTime $fechaModificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->nombreUsuario = $nombreUsuario;
        $this->contrasenia = $contrasenia;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->correo = $correo;
        $this->fechaCreacion = $fechaCreacion;
        $this->fechaModificacion = $fechaModificacion;
        $this->estado = $estado;
    }

    // Getters

    public function getId(): int
    {
        return $this->id;
    }

    public function getNombreUsuario(): string
    {
        return $this->nombreUsuario;
    }

    public function getContrasenia(): string
    {
        return $this->contrasenia;
    }

    public function getNombres(): string
    {
        return $this->nombres;
    }

    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fechaCreacion;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fechaModificacion;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }
}
