<?php 

namespace App\Entity\Publico;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Publico\DetalleZonaServicioHorarioClienteFacturacion; // Asegúrate de tener la ruta correcta para esta entidad

#[ORM\Entity]
#[ORM\Table(name: 'cliente_credito_periodico', schema: 'public')]
class ClientesCreditoPeriodico
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'id_detalle_zona_servicio_horario_cliente_facturacion', type: 'integer')]
    private ?int $idDetalleZonaServicioHorarioClienteFacturacion;

    #[ORM\ManyToOne(targetEntity: DetalleZonaServicioHorarioClienteFacturacion::class)]
    #[ORM\JoinColumn(name: 'id_detalle_zona_servicio_horario_cliente_facturacion', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private DetalleZonaServicioHorarioClienteFacturacion $detalleZonaServicioHorarioClienteFacturacion;

    #[ORM\Column(name: 'periodo_inicial', type: 'date', nullable: false)]
    private \DateTime $periodoInicial;

    #[ORM\Column(name: 'periodo_final', type: 'date', nullable: false)]
    private \DateTime $periodoFinal;

    #[ORM\Column(name: 'cantidad_credito_limite', type: 'integer', nullable: false, options: ['default' => 0])]
    private int $cantidadCreditoLimite = 0;

    #[ORM\Column(name: 'cantidad_credito_usado', type: 'integer', nullable: false, options: ['default' => 0])]
    private int $cantidadCreditoUsado = 0;

    #[ORM\Column(name: 'cantidad_credito_disponible', type: 'integer', nullable: false, options: ['default' => 0])]
    private int $cantidadCreditoDisponible = 0;

    #[ORM\Column(name: 'fecha_creacion', type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fechaCreacion;

    #[ORM\Column(name: 'fecha_modificacion', type: 'datetime', nullable: true)]
    private ?\DateTime $fechaModificacion = null;

    #[ORM\Column(name: 'estado', type: 'boolean', options: ['default' => true])]
    private bool $estado;

    // Getters y Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDetalleZonaServicioHorarioClienteFacturacion(): DetalleZonaServicioHorarioClienteFacturacion
    {
        return $this->detalleZonaServicioHorarioClienteFacturacion;
    }

    public function setDetalleZonaServicioHorarioClienteFacturacion(DetalleZonaServicioHorarioClienteFacturacion $detalleZonaServicioHorarioClienteFacturacion): self
    {
        $this->detalleZonaServicioHorarioClienteFacturacion = $detalleZonaServicioHorarioClienteFacturacion;
        return $this;
    }

    public function getPeriodoInicial(): \DateTime
    {
        return $this->periodoInicial;
    }

    public function setPeriodoInicial(\DateTime $periodoInicial): self
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

    public function getCantidadCreditoLimite(): int
    {
        return $this->cantidadCreditoLimite;
    }

    public function setCantidadCreditoLimite(int $cantidadCreditoLimite): self
    {
        $this->cantidadCreditoLimite = $cantidadCreditoLimite;
        return $this;
    }

    public function getCantidadCreditoUsado(): int
    {
        return $this->cantidadCreditoUsado;
    }

    public function setCantidadCreditoUsado(int $cantidadCreditoUsado): self
    {
        $this->cantidadCreditoUsado = $cantidadCreditoUsado;
        return $this;
    }

    public function getCantidadCreditoDisponible(): int
    {
        return $this->cantidadCreditoDisponible;
    }

    public function setCantidadCreditoDisponible(int $cantidadCreditoDisponible): self
    {
        $this->cantidadCreditoDisponible = $cantidadCreditoDisponible;
        return $this;
    }

    public function getFechaCreacion(): \DateTime
    {
        return $this->fechaCreacion;
    }

    public function setFechaCreacion(\DateTime $fechaCreacion): self
    {
        $this->fechaCreacion = $fechaCreacion;
        return $this;
    }

    public function getFechaModificacion(): ?\DateTime
    {
        return $this->fechaModificacion;
    }

    public function setFechaModificacion(?\DateTime $fechaModificacion): self
    {
        $this->fechaModificacion = $fechaModificacion;
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

    public function getExisteCantidadDisponible(int $cantidadFacturar): bool
    {
        $disponible = ($this->cantidadCreditoLimite - $this->cantidadCreditoUsado) - $cantidadFacturar;
        return $disponible >= 0;
    }
}
