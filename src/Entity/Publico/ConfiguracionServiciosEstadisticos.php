<?php

namespace App\Entity\Publico;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'public.configuracion_servicios_estadisticos')]
class ConfiguracionServiciosEstadisticos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'json_configuracion', type: 'jsonb')]
    private array $jsonConfiguracion;

    #[ORM\Column(name: 'fecha_corte', type: 'datetime')]
    private \DateTime $fechaCorte;

    #[ORM\Column(name: 'fecha_creacion', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fechaCreacion;

    #[ORM\Column(name: 'fecha_modificacion', type: 'datetime', nullable: true)]
    private ?\DateTime $fechaModificacion = null;

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado = true;

    public function __construct()
    {
        $this->fechaCreacion = new \DateTime(); // Establece la fecha de creación al momento actual
    }

    // Getters y Setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJsonConfiguracion(): array
    {
        return $this->jsonConfiguracion;
    }

    public function setJsonConfiguracion(array $configuracion): self
    {
        $this->jsonConfiguracion = $configuracion;
        return $this;
    }

    public function getFechaCorte(): ?\DateTime
    {
        return $this->fechaCorte;
    }

    public function setFechaCorte(?\DateTime $fechaCorte): self
    {
        $this->fechaCorte = $fechaCorte;
        return $this;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fechaCreacion;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fechaModificacion;
    }

    public function setFechaModificacion(?\DateTime $fecha): self
    {
        $this->fechaModificacion = $fecha;
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
