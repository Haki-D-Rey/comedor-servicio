<?php

namespace App\Entity\Publico;

use App\Entity\DetalleZonaServicioHorario;
use App\Entity\Publico\ConfiguracionServiciosEstadisticos;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
#[ORM\Table(name: 'public.control_estadisticos_servicios')]
class ControlEstadisticosServicios
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    #[ORM\Column(name: 'uuid', type: 'uuid', unique: true)]
    private UuidInterface $uuid;

    #[ORM\ManyToOne(targetEntity: DetalleZonaServicioHorario::class, inversedBy: 'detalleZonaServicioHorario')]
    #[ORM\JoinColumn(name: 'id_detalle_zona_servicio_horario', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private DetalleZonaServicioHorario $detalleZonaServicioHorario;

    #[ORM\ManyToOne(targetEntity: ConfiguracionServiciosEstadisticos::class, inversedBy: 'configuracionServiciosEstadisticos')]
    #[ORM\JoinColumn(name: 'id_configuracion_servicios_estadisticos', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ConfiguracionServiciosEstadisticos $configuracionServiciosEstadisticos;

    #[ORM\Column(name: 'cantidad_firmada', type: 'integer', options: ['default' => 0])]
    private int $cantidadFirmada = 0;

    #[ORM\Column(name: 'cantidad_anulada', type: 'integer', options: ['default' => 0])]
    private int $cantidadAnulada = 0;

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
        // Genera automáticamente un UUID al crear un nuevo registro
        $this->uuid = Uuid::uuid4(); // Genera un nuevo UUID
        $this->fechaCreacion = new \DateTime();
    }

    // Getters y Setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid->toString();
    }

    public function setIdUuid(UuidInterface $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getDetalleZonaServicioHorario(): DetalleZonaServicioHorario
    {
        return $this->detalleZonaServicioHorario;
    }

    public function setDetalleZonaServicioHorario(DetalleZonaServicioHorario $detalleZonaServicioHorario): self
    {
        $this->detalleZonaServicioHorario = $detalleZonaServicioHorario;
        return $this;
    }

    public function getConfiguracionServiciosEstadisticos(): ConfiguracionServiciosEstadisticos
    {
        return $this->configuracionServiciosEstadisticos;
    }

    public function setConfiguracionServiciosEstadisticos(ConfiguracionServiciosEstadisticos $configuracionServiciosEstadisticos): self
    {
        $this->configuracionServiciosEstadisticos = $configuracionServiciosEstadisticos;
        return $this;
    }

    public function getCantidadFirmada(): int
    {
        return $this->cantidadFirmada;
    }

    public function setCantidadFirmada(int $cantidad): self
    {
        $this->cantidadFirmada = $cantidad;
        return $this;
    }

    public function getCantidadAnulada(): int
    {
        return $this->cantidadAnulada;
    }

    public function setCantidadAnulada(int $cantidad): self
    {
        $this->cantidadAnulada = $cantidad;
        return $this;
    }

    public function getFechaCorte(): \DateTime
    {
        return $this->fechaCorte;
    }

    public function setFechaCorte(\DateTime $fecha): self
    {
        $this->fechaCorte = $fecha;
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

    // Métodos para obtener los IDs de las entidades relacionadas
    public function getDetalleZonaServicioHorarioId(): ?int
    {
        return $this->detalleZonaServicioHorario->getId(); // Asumiendo que la clase DetalleZonaServicioHorario tiene un método getId()
    }

    public function getConfiguracionServiciosEstadisticosId(): ?int
    {
        return $this->configuracionServiciosEstadisticos->getId(); // Asumiendo que la clase ConfiguracionServiciosEstadisticos tiene un método getId()
    }
}
