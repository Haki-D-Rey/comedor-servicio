<?php

namespace App\Entity\Seguridad;

use App\Entity\Seguridad\Permisos;
use App\Entity\Seguridad\TipoUsuario;
use App\Entity\Usuario;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'seguridad.tipo_usuario_permisos')]
class TipoUsuarioPermisos
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Usuario::class)]
    #[ORM\JoinColumn(name: 'usuario_id', referencedColumnName: 'id')]
    private ?Usuario $usuario = null;

    // Relación ManyToOne con TipoUsuario
    #[ORM\ManyToOne(targetEntity: TipoUsuario::class)]
    #[ORM\JoinColumn(name: 'tipo_usuario_id', referencedColumnName: 'id')]
    private ?TipoUsuario $tipoUsuario = null;

    // Relación ManyToOne con Permiso
    #[ORM\ManyToOne(targetEntity: Permisos::class)]
    #[ORM\JoinColumn(name: 'permiso_id', referencedColumnName: 'id')]
    private ?Permisos $permiso = null;

    // Campos adicionales para la tabla intermedia
    #[ORM\Column(type: 'datetime', name: 'fecha_creacion', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTime $fechaCreacion;

    #[ORM\Column(type: 'datetime', name: 'fecha_modificacion', nullable: true)]
    private ?\DateTime $fechaModificacion = null;

    #[ORM\Column(type: 'boolean', name: 'estado', options: ['default' => true])]
    private bool $estado = true;

    #[ORM\Column(type: 'string', length: 512, name: 'descripcion')]
    private ?string $descripcion = null;

    #[ORM\Column(type: 'string', length: 64, name: 'codigo_interno', unique: true)]
    private string $codigoInterno;

    // Métodos getter y setter

    public function getId(): int
    {
        return $this->id;
    }

    public function getUsuario(): ?Usuario
    {
        return $this->usuario;
    }

    public function setUsuario(Usuario $usuario): self
    {
        $this->usuario = $usuario;
        return $this;
    }

    public function getTipoUsuario(): ?TipoUsuario
    {
        return $this->tipoUsuario;
    }

    public function setTipoUsuario(TipoUsuario $tipoUsuario): self
    {
        $this->tipoUsuario = $tipoUsuario;
        return $this;
    }

    public function getPermiso(): ?Permisos
    {
        return $this->permiso;
    }

    public function setPermiso(Permisos $permiso): self
    {
        $this->permiso = $permiso;
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
}
