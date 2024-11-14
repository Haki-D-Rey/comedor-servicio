<?php

namespace App\DTO;

use JsonSerializable;

class UsuarioDTO implements JsonSerializable
{
    private $id;
    private $nombreUsuario;
    private $contrasenia;
    private $nombres;
    private $apellidos;
    private $correo;
    private $fecha_creacion;
    private $fecha_modificacion;
    private $isadmin;
    private $estado;
    private $idTipoUsuario;

    public function __construct(
        ?int $id,
        string $nombreUsuario,
        string $contrasenia,
        string $nombres,
        string $apellidos,
        string $correo,
        \DateTime $fecha_creacion,
        ?\DateTime $fecha_modificacion,
        bool $isadmin,
        bool $estado,
        int $idTipoUsuario
    ) {
        $this->id = $id;
        $this->nombreUsuario = $nombreUsuario;
        $this->contrasenia = $contrasenia;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->correo = $correo;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_modificacion = $fecha_modificacion;
        $this->isadmin = $isadmin;
        $this->estado = $estado;
        $this->idTipoUsuario = $idTipoUsuario;
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

    public function getFecha_creacion(): \DateTime
    {
        return $this->fecha_creacion;
    }

    public function getFecha_modificacion(): ?\DateTime
    {
        return $this->fecha_modificacion;
    }

    public function getIsAdmin(): bool
    {
        return $this->isadmin;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function getIdTipoUsuario(): int
    {
        return $this->idTipoUsuario;
    }

    // Implementación de JsonSerializable
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nombreUsuario' => $this->nombreUsuario,
            'contrasenia' => $this->contrasenia,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'correo' => $this->correo,
            'fecha_creacion' => $this->fecha_creacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fecha_modificacion ? $this->fecha_modificacion->format(\DateTime::ISO8601) : null,
            'isadmin' => $this->isadmin,
            'estado' => $this->estado,
            'idTipoUsuario' => $this->idTipoUsuario
        ];
    }
}
