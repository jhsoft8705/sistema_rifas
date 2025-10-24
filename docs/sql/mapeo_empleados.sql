-- =====================================================
-- DATOS DE PRUEBA - EMPLEADO Y MAPEO BIOMÉTRICO
-- Base de datos: db_control_asistencia_testing
-- =====================================================
-- Inserts limpios usando llaves foráneas

USE db_control_asistencia_testing;
GO

-- =====================================================
-- 1. INSERTAR EMPLEADO DE PRUEBA
-- =====================================================
INSERT INTO empleados (
    sede_id,
    tipo_documento_id,
    nro_documento,
    nombre,
    apellido_paterno,
    apellido_materno,
    email,
    telefono,
    fecha_ingreso,
    cargo_id,
    estado,
    creado_por
)
VALUES (
    1,                      -- sede_id (FK a sedes)
    1,                      -- tipo_documento_id (FK a tipos_documento, 1 = DNI)
    '73569079',             -- nro_documento
    'JHON',                 -- nombre
    'VILLA',                -- apellido_paterno
    'FLORES',               -- apellido_materno
    'jvilla@cafed.com',   -- email
    '999888777',            -- telefono
    '2025-01-01',           -- fecha_ingreso
    1,                      -- cargo_id (FK a cargos)
    1,                      -- estado (1 = Activo)
    'SYSTEM'                -- creado_por
);
GO

-- =====================================================
-- 2. MAPEAR EMPLEADO CON BIOMÉTRICO
-- =====================================================
 

INSERT INTO empleado_biometrico (
    sede_id,
    empleado_id,
    biometric_user_id,
    device_id,
    activo,
    creado_por
)
VALUES (
    1,                      -- sede_id
    1,                      -- empleado_id
    '100528',               -- biometric_user_id (emp_code del biométrico)
    'CKPG214860032',        -- device_id (terminal del reloj)
    1,                      -- activo
    'SYSTEM'                -- creado_por
);
GO
