<?php

declare(strict_types=1);

namespace MyProject\Component\Seeders;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241114193945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Validación de inserciones para tipo_servicios
        $this->abortIf(
            !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.tipo_servicios WHERE codigo_interno IN ('HPR-0001', 'HGE-0002', 'SDE-0003')")->fetchFirstColumn(),
            'Los registros para tipo_servicios ya existen en la base de datos.'
        );
        $this->addSql("INSERT INTO catalogo.tipo_servicios (nombre, descripcion, codigo_interno) VALUES
    ('Hospitalización Privada', 'Servicio de hospitalización privada', 'HPR-0001'),
    ('Hospitalización General', 'Servicio de hospitalización general', 'HGE-0002'),
    ('Sin Definir', 'Tipo de servicio sin definir', 'SDE-0003')");

        // Validación de inserciones para sistemas
        $this->abortIf(
            !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.sistemas WHERE codigo_interno IN ('SIS-0001', 'SIS-0002')")->fetchFirstColumn(),
            'Los registros para sistemas ya existen en la base de datos.'
        );
        $this->addSql("INSERT INTO catalogo.sistemas (nombre, descripcion, codigo_interno) VALUES
    ('Sistema de Comedor SISERVI', 'Sistema de servicios de comedor', 'SIS-0001'),
    ('Sistema de Dieta', 'Sistema de control de dietas', 'SIS-0002')");

        // Validación de inserciones para servicios_productos
        $this->abortIf(
            !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.servicios_productos WHERE codigo_interno IN ('DES-0001', 'ALM-0002', 'CEN-0003', 'REF-0004', 'MDES-0005', 'MALM-0006', 'MCEN-0007')")->fetchFirstColumn(),
            'Los registros para servicios_productos ya existen en la base de datos.'
        );
        $this->addSql("INSERT INTO catalogo.servicios_productos (nombre, descripcion, codigo_interno) VALUES
    ('Desayuno', 'Servicio de desayuno', 'DES-0001'),
    ('Almuerzo', 'Servicio de almuerzo', 'ALM-0002'),
    ('Cena', 'Servicio de cena', 'CEN-0003'),
    ('Refacción', 'Servicio de refacción', 'REF-0004'),
    ('Merienda de Desayuno', 'Servicio de merienda de desayuno', 'MDES-0005'),
    ('Merienda de Almuerzo', 'Servicio de merienda de almuerzo', 'MALM-0006'),
    ('Merienda de Cena', 'Servicio de merienda de cena', 'MCEN-0007')");

        // Validación de inserciones para servicios_productos_detalles
        $this->abortIf(
            !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.servicios_productos_detalles WHERE codigo_interno IN ('SPD-0001', 'SPD-0002', 'SPD-0003', 'SPD-0004', 'SPD-0005', 'SPD-0006', 'SPD-0011', 'SPD-0012', 'SPD-0013', 'SPD-0014', 'SPD-0015', 'SPD-0016')")->fetchFirstColumn(),
            'Los registros para servicios_productos_detalles ya existen en la base de datos.'
        );


        $this->addSql("INSERT INTO catalogo.servicios_productos_detalles (id_sistemas, id_tipo_servicios, id_servicios_productos, nombre, descripcion, codigo_interno) VALUES
   ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Comedor SISERVI'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Sin Definir'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Desayuno'), 'Desayuno', 'Desayuno SISERVI', 'SPD-0001'),
    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Comedor SISERVI'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Sin Definir'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Almuerzo'), 'Almuerzo', 'Almuerzo SISERVI', 'SPD-0002'),
    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Comedor SISERVI'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Sin Definir'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Cena'), 'Cena', 'Cena SISERVI', 'SPD-0003'),
    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Comedor SISERVI'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Sin Definir'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Refacción'), 'Refacción', 'Refacción SISERVI', 'SPD-0004'),
    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización General'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Desayuno'), 'Desayuno', 'Desayuno General', 'SPD-0005'),
	((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización Privada'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Desayuno'), 'Desayuno', 'Desayuno Privado', 'SPD-0011'),
    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización General'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Almuerzo'), 'Almuerzo', 'Almuerzo General', 'SPD-0012'),
	    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización Privada'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Almuerzo'), 'Almuerzo', 'Almuerzo Privado', 'SPD-0006'),
    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización General'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Cena'), 'Cena', 'Cena General', 'SPD-0007'),
	    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización Privada'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Cena'), 'Cena', 'Cena Privada', 'SPD-0013'),
    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización Privada'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Merienda de Desayuno'), 'Merienda de Desayuno', 'Merienda Desayuno Privada', 'SPD-0008'),
	    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización General'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Merienda de Desayuno'), 'Merienda de Desayuno', 'Merienda Desayuno General', 'SPD-0014'),
    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización General'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Merienda de Almuerzo'), 'Merienda de Almuerzo', 'Merienda Almuerzo General', 'SPD-0009'),
	    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización Privada'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Merienda de Almuerzo'), 'Merienda de Almuerzo', 'Merienda Almuerzo Privada', 'SPD-0015'),
    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización Privada'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Merienda de Cena'), 'Merienda de Cena', 'Merienda Cena Privada', 'SPD-0010'),
	    ((SELECT id FROM catalogo.sistemas WHERE nombre = 'Sistema de Dieta'), (SELECT id FROM catalogo.tipo_servicios WHERE nombre = 'Hospitalización General'), (SELECT id FROM catalogo.servicios_productos WHERE nombre = 'Merienda de Cena'), 'Merienda de Cena', 'Merienda Cena General', 'SPD-0016')");

        // Validación de inserciones para lista_catalogo y lista_catalogo_detalle
        $this->abortIf(
            !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.lista_catalogo WHERE codigo_interno = 'LCU-001'")->fetchFirstColumn(),
            'El registro para lista_catalogo ya existe en la base de datos.'
        );
        $this->addSql("INSERT INTO catalogo.lista_catalogo (descripcion, codigo_interno, fecha_creacion, estado) VALUES 
    ('Servicios de Productos Detalles', 'LCU-001', NOW(), true)");

        $this->abortIf(
            !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.lista_catalogo_detalle WHERE codigo_interno IN ('SPD-0001', 'SPD-0002', 'SPD-0003', 'SPD-0004')")->fetchFirstColumn(),
            'Los registros para lista_catalogo_detalle ya existen en la base de datos.'
        );
        $this->addSql("INSERT INTO catalogo.lista_catalogo_detalle (id_lista_catalogo, id_valor, codigo_interno, fecha_creacion, estado) VALUES
        (1, 1, 'SPD-0001', NOW(), true), (1, 2, 'SPD-0002', NOW(), true),
        (1, 3, 'SPD-0003', NOW(), true), 
        (1, 4, 'SPD-0004', NOW(), true), 
        (1, 5, 'SPD-0005', NOW(), true), 
        (1, 6, 'SPD-0006', NOW(), true), 
        (1, 7, 'SPD-0007', NOW(), true), 
        (1, 8, 'SPD-0008', NOW(), true), 
        (1, 9, 'SPD-0009', NOW(), true), 
        (1, 10, 'SPD-0010', NOW(), true);");

        // Validación de inserciones para departamento y cargo
        //     $this->abortIf(
        //         !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.departamento WHERE codigo_interno = 'CDP-0001'")->fetchFirstColumn(),
        //         'El registro para departamento ya existe en la base de datos.'
        //     );
        //     $this->addSql("INSERT INTO catalogo.departamento (nombre, descripcion, codigo_interno) VALUES 
        // ('Gerencia Tecnología', 'Departamento de tecnología y sistemas', 'CDP-0001')");

        //     $this->abortIf(
        //         !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.cargo WHERE codigo_interno = 'CRG-0001'")->fetchFirstColumn(),
        //         'El registro para cargo ya existe en la base de datos.'
        //     );
        //     $this->addSql("INSERT INTO catalogo.cargo (nombre, descripcion, codigo_interno) VALUES 
        // ('Administrador de Sitios Web y E-commerce', 'Responsable de administrar sitios web y plataformas de comercio electrónico', 'CRG-0001')");

        // Validación de inserciones para tipo_usuario
        $this->abortIf(
            !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.tipo_usuario WHERE codigo_interno IN ('CTU-001', 'CTU-002', 'CTU-003')")->fetchFirstColumn(),
            'Los registros para tipo_usuario ya existen en la base de datos.'
        );
        $this->addSql("INSERT INTO catalogo.tipo_usuario (nombre, descripcion, codigo_interno, fecha_creacion, estado) VALUES 
    ('Administrador', 'Usuario con permisos completos para gestionar el sistema', 'CTU-001', NOW(), TRUE),
    ('Facturador', 'Usuario con permisos para gestionar facturación', 'CTU-002', NOW(), TRUE),
    ('Estandar', 'Usuario con permisos limitados para visualizar información', 'CTU-003', NOW(), TRUE)");

        // Validación de inserciones para zona y horario
        $this->abortIf(
            !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.zona WHERE codigo_interno IN ('ZNS-001', 'ZNS-002')")->fetchFirstColumn(),
            'Los registros para zona ya existen en la base de datos.'
        );
        $this->addSql("INSERT INTO catalogo.zona (nombre, descripcion, codigo_interno) VALUES 
    ('Sitio Edificio Central - HMEADB', 'Zona asignada para los comedores del Edificio Central HMEADB', 'ZNS-001'),
    ('Sitios de Comedor 1A - HMEADB', 'Zona asignada para el comedor de la planta 1A HMEADB', 'ZNS-002')");

        $this->abortIf(
            !$this->connection->executeQuery("SELECT COUNT(*) FROM catalogo.horario WHERE codigo_interno IN ('HR-001', 'HR-002', 'HR-003', 'HR-004', 'HR-005')")->fetchFirstColumn(),
            'Los registros para horario ya existen en la base de datos.'
        );
        $this->addSql("INSERT INTO catalogo.horario (nombre, descripcion, codigo_interno, inicio, fin) VALUES 
    ('Desayuno', 'Horario para el servicio de desayuno', 'HR-001', '05:00:00', '07:00:00'),
    ('Almuerzo', 'Horario para el servicio de almuerzo', 'HR-002', '11:00:00', '14:30:00'),
    ('Cena', 'Horario para el servicio de cena', 'HR-003', '17:30:00', '20:00:00'),
    ('Refacción', 'Horario para el servicio de refacción', 'HR-004', '23:00:00', '23:59:59'),
    ('Merienda de Almuerzo', 'Horario para la merienda de almuerzo', 'HR-005', '15:00:00', '16:00:00')");

        // $this->abortIf(
        //     $this->connection->fetchOne("SELECT COUNT(1) FROM catalogo.identificacion_facturacion WHERE codigo_interno = 'IFT-001'") > 0,
        //     'El registro con el código interno IFT-001 ya existe en la tabla identificacion_facturacion.'
        // );
        // $this->addSql("
        //     INSERT INTO catalogo.identificacion_facturacion (nombre, descripcion, codigo_interno)
        //     VALUES ('Tarjeta de Aproximacion', 'RFID Reader Card', 'IFT-001')
        // ");

        // // Insertar datos en la tabla 'estado_ventas'
        // $this->abortIf(
        //     $this->connection->fetchOne("SELECT COUNT(1) FROM catalogo.estado_ventas WHERE codigo_interno = 'EDV-001'") > 0,
        //     'El registro con el código interno EDV-001 ya existe en la tabla estado_ventas.'
        // );
        // $this->addSql("
        //     INSERT INTO catalogo.estado_ventas (nombre, descripcion, codigo_interno)
        //     VALUES ('Estado Vendido', 'Transacción completada exitosamente', 'EDV-001')
        // ");

        // $this->abortIf(
        //     $this->connection->fetchOne("SELECT COUNT(1) FROM catalogo.estado_ventas WHERE codigo_interno = 'EDV-002'") > 0,
        //     'El registro con el código interno EDV-002 ya existe en la tabla estado_ventas.'
        // );
        // $this->addSql("
        //     INSERT INTO catalogo.estado_ventas (nombre, descripcion, codigo_interno)
        //     VALUES ('Estado Pendiente', 'Transacción en proceso o espera de aprobación', 'EDV-002')
        // ");

        // $this->abortIf(
        //     $this->connection->fetchOne("SELECT COUNT(1) FROM catalogo.estado_ventas WHERE codigo_interno = 'EDV-003'") > 0,
        //     'El registro con el código interno EDV-003 ya existe en la tabla estado_ventas.'
        // );
        // $this->addSql("
        //     INSERT INTO catalogo.estado_ventas (nombre, descripcion, codigo_interno)
        //     VALUES ('Estado Anulado', 'Transacción cancelada o inválida', 'EDV-003')
        // ");

        // Insertar usuarios en 'seguridad.usuarios'
        $password = '$2y$10$RAK5xnWYNhDDH40pkdWyVOGoxSjZ67.I1dybkXlBGStFBZgeHNbeq';
        $this->abortIf(
            $this->connection->fetchOne("SELECT COUNT(1) FROM seguridad.usuarios WHERE id = 2") > 0,
            'El usuario con ID 2 ya existe en la tabla usuarios.'
        );
        $this->addSql("
            INSERT INTO seguridad.usuarios (id, nombre_usuario, contrasenia, nombres, apellidos, correo, fecha_creacion, fecha_modificacion, estado, isadmin, id_tipo_usuario_permiso)
            VALUES (2, 'hola1235', '$password', 'Cesar Adan', 'Cuadra Irías', 'cesar.cuadra25@gmail.com', '2024-09-11 09:54:24', '2024-09-11 11:05:14', true, true, 3)
        ");

        $this->abortIf(
            $this->connection->fetchOne("SELECT COUNT(1) FROM seguridad.usuarios WHERE id = 1") > 0,
            'El usuario con ID 1 ya existe en la tabla usuarios.'
        );
        $this->addSql("
            INSERT INTO seguridad.usuarios (id, nombre_usuario, contrasenia, nombres, apellidos, correo, fecha_creacion, fecha_modificacion, estado, isadmin, id_tipo_usuario_permiso)
            VALUES (1, 'haki', '$password' , 'Cesar Adan', 'Cuadra Irías', 'cesar.cuadra450@gmail.com', '2024-09-11 10:22:03', NULL, false, false, 3)
        ");

        // Insertar permisos en 'seguridad.permisos'
        $this->abortIf(
            $this->connection->fetchOne("SELECT COUNT(1) FROM seguridad.permisos WHERE codigo_interno = 'PU-00001'") > 0,
            'El permiso con código interno PU-00001 ya existe en la tabla permisos.'
        );
        $this->addSql("
            INSERT INTO seguridad.permisos (nombre, accion, descripcion, codigo_interno)
            VALUES ('Acceso a todos los proyectos', 'project_all', 'Permiso para acceder a todos los proyectos', 'PU-00001')
        ");

        $this->abortIf(
            $this->connection->fetchOne("SELECT COUNT(1) FROM seguridad.permisos WHERE codigo_interno = 'PU-00002'") > 0,
            'El permiso con código interno PU-00002 ya existe en la tabla permisos.'
        );
        $this->addSql("
            INSERT INTO seguridad.permisos (nombre, accion, descripcion, codigo_interno)
            VALUES ('Listar todos los tipos de servicios', 'tipo_servicios.list_all', 'Permiso para listar todos los tipos de servicios', 'PU-00002')
        ");

        $this->abortIf(
            $this->connection->fetchOne("SELECT COUNT(1) FROM seguridad.permisos WHERE codigo_interno = 'PU-00003'") > 0,
            'El permiso con código interno PU-00003 ya existe en la tabla permisos.'
        );
        $this->addSql("
            INSERT INTO seguridad.permisos (nombre, accion, descripcion, codigo_interno)
            VALUES ('Acceder al Panel Administrador', 'dashboard_view', 'Permiso para acceder al panel Administrador', 'PU-00003')
        ");

        // Insertar asignaciones de permisos en 'seguridad.tipo_usuario_permisos'
        $this->abortIf(
            $this->connection->fetchOne("SELECT COUNT(1) FROM seguridad.tipo_usuario_permisos WHERE usuario_id = 1 AND tipo_usuario_id = 1 AND permiso_id = (SELECT id FROM seguridad.permisos WHERE codigo_interno = 'PU-00001')") > 0,
            'La asignación de permiso para el usuario 1 con tipo 1 y código PU-00001 ya existe.'
        );
        $this->addSql("
            INSERT INTO seguridad.tipo_usuario_permisos (usuario_id, tipo_usuario_id, permiso_id, descripcion, codigo_interno)
            SELECT 1, 1, (SELECT id FROM seguridad.permisos WHERE codigo_interno = 'PU-00001'), 'Permiso completo para proyectos', 'TUP-00001'
        ");

        $this->abortIf(
            $this->connection->fetchOne("SELECT COUNT(1) FROM seguridad.tipo_usuario_permisos WHERE usuario_id = 2 AND tipo_usuario_id = 2 AND permiso_id = (SELECT id FROM seguridad.permisos WHERE codigo_interno = 'PU-00002')") > 0,
            'La asignación de permiso para el usuario 2 con tipo 2 y código PU-00002 ya existe.'
        );
        $this->addSql("
            INSERT INTO seguridad.tipo_usuario_permisos (usuario_id, tipo_usuario_id, permiso_id, descripcion, codigo_interno)
            SELECT 2, 2, (SELECT id FROM seguridad.permisos WHERE codigo_interno = 'PU-00002'), 'Permiso para listar todos los tipos de servicios', 'TUP-00002'
        ");

        // Insertar zonas de usuario en 'seguridad.zona_usuario'
        $this->abortIf(
            $this->connection->fetchOne("SELECT COUNT(1) FROM seguridad.zona_usuario WHERE id_zona = 1 AND id_usuario = 1") > 0,
            'La zona para el usuario 1 con ID 1 ya está asignada.'
        );
        $this->addSql("
            INSERT INTO seguridad.zona_usuario(id_zona, id_usuario, codigo_interno, estado)
            VALUES (1, 1, 'ZU-001', true)
        ");
    }

    public function down(Schema $schema): void
    {
        // Rollback de las inserciones (si es necesario)
        // Eliminar registros de detalle_zona_servicio_horario
        $this->addSql("DELETE FROM catalogo.detalle_zona_servicio_horario");
        $this->addSql("ALTER SEQUENCE catalogo.detalle_zona_servicio_horario_id_seq RESTART WITH 1");

        // Eliminar registros de servicios_productos_detalles
        $this->addSql("DELETE FROM catalogo.servicios_productos_detalles");
        $this->addSql("ALTER SEQUENCE catalogo.servicios_productos_detalles_id_seq RESTART WITH 1");

        // Eliminar registros de lista_catalogo
        $this->addSql("DELETE FROM catalogo.lista_catalogo");
        $this->addSql("ALTER SEQUENCE catalogo.lista_catalogo_id_seq RESTART WITH 1");

        // Eliminar registros de servicios_productos
        $this->addSql("DELETE FROM catalogo.servicios_productos");
        $this->addSql("ALTER SEQUENCE catalogo.servicios_productos_id_seq RESTART WITH 1");

        // Eliminar registros de sistemas
        $this->addSql("DELETE FROM catalogo.sistemas");
        $this->addSql("ALTER SEQUENCE catalogo.sistemas_id_seq RESTART WITH 1");

        // Eliminar registros de tipo_servicios
        $this->addSql("DELETE FROM catalogo.tipo_servicios");
        $this->addSql("ALTER SEQUENCE catalogo.tipo_servicios_id_seq RESTART WITH 1");

        // Eliminar registros de tipo_usuario
        $this->addSql("DELETE FROM catalogo.tipo_usuario");
        $this->addSql("ALTER SEQUENCE catalogo.tipo_usuario_id_seq RESTART WITH 1");

        // Eliminar registros de zona
        $this->addSql("DELETE FROM catalogo.zona");
        $this->addSql("ALTER SEQUENCE catalogo.zona_id_seq RESTART WITH 1");

        // Eliminar registros de zona
        $this->addSql("DELETE FROM catalogo.horario");
        $this->addSql("ALTER SEQUENCE catalogo.horario_id_seq RESTART WITH 1");

        // Eliminar registros de seguridad.tipo_usuario_permisos
        $this->addSql("DELETE FROM seguridad.tipo_usuario_permisos");
        $this->addSql("ALTER SEQUENCE seguridad.tipo_usuario_permisos_id_seq RESTART WITH 1");

        // Eliminar registros de seguridad.zona_usuario
        $this->addSql("DELETE FROM seguridad.zona_usuario");
        $this->addSql("ALTER SEQUENCE seguridad.zona_usuario_id_seq RESTART WITH 1");

        // Eliminar registros de seguridad.usuarios
        $this->addSql("DELETE FROM seguridad.usuarios");
        $this->addSql("ALTER SEQUENCE seguridad.usuarios_id_seq RESTART WITH 1");

        // Eliminar registros de seguridad.permisos
        $this->addSql("DELETE FROM seguridad.permisos");
        $this->addSql("ALTER SEQUENCE seguridad.permisos_id_seq RESTART WITH 1");
    }
}
