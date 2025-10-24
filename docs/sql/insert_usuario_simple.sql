-- =====================================================
-- INSERTS SIMPLES PARA USUARIO INICIAL
-- =====================================================
-- NOTA: Ejecutar estos scripts en orden
-- =====================================================

-- 1. INSERTAR SEDE PRINCIPAL
INSERT INTO sedes (codigo, nombre, direccion, email, es_principal, estado, creado_por)
VALUES ('SEDE001', 'Sede Principal - CAFED', 'Av. Principal 123, Lima', 'contacto@cafed.gob.pe', 1, 1, 'SISTEMA');

-- 2. INSERTAR ROL ADMINISTRADOR
-- (Reemplazar @sede_id con el ID real de la sede creada, ejemplo: 1)
INSERT INTO roles (sede_id, nombre, descripcion, nivel_acceso, estado, creado_por)
VALUES (1, 'Administrador', 'Acceso total al sistema', 4, 1, 'SISTEMA');

-- 3. INSERTAR USUARIO ADMINISTRADOR
-- Password hash de "admin123"
-- (Reemplazar @sede_id con el ID real de la sede, ejemplo: 1)
INSERT INTO usuarios (
    sede_id, 
    empleado_id, 
    username, 
    password_hash, 
    email, 
    primer_nombre, 
    apellido_paterno, 
    apellido_materno,
    intentos_fallidos,
    cuenta_bloqueada,
    debe_cambiar_password,
    estado, 
    creado_por
)
VALUES (
    1,                                                                      -- sede_id
    NULL,                                                                   -- empleado_id (NULL = admin)
    'admin',                                                                -- username
    '$2y$10$6FYaThUkA9gOKFxWGzC6ueUQ/706iQfmiiUHe2mEyh5zicf6Im6Za',        -- password: admin123
    'admin@cafed.gob.pe',                                                   -- email
    'Administrador',                                                        -- primer_nombre
    'Sistema',                                                              -- apellido_paterno
    'CAFED',                                                                -- apellido_materno
    0,                                                                      -- intentos_fallidos
    0,                                                                      -- cuenta_bloqueada
    0,                                                                      -- debe_cambiar_password
    1,                                                                      -- estado
    'SISTEMA'                                                               -- creado_por
);

-- 4. ASIGNAR ROL AL USUARIO
-- (Reemplazar los IDs con los valores reales: sede_id, usuario_id, rol_id)
INSERT INTO usuario_roles (sede_id, usuario_id, rol_id, fecha_asignacion, estado, asignado_por)
VALUES (1, 1, 1, GETDATE(), 1, 'SISTEMA');

-- =====================================================
-- CREDENCIALES DE ACCESO:
-- Usuario: admin
-- Password: admin123
-- =====================================================

-- PARA GENERAR UN NUEVO HASH DE CONTRASEÑA EN PHP:
-- <?php
-- echo password_hash('tu_password_aqui', PASSWORD_DEFAULT);
-- ?>


