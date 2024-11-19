<?php

declare(strict_types=1);

namespace MyProject\Component\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119183617 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migraciones 2 Parte: 
                Tablas
                ->Catalogo:
                    Cargo, Departamento, IdentificacionFacturacion
                ->Public:
                    Cliente, DetalleClienteIdentificacionFacturacion, DetalleZonaServicioHorarioClienteFacturacion, ClienteCreditoPeriodico, Ventas';
    }

    public function up(Schema $schema): void
    {
        //SCHEMA CATALOGO

        // Crear tabla catalogo.departamento
        $this->addSql('CREATE TABLE IF NOT EXISTS catalogo.departamento (
                        id SERIAL NOT NULL,
                        nombre VARCHAR(128) NOT NULL,
                        descripcion VARCHAR(256) NOT NULL,
                        codigo_interno VARCHAR(64) NOT NULL,
                        fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
                        fecha_modificacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                        estado BOOLEAN DEFAULT TRUE NOT NULL,
                        PRIMARY KEY(id)
                    )');

        $this->addSql('CREATE UNIQUE INDEX uniq_departamento_codigo_interno ON catalogo.departamento (codigo_interno)');
        $this->addSql('CREATE INDEX idx_departamento_nombre ON catalogo.departamento USING gin (to_tsvector(\'spanish\', nombre))');
        $this->addSql('CREATE INDEX idx_departamento_descripcion ON catalogo.departamento USING gin (to_tsvector(\'spanish\', descripcion))');
        $this->addSql('CREATE INDEX idx_departamento_estado ON catalogo.departamento (estado)');

        // Crear tabla catalogo.cargo
        $this->addSql('CREATE TABLE IF NOT EXISTS catalogo.cargo (
                        id SERIAL NOT NULL,
                        nombre VARCHAR(128) NOT NULL,
                        descripcion VARCHAR(256) NOT NULL,
                        codigo_interno VARCHAR(64) NOT NULL,
                        fecha_creacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT CURRENT_TIMESTAMP NOT NULL,
                        fecha_modificacion TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL,
                        estado BOOLEAN DEFAULT TRUE NOT NULL,
                        PRIMARY KEY(id)
                    )');

        $this->addSql('CREATE UNIQUE INDEX uniq_cargo_codigo_interno ON catalogo.cargo (codigo_interno)');
        $this->addSql('CREATE INDEX idx_cargo_nombre ON catalogo.cargo USING gin (to_tsvector(\'spanish\', nombre))');
        $this->addSql('CREATE INDEX idx_cargo_descripcion ON catalogo.cargo USING gin (to_tsvector(\'spanish\', descripcion))');
        $this->addSql('CREATE INDEX idx_cargo_estado ON catalogo.cargo (estado)');

        $this->addSql('CREATE TABLE IF NOT EXISTS catalogo.estado_ventas (
                        id SERIAL PRIMARY KEY,
                        nombre VARCHAR(128) NOT NULL,
                        descripcion VARCHAR(256) NOT NULL,
                        codigo_interno VARCHAR(64) NOT NULL,
                        fecha_creacion TIMESTAMP DEFAULT NOW(),
                        fecha_modificacion TIMESTAMP NULL,
                        estado BOOLEAN DEFAULT TRUE,
                        UNIQUE (id, codigo_interno)
                    );');

        $this->addSql('CREATE INDEX IF NOT EXISTS idx_estado_ventas_codigo_interno ON catalogo.estado_ventas USING gin (to_tsvector(\'spanish\', codigo_interno));');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_estado_ventas_descripcion ON catalogo.estado_ventas USING gin (to_tsvector(\'spanish\', descripcion));');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_estado_ventas_estado ON catalogo.estado_ventas (estado);');

        // SCHEMA PUBLICO

        $this->addSql('CREATE TABLE IF NOT EXISTS public.cliente (
            id SERIAL PRIMARY KEY,
            nombres VARCHAR(128) NOT NULL,
            apellidos VARCHAR(128) NOT NULL,
            id_departamento INTEGER NOT NULL,
            id_cargo INTEGER NOT NULL,
            correo VARCHAR(256) NOT NULL UNIQUE,
            clie_docnum VARCHAR(64) NOT NULL UNIQUE,
            fecha_creacion TIMESTAMP DEFAULT NOW(),
            fecha_modificacion TIMESTAMP NULL,
            estado BOOLEAN DEFAULT TRUE,
            FOREIGN KEY (id_departamento) REFERENCES catalogo.departamento(id) ON DELETE CASCADE,
            FOREIGN KEY (id_cargo) REFERENCES catalogo.cargo(id) ON DELETE CASCADE
        );');
        
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_cliente_id_departamento ON public.cliente(id_departamento);');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_cliente_id_cargo ON public.cliente(id_cargo);');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_cliente_clie_docnum ON public.cliente(clie_docnum);');
        $this->addSql("CREATE INDEX IF NOT EXISTS idx_cliente_nombres_apellidos ON public.cliente USING gin (to_tsvector('spanish', nombres || ' ' || apellidos));");

        $this->addSql('CREATE TABLE IF NOT EXISTS public.detalle_cliente_identificacion_facturacion (
            id SERIAL PRIMARY KEY,
            id_cliente INTEGER NOT NULL,
            id_identificacion_facturacion INTEGER NOT NULL,
            json_identificacion JSONB,
            fecha_creacion TIMESTAMP DEFAULT NOW(),
            fecha_modificacion TIMESTAMP NULL,
            estado BOOLEAN DEFAULT TRUE,
            FOREIGN KEY (id_cliente) REFERENCES public.cliente(id) ON DELETE CASCADE,
            FOREIGN KEY (id_identificacion_facturacion) REFERENCES catalogo.lista_catalogo_detalle(id_valor) ON DELETE CASCADE
        );');
    
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_detalle_cliente_identificacion_facturacion_id_cliente ON public.detalle_cliente_identificacion_facturacion(id_cliente);');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_detalle_cliente_identificacion_facturacion_id_identificacion_facturacion ON public.detalle_cliente_identificacion_facturacion(id_identificacion_facturacion);');


        $this->addSql('CREATE TABLE IF NOT EXISTS public.detalle_zona_servicio_horario_cliente_facturacion (
            id SERIAL PRIMARY KEY,
            id_detalle_cliente_identificacion_facturacion INTEGER NOT NULL,
            id_detalle_zona_servicio_horario INTEGER NOT NULL,
            codigo_interno VARCHAR(64) NOT NULL UNIQUE,
            fecha_creacion TIMESTAMP DEFAULT NOW(),
            fecha_modificacion TIMESTAMP NULL,
            estado BOOLEAN DEFAULT TRUE,
            FOREIGN KEY (id_detalle_cliente_identificacion_facturacion) REFERENCES public.detalle_cliente_identificacion_facturacion(id) ON DELETE CASCADE,
            FOREIGN KEY (id_detalle_zona_servicio_horario) REFERENCES catalogo.detalle_zona_servicio_horario(id) ON DELETE CASCADE
        );');
    
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_detalle_zona_servicio_horario_cliente_facturacion_id_detalle_cliente_identificacion ON public.detalle_zona_servicio_horario_cliente_facturacion(id_detalle_cliente_identificacion_facturacion);');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_detalle_zona_servicio_horario_cliente_facturacion_id_detalle_zona_servicio_horario ON public.detalle_zona_servicio_horario_cliente_facturacion(id_detalle_zona_servicio_horario);');

        $this->addSql('CREATE TABLE IF NOT EXISTS public.cliente_credito_periodico (
            id SERIAL PRIMARY KEY,
            id_detalle_zona_servicio_horario_cliente_facturacion INTEGER NOT NULL,
            periodo_inicial DATE NOT NULL,
            periodo_final DATE NOT NULL,
            cantidad_credito_limite INTEGER NOT NULL DEFAULT 0,
            cantidad_credito_usado INTEGER NOT NULL DEFAULT 0,
            cantidad_credito_disponible INTEGER NOT NULL DEFAULT 0,
            fecha_creacion TIMESTAMP DEFAULT NOW(),
            fecha_modificacion TIMESTAMP NULL,
            estado BOOLEAN DEFAULT TRUE,
            CONSTRAINT FK_cliente_credito_periodico_detalle_zona FOREIGN KEY (id_detalle_zona_servicio_horario_cliente_facturacion) REFERENCES detalle_zona_servicio_horario_cliente_facturacion(id) ON DELETE CASCADE
        )');

        $this->addSql('CREATE INDEX IF NOT EXISTS idx_cliente_credito_periodico_id_detalle_zona_servicio_horario_cliente_facturacion ON cliente_credito_periodico (id_detalle_zona_servicio_horario_cliente_facturacion)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_cliente_credito_periodico_periodo_inicial ON cliente_credito_periodico (periodo_inicial)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_cliente_credito_periodico_periodo_final ON cliente_credito_periodico (periodo_final)');

        $this->addSql('CREATE TABLE IF NOT EXISTS public.ventas (
            id SERIAL PRIMARY KEY,
            uuid UUID DEFAULT gen_random_uuid(),
            id_detalle_zona_servicio_horario_cliente_facturacion INTEGER NOT NULL,
            cantidad_facturada INTEGER NOT NULL DEFAULT 0,
            ticket_anulado BOOLEAN DEFAULT FALSE,
            cantidad_anulada INTEGER DEFAULT 0,
            fecha_emision TIMESTAMP DEFAULT NOW(),
            fecha_modificacion TIMESTAMP NULL,
            id_estado_venta INTEGER NOT NULL,
            estado BOOLEAN DEFAULT TRUE,
            UNIQUE (id, uuid),
            CONSTRAINT FK_ventas_detalle_zona FOREIGN KEY (id_detalle_zona_servicio_horario_cliente_facturacion) REFERENCES detalle_zona_servicio_horario_cliente_facturacion(id) ON DELETE CASCADE,
            CONSTRAINT FK_ventas_estado_venta FOREIGN KEY (id_estado_venta) REFERENCES catalogo.lista_catalogo_detalle(id_valor) ON DELETE CASCADE
        )');

        $this->addSql('CREATE INDEX IF NOT EXISTS idx_ventas_id_detalle_zona_servicio_horario_cliente_facturacion ON ventas (id_detalle_zona_servicio_horario_cliente_facturacion)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_ventas_id_estado_venta ON ventas (id_estado_venta)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_ventas_fecha_creacion ON ventas (fecha_emision)');
        $this->addSql('CREATE INDEX IF NOT EXISTS idx_ventas_uuid_ticket ON ventas (uuid)');
    }

    public function down(Schema $schema): void
    {

        $this->addSql('DROP TABLE IF EXISTS public.cliente CASCADE');
        $this->addSql('DROP TABLE IF EXISTS public.detalle_cliente_identificacion_facturacion CASCADE');
        $this->addSql('DROP TABLE IF EXISTS catalogo.identificacion_facturacion CASCADE');
        $this->addSql('DROP TABLE IF EXISTS catalogo.departamento CASCADE');
        $this->addSql('DROP TABLE IF EXISTS catalogo.cargo CASCADE');
        $this->addSql('DROP TABLE IF EXISTS public.ventas CASCADE');
        $this->addSql('DROP TABLE IF EXISTS catalogo.estado_ventas CASCADE');
        $this->addSql('DROP TABLE IF EXISTS public.detalle_zona_servicio_horario_cliente_facturacion CASCADE');
        $this->addSql('DROP TABLE IF EXISTS public.cliente_credito_periodico CASCADE');       
    }
}
