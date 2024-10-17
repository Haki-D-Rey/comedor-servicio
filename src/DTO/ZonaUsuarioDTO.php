<?php

namespace App\DTO;

use JsonSerializable;

class ZonaUsuarioDTO implements JsonSerializable
{
    private ?int $id;
    private int $id_zona;
    private int $id_usuario;
    private string $codigo_interno;
    private \DateTime $fecha_creacion;
    private ?\DateTime $fecha_modificacion;
    private bool $estado;

    public function __construct(
        ?int $id,
        int $id_zona,
        int $id_usuario,
        string $codigo_interno,
        \DateTime $fecha_creacion,
        ?\DateTime $fecha_modificacion,
        bool $estado
    ) {
        $this->id = $id;
        $this->id_zona = $id_zona;
        $this->id_usuario = $id_usuario;
        $this->codigo_interno = $codigo_interno;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_modificacion = $fecha_modificacion;
        $this->estado = $estado;
    }

    // Getters
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdZona(): int
    {
        return $this->id_zona;
    }

    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }

    public function getCodigoInterno(): string
    {
        return $this->codigo_interno;
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

    // Implementación de jsonSerialize para la serialización JSON
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'id_zona' => $this->id_zona,
            'id_usuario' => $this->id_usuario,
            'fecha_creacion' => $this->fecha_creacion->format(\DateTime::ISO8601),
            'fecha_modificacion' => $this->fecha_modificacion ? $this->fecha_modificacion->format(\DateTime::ISO8601) : null,
            'estado' => $this->estado,
        ];
    }
}
