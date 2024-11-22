<?php

namespace App\DTO\Publico;

use JsonSerializable;

class ClientesDTO implements JsonSerializable
{
    private $id;
    private $nombres;
    private $apellidos;
    private $id_departamento;
    private $id_cargo;
    private $correo;
    private $clie_docnum;
    private $fecha_creacion;
    private $fecha_modificacion = null;
    private $estado;

    public function __construct(
        ?int $id,
        string $nombres,
        string $apellidos,
        int $id_departamento,
        int $id_cargo,
        string $correo,
        string $clie_docnum,
        \DateTime $fecha_creacion,
        ?\DateTime $fecha_modificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->nombres = $nombres;
        $this->apellidos = $apellidos;
        $this->id_departamento = $id_departamento;
        $this->id_cargo = $id_cargo;
        $this->correo = $correo;
        $this->clie_docnum = str_pad($clie_docnum, 10, '0', STR_PAD_LEFT);
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_modificacion = $fecha_modificacion;
        $this->estado = $estado;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombres(): string
    {
        return $this->nombres;
    }

    public function getApellidos(): string
    {
        return $this->apellidos;
    }

    public function getIdDepartamento(): int
    {
        return $this->id_departamento;
    }

    public function getIdCargo(): int
    {
        return $this->id_cargo;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    public function getClieDocnum(): string
    {
        return $this->clie_docnum;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fecha_creacion;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fecha_modificacion;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    // Implementación de JsonSerializable
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'id_departamento' => $this->id_departamento,
            'id_cargo' => $this->id_cargo,
            'correo' => $this->correo,
            'clie_docnum' => $this->clie_docnum,
            'fecha_creacion' => $this->fecha_creacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fecha_modificacion ? $this->fecha_modificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado
        ];
    }
}
