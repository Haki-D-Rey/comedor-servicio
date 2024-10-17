<?php

namespace App\Entity;

use App\Entity\Publico\ControlEstadisticosServicios;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'catalogo.detalle_zona_servicio_horario')]
class DetalleZonaServicioHorario
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_servicios_productos_detalles', type: 'integer', nullable: false)]
    private int $idServiciosProductosDetalles;

    #[ORM\Column(name: 'id_horario', type: 'integer', nullable: false)]
    private int $idHorario;

    #[ORM\Column(name: 'id_zona_usuario', type: 'integer', nullable: false)]
    private int $idZonaUsuario;

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

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado;

    /**
     * Many DetalleZonaServicioHorario have One Sistema.
     * @var ZonaUsuarios
     */
    #[ORM\ManyToOne(targetEntity: ZonaUsuarios::class, inversedBy: 'detalleZonaServicioHorario')]
    #[ORM\JoinColumn(name: 'id_zona_usuario', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ZonaUsuarios $zonaUsuario;

    /**
     * Many ServiciosProductosDetalles have One Sistema.
     * @var Horario
     */
    #[ORM\ManyToOne(targetEntity: Horario::class, inversedBy: 'detalleZonaServicioHorario')]
    #[ORM\JoinColumn(name: 'id_horario', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Horario $horario;

    /**
     * Many ServiciosProductosDetalles have One Sistema.
     * @var ServiciosProductosDetalles
     */
    #[ORM\ManyToOne(targetEntity: ServiciosProductosDetalles::class, inversedBy: 'detalleZonaServicioHorario')]
    #[ORM\JoinColumn(name: 'id_servicios_productos_detalles', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ServiciosProductosDetalles $serviciosProductosDetalles;


    /**
     * One ServicioProducto has Many ControlEstadisticosServicios.
     * @var Collection<int, ControlEstadisticosServicios>
     */
    #[ORM\OneToMany(targetEntity: ControlEstadisticosServicios::class, mappedBy: 'controlEstadisticosServicios')]
    private Collection $ControlEstadisticosServicios;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdServiciosProductosDetalles(): int
    {
        return $this->idServiciosProductosDetalles;
    }

    public function setIdServiciosProductosDetalles(int $idServiciosProductosDetalles): self
    {
        $this->idServiciosProductosDetalles = $idServiciosProductosDetalles;
        return $this;
    }

    public function getIdHorario(): int
    {
        return $this->idHorario;
    }

    public function setIdHorario(int $idHorario): self
    {
        $this->idHorario = $idHorario;
        return $this;
    }

    public function getIdZonaUsuario(): int
    {
        return $this->idZonaUsuario;
    }

    public function setIdZonaUsuario(int $idZonaUsuario): self
    {
        $this->idZonaUsuario = $idZonaUsuario;
        return $this;
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

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;
        return $this;
    }

    public function getCodigoInterno(): string
    {
        return $this->codigo_interno;
    }

    public function setCodigoInterno(string $codigo_interno): self
    {
        $this->codigo_interno = $codigo_interno;
        return $this;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fecha_creacion;
    }


    public function setFechaCreacion(?\DateTime $fecha_creacion): self
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

    public function getServiciosProductosDetalles(): ?ServiciosProductosDetalles
    {
        return $this->serviciosProductosDetalles;
    }

    public function setServiciosProductosDetalles(?ServiciosProductosDetalles $serviciosProductosDetalles): self
    {
        $this->serviciosProductosDetalles = $serviciosProductosDetalles;
        return $this;
    }

    public function getHorario(): ?Horario
    {
        return $this->horario;
    }

    public function setHorario(?Horario $horario): self
    {
        $this->horario = $horario;
        return $this;
    }

    public function getZonaUsuario(): ?ZonaUsuarios
    {
        return $this->zonaUsuario;
    }

    public function setZonaUsuario(?ZonaUsuarios $zonaUsuario): self
    {
        $this->zonaUsuario = $zonaUsuario;
        return $this;
    }
}
