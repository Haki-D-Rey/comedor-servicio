<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\TimeType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'catalogo.horario')]
class Horario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'nombre', type: 'string', length: 128)]
    private string $nombre;

    #[ORM\Column(name: 'descripcion', type: 'string', length: 256)]
    private string $descripcion;

    #[ORM\Column(name: 'codigo_interno', type: 'string', length: 64, nullable: false, unique: true)]
    private string $codigo_interno;

    #[ORM\Column(name: 'fecha_creacion', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fecha_creacion;

    #[ORM\Column(name: 'fecha_modificacion', type: 'datetime', nullable: true)]
    private ?\DateTime $fecha_modificacion = null;

    #[ORM\Column(name: 'inicio', type: 'time', nullable: false)]
    private ?\DateTime $inicio = null;
    
    #[ORM\Column(name: 'fin', type: 'time', nullable: false)]
    private ?\DateTime $fin = null;

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado;

    /**
     * One ServicioProducto has Many DetalleZonaServicioHorario.
     * @var Collection<int, DetalleZonaServicioHorario>
     */
    #[ORM\OneToMany(targetEntity: DetalleZonaServicioHorario::class, mappedBy: 'horario')]
    private Collection $detalleZonaServicioHorario;

    public function __construct()
    {
        $this->detalleZonaServicioHorario = new ArrayCollection();
    }

    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombre(): ?string
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

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getcodigo_interno(): ?string
    {
        return $this->codigo_interno;
    }

    public function setcodigo_interno(string $codigo_interno): self
    {
        $this->codigo_interno = $codigo_interno;
        return $this;
    }

    public function getPeriodoInicio(): ?\DateTime
    {
        return $this->inicio;
    }

    public function setPeriodoInicio(\DateTime $inicio): self
    {
        $this->inicio = $inicio;
        return $this;
    }

    public function getPeriodoFin(): ?\DateTime
    {
        return $this->fin;
    }

    public function setPeriodoFin(\DateTime $fin): self
    {
        $this->fin = $fin;
        return $this;
    }

    public function getFecha_creacion(): ?\DateTimeInterface
    {
        return $this->fecha_creacion;
    }

    public function setFecha_creacion(\DateTimeInterface $fecha_creacion): self
    {
        $this->fecha_creacion = $fecha_creacion;
        return $this;
    }

    public function getFecha_modificacion(): ?\DateTimeInterface
    {
        return $this->fecha_modificacion;
    }

    public function setFecha_modificacion(?\DateTimeInterface $fecha_modificacion): self
    {
        $this->fecha_modificacion = $fecha_modificacion;
        return $this;
    }

    public function getEstado(): ?bool
    {
        return $this->estado;
    }

    public function setEstado(bool $estado): self
    {
        $this->estado = $estado;
        return $this;
    }
}
