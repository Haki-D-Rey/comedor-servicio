<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'catalogo.servicios_productos_detalles')]
class ServiciosProductosDetalles
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

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado;

    /**
     * Many ServiciosProductosDetalles have One Sistema.
     * @var Sistemas
     */
    #[ORM\ManyToOne(targetEntity: Sistemas::class, inversedBy: 'serviciosProductosDetalles', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'id_sistemas', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Sistemas $sistemas = null;

    /**
     * Many ServiciosProductosDetalles have One TipoServicio.
     * @var TipoServicios
     */
    #[ORM\ManyToOne(targetEntity: TipoServicios::class, inversedBy: 'serviciosProductosDetalles', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'id_tipo_servicios', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private TipoServicios $tipoServicios;

    /**
     * Many ServiciosProductosDetalles have One ServicioProducto.
     * @var ServiciosProductos
     */
    #[ORM\ManyToOne(targetEntity: ServiciosProductos::class, inversedBy: 'serviciosProductosDetalles', cascade: ['persist'])]
    #[ORM\JoinColumn(name: 'id_servicios_productos', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ServiciosProductos $serviciosProductos;

    /**
     * Many ServiciosProductosDetalles have One ServicioProducto.
     * @var ServiciosProductos
     */
    #[ORM\OneToMany(targetEntity: DetalleZonaServicioHorario::class, mappedBy: 'serviciosProductosDetalles')]
    #[ORM\JoinColumn(name: 'id_servicios_productos', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $detalleZonaServicioHorario;

    #[ORM\Column(name: 'fecha_inicio_periodo', type: 'date', nullable: false)]
    private \DateTime $periodoInicial;

    #[ORM\Column(name: 'fecha_fin_periodo', type: 'date', nullable: false)]
    private \DateTime $periodoFinal;

    #[ORM\Column(name: 'orden', type: 'integer')]
    private ?int $orden = 1;

    public function __construct()
    {
        // Constructor logic here
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

    public function getSistemas(): ?Sistemas
    {
        return $this->sistemas;
    }

    public function setSistemas(Sistemas $sistemas): self
    {
        $this->sistemas = $sistemas;
        return $this;
    }

    public function getTipoServicios(): ?TipoServicios
    {
        return $this->tipoServicios;
    }

    public function setTipoServicios(TipoServicios $tipoServicios): self
    {
        $this->tipoServicios = $tipoServicios;
        return $this;
    }

    public function getServiciosProductos(): ?ServiciosProductos
    {
        return $this->serviciosProductos;
    }

    public function setServiciosProductos(ServiciosProductos $serviciosProductos): self
    {
        $this->serviciosProductos = $serviciosProductos;
        return $this;
    }

    public function getIdSistemas(): ?int
    {
        return $this->sistemas ? $this->sistemas->getId() : null;
    }

    public function getIdTipoServicios(): ?int
    {
        return $this->tipoServicios ? $this->tipoServicios->getId() : null;
    }

    public function getIdServiciosProductos(): ?int
    {
        return $this->serviciosProductos ? $this->serviciosProductos->getId() : null;
    }

    public function getPeriodoIncial(): \DateTime
    {
        return $this->periodoInicial;
    }

    public function setPeriodoIncial(\DateTime $periodoInicial): self
    {
        $this->periodoInicial = $periodoInicial;
        return $this;
    }

    public function getPeriodoFinal(): \DateTime
    {
        return $this->periodoFinal;
    }

    public function setPeriodoFinal(\DateTime $periodoFinal): self
    {
        $this->periodoFinal = $periodoFinal;
        return $this;
    }

    public function getOrden(): int
    {
        return $this->orden;
    }

    public function setOrden(int $orden): self
    {
        $this->orden = $orden;
        return $this;
    }
}
