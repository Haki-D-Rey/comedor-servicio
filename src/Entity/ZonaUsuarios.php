<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'seguridad.zona_usuario')]
class ZonaUsuarios
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_zona', type: 'integer')]
    private int $id_zona;

    #[ORM\Column(name: 'id_usuario', type: 'integer')]
    private int $id_usuario;

    #[ORM\Column(name: 'codigo_interno', type: 'string', length: 64, nullable: false, unique: true)]
    private string $codigo_interno;

    #[ORM\Column(name: 'fecha_creacion', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fecha_creacion;

    #[ORM\Column(name: 'fecha_modificacion', type: 'datetime', nullable: true)]
    private ?\DateTime $fecha_modificacion = null;

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado;

    /**
     * Many ZonaUsuario have One Zona.
     * @var Zona
     */
    #[ORM\ManyToOne(targetEntity: Zona::class)]
    #[ORM\JoinColumn(name: 'id_zona', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Zona $zona = null;

    /**
     * Many ZonaUsuario have One Usuario.
     * @var Usuario
     */
    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'id_usuario', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Usuario $usuario = null;


    /**
     * One TipoServicio has Many ServiciosProductosDetalles.
     * @var Collection<int, ServiciosProductosDetalles>
     */
    #[ORM\OneToMany(targetEntity: DetalleZonaServicioHorario::class, mappedBy: 'detalleZonaServicioHorario')]
    private Collection $detalleZonaServicioHorario;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdZona(): int
    {
        return $this->id_zona;
    }

    public function setIdZona(int $id_zona): self
    {
        $this->id_zona = $id_zona;
        return $this;
    }

    public function getIdUsuario(): int
    {
        return $this->id_usuario;
    }

    public function setIdUsuario(int $id_usuario): self
    {
        $this->id_usuario = $id_usuario;
        return $this;
    }

    public function getCodigoInterno(): string
    {
        return $this->codigo_interno;
    }

    public function setCodigoInterno(int $codigo_interno): self
    {
        $this->codigo_interno = $codigo_interno;
        return $this;
    }

    public function getFechaCreacion(): ?\DateTimeInterface
    {
        return $this->fecha_creacion;
    }

    public function setFechaCreacion(\DateTimeInterface $fecha_creacion): self
    {
        $this->fecha_creacion = $fecha_creacion;
        return $this;
    }

    public function getFechaModificacion(): ?\DateTimeInterface
    {
        return $this->fecha_modificacion;
    }

    public function setFechaModificacion(?\DateTimeInterface $fecha_modificacion): self
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

    public function getZona(): ?Zona
    {
        return $this->zona;
    }

    public function setZona(?Zona $zona): self
    {
        $this->zona = $zona;
        return $this;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(?Usuario $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }
}
