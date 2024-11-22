<?php

namespace App\Entity\Catalogo;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'departamento', schema: 'catalogo')]
class Departamento
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'nombre', type: 'string', length: 128, nullable: false)]
    private string $nombre;

    #[ORM\Column(name: 'descripcion', type: 'string', length: 256, nullable: true)]
    private ?string $descripcion = null;

    #[ORM\Column(name: 'codigo_interno', type: 'string', length: 64, nullable: false, unique: true)]
    private string $codigoInterno;

    #[ORM\Column(name: 'fecha_creacion', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fecha_creacion;

    #[ORM\Column(name: 'fecha_modificacion', type: 'datetime', nullable: true)]
    private ?\DateTime $fecha_modificacion = null;

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado;

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;
        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getCodigoInterno(): string
    {
        return $this->codigoInterno;
    }

    public function setCodigoInterno(string $codigoInterno): self
    {
        $this->codigoInterno = $codigoInterno;
        return $this;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fecha_creacion;
    }

    public function setFechaCreacion(\DateTime $fecha_creacion): self
    {
        $this->fecha_creacion = $fecha_creacion;
        return $this;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fecha_modificacion;
    }

    public function setFechaModificacion(?\DateTime $fecha_modificacion): self
    {
        $this->fecha_modificacion = $fecha_modificacion;
        return $this;
    }

    public function getEstado(): bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): self
    {
        $this->estado = $estado;
        return $this;
    }
}
