
-- =====================================================
-- 8. VISTAS ÚTILES (ACTUALIZADAS)
-- =====================================================

-- Vista de empleados con datos biométricos
CREATE VIEW vista_empleados_biometricos AS
SELECT 
    e.id,
    e.nro_documento,
    e.nombre,
    e.apellido_paterno,
    e.apellido_materno,
    e.email,
    g.nombre as gerencia,
    d.nombre_departamento as departamento,
    c.nombre_cargo as cargo,
    t.nombre_turno as turno,
    eb.biometric_user_id,
    eb.device_id,
    eb.activo as biometrico_activo,
    u.username,
    u.estado as usuario_activo,
    -- Nombre completo del usuario (desde empleado o usuario administrativo)
    CASE 
        WHEN u.empleado_id IS NOT NULL THEN e.nombre + ' ' + e.apellido_paterno + ISNULL(' ' + e.apellido_materno, '')
        ELSE u.primer_nombre + ' ' + u.apellido_paterno + ISNULL(' ' + u.apellido_materno, '')
    END as nombre_completo_usuario
FROM empleados e
LEFT JOIN gerencias g ON e.gerencia_id = g.id
LEFT JOIN departamentos d ON e.departamento_id = d.id
LEFT JOIN cargos c ON e.cargo_id = c.id
LEFT JOIN turnos_laborables t ON e.turno_id = t.id
LEFT JOIN empleado_biometrico eb ON e.id = eb.empleado_id
LEFT JOIN usuarios u ON e.id = u.empleado_id
WHERE e.estado = 1;

-- Vista de asistencias diarias
CREATE VIEW vista_asistencias_diarias AS
SELECT 
    m.empleado_id,
    e.nro_documento,
    e.nombre + ' ' + e.apellido_paterno as nombre_completo,
    g.nombre as gerencia,
    m.fecha_marcacion,
    m.hora_entrada,
    m.hora_salida,
    m.estado_asistencia,
    m.minutos_tardanza,
    m.fuente,
    m.creado_por,
    m.modificado_por,
    -- Nombres completos de usuarios (desde vista_usuarios_roles)
    u_creado.nombre_completo as creado_por_nombre,
    u_modificado.nombre_completo as modificado_por_nombre,
    u_creado.username as creado_por_username,
    u_modificado.username as modificado_por_username
FROM marcaciones m
INNER JOIN empleados e ON m.empleado_id = e.id
LEFT JOIN gerencias g ON e.gerencia_id = g.id
LEFT JOIN vista_usuarios_roles u_creado ON m.creado_por = u_creado.id
LEFT JOIN vista_usuarios_roles u_modificado ON m.modificado_por = u_modificado.id
WHERE e.estado = 1;

-- Vista de logs sin mapeo
CREATE VIEW vista_logs_sin_mapeo AS
SELECT 
    blr.id,
    blr.biometric_user_id,
    blr.device_id,
    blr.evento_at,
    blr.raw_data,
    blr.fecha_creacion
FROM biometric_log_raw blr
LEFT JOIN empleado_biometrico eb ON blr.biometric_user_id = eb.biometric_user_id
WHERE eb.id IS NULL AND blr.procesado = 0;

-- Vista de usuarios con roles
CREATE VIEW vista_usuarios_roles AS
SELECT 
    u.id,
    u.username,
    u.email,
    u.empleado_id,
    -- Nombre completo: desde empleado si existe, sino desde usuario administrativo
    CASE 
        WHEN u.empleado_id IS NOT NULL THEN e.nombre + ' ' + e.apellido_paterno + ISNULL(' ' + e.apellido_materno, '')
        ELSE u.primer_nombre + ' ' + u.apellido_paterno + ISNULL(' ' + u.apellido_materno, '')
    END as nombre_completo,
    CASE 
        WHEN u.empleado_id IS NOT NULL THEN 'EMPLEADO'
        ELSE 'ADMINISTRATIVO'
    END as tipo_usuario,
    u.estado,
    u.ultimo_acceso,
    STRING_AGG(r.nombre, ', ') as roles,
    STRING_AGG(r.descripcion, '; ') as descripcion_roles
FROM usuarios u
LEFT JOIN empleados e ON u.empleado_id = e.id
LEFT JOIN usuario_roles ur ON u.id = ur.usuario_id AND ur.estado = 1
LEFT JOIN roles r ON ur.rol_id = r.id AND r.estado = 1
GROUP BY u.id, u.username, u.email, u.empleado_id, u.primer_nombre, u.apellido_paterno, u.apellido_materno, 
         e.nombre, e.apellido_paterno, e.apellido_materno, u.estado, u.ultimo_acceso;

-- Vista de permisos por usuario
CREATE VIEW vista_permisos_usuario AS
SELECT 
    u.id as usuario_id,
    u.username,
    p.id as permiso_id,
    p.nombre as permiso,
    p.modulo,
    p.accion,
    'DIRECTO' as tipo_asignacion
FROM usuarios u
INNER JOIN usuario_permisos up ON u.id = up.usuario_id AND up.estado = 1
INNER JOIN permisos p ON up.permiso_id = p.id AND p.estado = 1

UNION

SELECT 
    u.id as usuario_id,
    u.username,
    p.id as permiso_id,
    p.nombre as permiso,
    p.modulo,
    p.accion,
    'POR_ROL' as tipo_asignacion
FROM usuarios u
INNER JOIN usuario_roles ur ON u.id = ur.usuario_id AND ur.estado = 1
INNER JOIN rol_permisos rp ON ur.rol_id = rp.rol_id AND rp.estado = 1
INNER JOIN permisos p ON rp.permiso_id = p.id AND p.estado = 1;

-- Vista de direcciones de empleados con UBIGEO
CREATE VIEW vista_direcciones_empleado AS
SELECT 
    de.id,
    de.empleado_id,
    e.nro_documento,
    e.nombre + ' ' + e.apellido_paterno as nombre_completo,
    de.tipo_direccion,
    de.direccion_completa,
    de.ubigeo_id,
    u.departamento,
    u.provincia,
    u.distrito,
    de.referencia,
    de.es_principal,
    de.estado,
    de.fecha_creacion,
    de.fecha_modificacion,
    -- Dirección completa con UBIGEO
    de.direccion_completa + 
    CASE 
        WHEN u.departamento IS NOT NULL THEN 
            ', ' + u.distrito + ', ' + u.provincia + ', ' + u.departamento
        ELSE ''
    END as direccion_completa_con_ubigeo
FROM direcciones_empleado de
INNER JOIN empleados e ON de.empleado_id = e.id
LEFT JOIN ubigeo u ON de.ubigeo_id = u.codigo
WHERE de.estado = 1;

-- Vista completa de empleados con datos de planilla
CREATE VIEW vista_empleados_planilla AS
SELECT 
    e.id,
    e.nro_documento,
    e.nombre + ' ' + e.apellido_paterno + ISNULL(' ' + e.apellido_materno, '') as nombre_completo,
    e.email,
    e.telefono,
    e.fecha_ingreso,
    e.fecha_cese,
    e.cuspp,
    e.airhsp,
    e.codigo_reloj,
    e.numero_cuenta,
    e.numero_cci,
    e.observaciones,
    
    -- Datos organizacionales
    s.nombre as sede,
    g.nombre as gerencia,
    d.nombre_departamento as departamento,
    c.nombre_cargo as cargo,
    t.nombre_turno as turno,
    
    -- Datos de planilla
    sp.nombre as sistema_pension,
    sp.codigo as codigo_pension,
    rl.nombre as regimen_laboral,
    rl.base_legal,
    rl.duracion_meses,
    rl.es_indefinido,
    rl.es_tiempo_parcial,
    rl.es_intermitente,
    tt.nombre as tipo_trabajador,
    nr.nombre as nivel_remunerativo,
    
    -- Datos bancarios
    b.nombre as banco,
    b.codigo as codigo_banco,
    tcb.descripcion as tipo_cuenta,
    
    -- Datos personales
    ec.descripcion as estado_civil,
    td.descripcion as tipo_documento,
    e.sexo,
    e.fecha_nacimiento,
    
    -- Control
    e.estado,
    e.fecha_creacion,
    e.fecha_modificacion
FROM empleados e
LEFT JOIN sedes s ON e.sede_id = s.id
LEFT JOIN gerencias g ON e.gerencia_id = g.id
LEFT JOIN departamentos d ON e.departamento_id = d.id
LEFT JOIN cargos c ON e.cargo_id = c.id
LEFT JOIN turnos_laborables t ON e.turno_id = t.id
LEFT JOIN sistemas_pension sp ON e.sistema_pension_id = sp.id
LEFT JOIN regimenes_laborales rl ON e.regimen_laboral_id = rl.id
LEFT JOIN tipos_trabajador tt ON e.tipo_trabajador_id = tt.id
LEFT JOIN niveles_remunerativos nr ON e.nivel_remunerativo_id = nr.id
LEFT JOIN bancos b ON e.banco_id = b.id
LEFT JOIN tipos_cuenta_bancaria tcb ON e.banco_id = tcb.id
LEFT JOIN estado_civil ec ON e.estado_civil_id = ec.id
LEFT JOIN tipos_documento td ON e.tipo_documento_id = td.id
WHERE e.estado = 1;

-- Vista genérica de auditoría (para usar en cualquier tabla)
CREATE VIEW vista_auditoria_generica AS
SELECT 
    'empleados' as tabla,
    e.id as registro_id,
    e.nombre + ' ' + e.apellido_paterno as registro_nombre,
    e.fecha_creacion,
    e.fecha_modificacion,
    e.creado_por,
    e.modificado_por,
    u_creado.nombre_completo as creado_por_nombre,
    u_modificado.nombre_completo as modificado_por_nombre,
    u_creado.username as creado_por_username,
    u_modificado.username as modificado_por_username
FROM empleados e
LEFT JOIN vista_usuarios_roles u_creado ON e.creado_por = u_creado.id
LEFT JOIN vista_usuarios_roles u_modificado ON e.modificado_por = u_modificado.id

UNION ALL

SELECT 
    'marcaciones' as tabla,
    m.id as registro_id,
    e.nombre + ' ' + e.apellido_paterno + ' - ' + CAST(m.fecha_marcacion AS VARCHAR) as registro_nombre,
    m.fecha_creacion,
    m.fecha_modificacion,
    m.creado_por,
    m.modificado_por,
    u_creado.nombre_completo as creado_por_nombre,
    u_modificado.nombre_completo as modificado_por_nombre,
    u_creado.username as creado_por_username,
    u_modificado.username as modificado_por_username
FROM marcaciones m
INNER JOIN empleados e ON m.empleado_id = e.id
LEFT JOIN vista_usuarios_roles u_creado ON m.creado_por = u_creado.id
LEFT JOIN vista_usuarios_roles u_modificado ON m.modificado_por = u_modificado.id

UNION ALL

SELECT 
    'planillas' as tabla,
    p.id as registro_id,
    p.nombre_planilla as registro_nombre,
    p.fecha_creacion,
    p.fecha_modificacion,
    p.creado_por,
    p.modificado_por,
    u_creado.nombre_completo as creado_por_nombre,
    u_modificado.nombre_completo as modificado_por_nombre,
    u_creado.username as creado_por_username,
    u_modificado.username as modificado_por_username
FROM planillas p
LEFT JOIN vista_usuarios_roles u_creado ON p.creado_por = u_creado.id
LEFT JOIN vista_usuarios_roles u_modificado ON p.modificado_por = u_modificado.id;

-- =====================================================
-- 9. DATOS INICIALES
-- =====================================================

-- Insertar roles básicos
INSERT INTO roles (nombre, descripcion, nivel_acceso, creado_por) VALUES
('ADMINISTRADOR', 'Administrador del sistema con acceso completo', 4, NULL),
('RRHH', 'Recursos Humanos - Gestión de empleados y planillas', 3, NULL),
('SUPERVISOR', 'Supervisor - Gestión de asistencias y reportes', 2, NULL),
('EMPLEADO', 'Empleado - Consulta de sus propios datos', 1, NULL);

-- Insertar permisos básicos
INSERT INTO permisos (nombre, descripcion, modulo, accion, creado_por) VALUES
-- Empleados
('EMPLEADOS_CREAR', 'Crear nuevos empleados', 'EMPLEADOS', 'CREAR', NULL),
('EMPLEADOS_LEER', 'Ver información de empleados', 'EMPLEADOS', 'LEER', NULL),
('EMPLEADOS_ACTUALIZAR', 'Modificar datos de empleados', 'EMPLEADOS', 'ACTUALIZAR', NULL),
('EMPLEADOS_ELIMINAR', 'Eliminar empleados', 'EMPLEADOS', 'ELIMINAR', NULL),

-- Asistencia
('ASISTENCIA_LEER', 'Ver asistencias y marcaciones', 'ASISTENCIA', 'LEER', NULL),
('ASISTENCIA_ACTUALIZAR', 'Modificar asistencias', 'ASISTENCIA', 'ACTUALIZAR', NULL),
('ASISTENCIA_AJUSTAR', 'Realizar ajustes manuales', 'ASISTENCIA', 'AJUSTAR', NULL),
('ASISTENCIA_APROBAR', 'Aprobar ajustes de asistencia', 'ASISTENCIA', 'APROBAR', NULL),

-- Planilla
('PLANILLA_CREAR', 'Crear planillas', 'PLANILLA', 'CREAR', NULL),
('PLANILLA_LEER', 'Ver planillas', 'PLANILLA', 'LEER', NULL),
('PLANILLA_ACTUALIZAR', 'Modificar planillas', 'PLANILLA', 'ACTUALIZAR', NULL),
('PLANILLA_PROCESSAR', 'Procesar planillas', 'PLANILLA', 'PROCESSAR', NULL),
('PLANILLA_EXPORTAR', 'Exportar planillas', 'PLANILLA', 'EXPORTAR', NULL),

-- Reportes
('REPORTES_LEER', 'Ver reportes', 'REPORTES', 'LEER', NULL),
('REPORTES_EXPORTAR', 'Exportar reportes', 'REPORTES', 'EXPORTAR', NULL),

-- Configuración
('CONFIG_CREAR', 'Crear configuraciones', 'CONFIGURACION', 'CREAR', NULL),
('CONFIG_LEER', 'Ver configuraciones', 'CONFIGURACION', 'LEER', NULL),
('CONFIG_ACTUALIZAR', 'Modificar configuraciones', 'CONFIGURACION', 'ACTUALIZAR', NULL),
('CONFIG_ELIMINAR', 'Eliminar configuraciones', 'CONFIGURACION', 'ELIMINAR', NULL),

-- Usuarios
('USUARIOS_CREAR', 'Crear usuarios', 'USUARIOS', 'CREAR', NULL),
('USUARIOS_LEER', 'Ver usuarios', 'USUARIOS', 'LEER', NULL),
('USUARIOS_ACTUALIZAR', 'Modificar usuarios', 'USUARIOS', 'ACTUALIZAR', NULL),
('USUARIOS_ELIMINAR', 'Eliminar usuarios', 'USUARIOS', 'ELIMINAR', NULL);

-- Asignar permisos a roles
-- Administrador: todos los permisos
INSERT INTO rol_permisos (rol_id, permiso_id, asignado_por)
SELECT 1, id, NULL FROM permisos;

-- RRHH: permisos de empleados, planilla y reportes
INSERT INTO rol_permisos (rol_id, permiso_id, asignado_por)
SELECT 2, id, NULL FROM permisos 
WHERE modulo IN ('EMPLEADOS', 'PLANILLA', 'REPORTES') OR nombre LIKE '%LEER%';

-- Supervisor: permisos de asistencia y reportes
INSERT INTO rol_permisos (rol_id, permiso_id, asignado_por)
SELECT 3, id, NULL FROM permisos 
WHERE modulo IN ('ASISTENCIA', 'REPORTES') OR (modulo = 'EMPLEADOS' AND accion = 'LEER');

-- Empleado: solo lectura de sus propios datos
INSERT INTO rol_permisos (rol_id, permiso_id, asignado_por)
SELECT 4, id, NULL FROM permisos 
WHERE nombre IN ('ASISTENCIA_LEER', 'PLANILLA_LEER', 'REPORTES_LEER');

-- Insertar datos de UBIGEO (ejemplos)
INSERT INTO ubigeo (codigo, departamento, provincia, distrito) VALUES
('080101', 'CUSCO', 'ANTA', 'ZURITE'),
('150101', 'LIMA', 'CAÑETE', 'ZUÑIGA'),
('240101', 'TUMBES', 'CONTRALMIRANTE VILLAR', 'ZORRITOS'),
('210101', 'PUNO', 'CHUCUITO', 'ZEPITA'),
('240201', 'TUMBES', 'ZARUMILLA', 'ZARUMILLA'),
('220101', 'SAN MARTIN', 'LAMAS', 'ZAPATERO'),
('100101', 'HUANUCO', 'PUERTO INCA', 'YUYAPICHIS'),
('250101', 'UCAYALI', 'ATALAYA', 'YURUA'),
('160101', 'LORETO', 'ALTO AMAZONAS', 'YURIMAGUAS'),
('220201', 'SAN MARTIN', 'RIOJA', 'YURACYACU');

-- Insertar datos de estado civil
INSERT INTO estado_civil (descripcion) VALUES
('Soltero(a)'),
('Casado(a)'),
('Divorciado(a)'),
('Viudo(a)'),
('Conviviente');

-- Insertar tipos de documento
INSERT INTO tipos_documento (descripcion) VALUES
('DNI'),
('Carnet de Extranjería'),
('Pasaporte'),
('Cédula de Identidad');

-- Insertar grados de instrucción
INSERT INTO grados_instruccion (descripcion) VALUES
('Sin Instrucción'),
('Educación Inicial'),
('Educación Primaria'),
('Educación Secundaria'),
('Educación Superior No Universitaria'),
('Educación Superior Universitaria'),
('Maestría'),
('Doctorado');

-- Insertar profesiones (ejemplos)
INSERT INTO profesiones (descripcion) VALUES
('Administrador'),
('Contador'),
('Ingeniero de Sistemas'),
('Abogado'),
('Médico'),
('Enfermero(a)'),
('Técnico'),
('Secretario(a)'),
('Auxiliar'),
('Operario');

-- Insertar sistemas de pensiones
INSERT INTO sistemas_pension (codigo, nombre, descripcion, porcentaje_empleado, porcentaje_empleador) VALUES
('ONP', 'Oficina de Normalización Previsional', 'Sistema Nacional de Pensiones', 13.00, 0.00),
('AFP', 'Administradora de Fondos de Pensiones', 'Sistema Privado de Pensiones', 10.00, 0.00),
('SPP', 'Sistema Privado de Pensiones', 'Sistema Privado de Pensiones', 10.00, 0.00);

-- Insertar regímenes laborales (incluye condiciones)
INSERT INTO regimenes_laborales (codigo, nombre, descripcion, base_legal, duracion_meses, es_indefinido, es_tiempo_parcial, es_intermitente) VALUES
-- D.LEG. 728
('INDEFINIDO_728', 'A PLAZO INDETERMINADO - D.LEG. 728', 'Contrato a plazo indeterminado', 'D.LEG. 728', NULL, 1, 0, 0),
('PARCIAL_728', 'A TIEMPO PARCIAL - D.LEG. 728', 'Contrato a tiempo parcial', 'D.LEG. 728', NULL, 0, 1, 0),
('INICIO_ACTIVIDAD_728', 'POR INICIO O INCREMENTO DE ACTIVIDAD - D.LEG. 728', 'Contrato por inicio de actividad', 'D.LEG. 728', 12, 0, 0, 0),
('NECESIDADES_MERCADO_728', 'POR NECESIDADES DEL MERCADO - D.LEG. 728', 'Contrato por necesidades del mercado', 'D.LEG. 728', 12, 0, 0, 0),
('RECONVERSION_728', 'POR RECONVERSIÓN EMPRESARIAL - D.LEG. 728', 'Contrato por reconversión empresarial', 'D.LEG. 728', 12, 0, 0, 0),
('OCASIONAL_728', 'OCASIONAL - D.LEG. 728', 'Contrato ocasional', 'D.LEG. 728', NULL, 0, 0, 1),
('SUPLENCIA_728', 'DE SUPLENCIA - D.LEG. 728', 'Contrato de suplencia', 'D.LEG. 728', 6, 0, 0, 0),
('EMERGENCIA_728', 'DE EMERGENCIA - D.LEG. 728', 'Contrato de emergencia', 'D.LEG. 728', 3, 0, 0, 0),
('OBRA_SERVICIO_728', 'PARA OBRA DETERMINADA O SERVICIO ESPECÍFICO - D.LEG. 728', 'Contrato para obra determinada', 'D.LEG. 728', 24, 0, 0, 0),
('INTERMITENTE_728', 'INTERMITENTE - D.LEG. 728', 'Contrato intermitente', 'D.LEG. 728', NULL, 0, 0, 1),
-- CAS
('CAS_1057', 'D. LEG.1057 - CAS', 'Contrato Administrativo de Servicios', 'D.LEG. 1057', 12, 0, 0, 0),
-- Público
('PUBLICO_LEY30057', 'RÉGIMEN LABORAL PÚBLICO', 'Régimen Laboral del Sector Público', 'Ley 30057', NULL, 1, 0, 0);

-- Insertar tipos de trabajador
INSERT INTO tipos_trabajador (codigo, nombre, descripcion, base_legal, es_funcionario, es_contratado, es_empleado, es_cas) VALUES
('FUNCIONARIO_728', 'D. LEG.728 - FUNCIONARIO', 'Funcionario bajo D.LEG. 728', 'D.LEG. 728', 1, 0, 0, 0),
('CAS_1057', 'D. LEG.1057 - CAS', 'Contratado bajo régimen CAS', 'D.LEG. 1057', 0, 0, 0, 1),
('CONTRATADO', 'CONTRATADO', 'Trabajador contratado', 'D.LEG. 728', 0, 1, 0, 0),
('EMPLEADO', 'EMPLEADO', 'Empleado de la empresa', 'D.LEG. 728', 0, 0, 1, 0);

-- Insertar niveles remunerativos
INSERT INTO niveles_remunerativos (codigo, nombre, descripcion, es_contrato, es_empleado, es_obrero, es_funcionario) VALUES
('CONTRATO', 'CONTRATO', 'Nivel de contrato', 1, 0, 0, 0),
('EMPLEADO', 'EMPLEADO', 'Nivel de empleado', 0, 1, 0, 0),
('OBRERO', 'OBRERO', 'Nivel de obrero', 0, 0, 1, 0),
('FUNCIONARIO', 'FUNCIONARIO', 'Nivel de funcionario', 0, 0, 0, 1),
('DIRECTIVO', 'DIRECTIVO', 'Nivel directivo', 0, 1, 0, 0),
('EJECUTIVO', 'EJECUTIVO', 'Nivel ejecutivo', 0, 1, 0, 0);

-- Insertar bancos principales del Perú
INSERT INTO bancos (codigo, nombre, descripcion, codigo_swift) VALUES
('002', 'Banco de Crédito del Perú', 'BCP', 'BCPLPEPL'),
('003', 'Banco Interbank', 'Interbank', 'BINPPEPL'),
('011', 'BBVA Continental', 'BBVA', 'BCONPEPL'),
('014', 'Scotiabank Perú', 'Scotiabank', 'NOSCPEPL'),
('016', 'Banco de la Nación', 'Banco de la Nación', 'BNACPEPL'),
('020', 'Banco Pichincha', 'Pichincha', 'PICHPE1L'),
('021', 'Banco GNB Perú', 'GNB', 'GNBPPEPL'),
('022', 'Banco Falabella', 'Falabella', 'FALBPE1L'),
('023', 'Banco Ripley', 'Ripley', 'RIPLPE1L'),
('024', 'Banco Azteca', 'Azteca', 'AZTEPE1L');

-- Insertar tipos de cuenta bancaria
INSERT INTO tipos_cuenta_bancaria (codigo, descripcion) VALUES
('AHO', 'Ahorros'),
('COR', 'Corriente'),
('SUE', 'Sueldo'),
('PLA', 'Plazo Fijo');

-- Insertar sedes (ejemplo)
INSERT INTO sedes (codigo, nombre, direccion, ubigeo_id, telefono, email, es_principal) VALUES
('SEDE_001', 'Sede Principal', 'Av. Principal 123', '150101', '01-234-5678', 'sede.principal@empresa.com', 1),
('SEDE_002', 'Sede Norte', 'Av. Norte 456', '150102', '01-234-5679', 'sede.norte@empresa.com', 0),
('SEDE_003', 'Sede Sur', 'Av. Sur 789', '150103', '01-234-5680', 'sede.sur@empresa.com', 0);

-- =====================================================
-- 10. TRIGGERS DE AUDITORÍA
-- =====================================================

-- Trigger para auditoría general
CREATE TRIGGER TR_AuditLog_Insert ON audit_logs
AFTER INSERT
AS
BEGIN
    -- Este trigger se ejecuta después de insertar en audit_logs
    -- Puede usarse para notificaciones o procesamiento adicional
    PRINT 'Audit log registrado: ' + CAST(@@ROWCOUNT AS VARCHAR(10)) + ' registros'
END;

-- =====================================================
-- 11. PROCEDIMIENTOS ALMACENADOS BÁSICOS
-- =====================================================

-- Procedimiento para crear usuario con rol por defecto
CREATE PROCEDURE SP_CrearUsuario
    @empleado_id INT = NULL,
    @username VARCHAR(50),
    @password_hash VARCHAR(255),
    @email VARCHAR(100),
    @primer_nombre VARCHAR(50) = NULL, -- Solo para usuarios administrativos
    @apellido_paterno VARCHAR(50) = NULL, -- Solo para usuarios administrativos
    @apellido_materno VARCHAR(50) = NULL, -- Solo para usuarios administrativos
    @rol_id INT = 4, -- Empleado por defecto
    @creado_por INT
AS
BEGIN
    SET NOCOUNT ON;
    
    BEGIN TRY
        BEGIN TRANSACTION;
        
        -- Validar que si es empleado, empleado_id no sea NULL
        IF @empleado_id IS NULL AND (@primer_nombre IS NULL OR @apellido_paterno IS NULL)
        BEGIN
            RAISERROR('Para usuarios administrativos, primer_nombre y apellido_paterno son obligatorios', 16, 1);
            RETURN;
        END
        
        -- Validar que si es empleado, no se pasen nombres
        IF @empleado_id IS NOT NULL AND (@primer_nombre IS NOT NULL OR @apellido_paterno IS NOT NULL)
        BEGIN
            RAISERROR('Para usuarios de empleados, no se deben proporcionar nombres (se toman del empleado)', 16, 1);
            RETURN;
        END
        
        -- Insertar usuario
        INSERT INTO usuarios (empleado_id, username, password_hash, email, primer_nombre, apellido_paterno, apellido_materno, creado_por)
        VALUES (@empleado_id, @username, @password_hash, @email, @primer_nombre, @apellido_paterno, @apellido_materno, @creado_por);
        
        DECLARE @usuario_id INT = SCOPE_IDENTITY();
        
        -- Asignar rol
        INSERT INTO usuario_roles (usuario_id, rol_id, asignado_por)
        VALUES (@usuario_id, @rol_id, @creado_por);
        
        COMMIT TRANSACTION;
        
        SELECT @usuario_id as usuario_id, 'Usuario creado exitosamente' as mensaje;
        
    END TRY
    BEGIN CATCH
        ROLLBACK TRANSACTION;
        THROW;
    END CATCH
END;

-- Procedimiento para verificar permisos de usuario
CREATE PROCEDURE SP_VerificarPermiso
    @usuario_id INT,
    @modulo VARCHAR(50),
    @accion VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    
    SELECT 
        CASE 
            WHEN COUNT(*) > 0 THEN 1 
            ELSE 0 
        END as tiene_permiso
    FROM vista_permisos_usuario vpu
    WHERE vpu.usuario_id = @usuario_id
    AND vpu.modulo = @modulo
    AND vpu.accion = @accion;
END;

-- Procedimiento para limpiar sesiones expiradas
CREATE PROCEDURE SP_LimpiarSesionesExpiradas
AS
BEGIN
    SET NOCOUNT ON;
    
    UPDATE sesiones 
    SET activa = 0 
    WHERE fecha_expiracion < GETDATE() AND activa = 1;
    
    SELECT @@ROWCOUNT as sesiones_limpiadas;
END;

-- Procedimiento para actualizar datos de empleado (mantiene consistencia con usuarios)
CREATE PROCEDURE SP_ActualizarEmpleado
    @empleado_id INT,
    @nombre VARCHAR(100) = NULL,
    @apellido_paterno VARCHAR(100) = NULL,
    @apellido_materno VARCHAR(100) = NULL,
    @email VARCHAR(100) = NULL,
    @telefono VARCHAR(15) = NULL,
    @fecha_nacimiento DATE = NULL,
    @cargo_id INT = NULL,
    @departamento_id INT = NULL,
    @gerencia_id INT = NULL,
    @turno_id INT = NULL,
    @estado_civil_id INT = NULL,
    @tipo_documento_id INT = NULL,
    @sexo VARCHAR(10) = NULL,
    @modificado_por INT
AS
BEGIN
    SET NOCOUNT ON;
    
    BEGIN TRY
        BEGIN TRANSACTION;
        
        -- Actualizar empleado
        UPDATE empleados 
        SET 
            nombre = ISNULL(@nombre, nombre),
            apellido_paterno = ISNULL(@apellido_paterno, apellido_paterno),
            apellido_materno = ISNULL(@apellido_materno, apellido_materno),
            email = ISNULL(@email, email),
            telefono = ISNULL(@telefono, telefono),
            fecha_nacimiento = ISNULL(@fecha_nacimiento, fecha_nacimiento),
            cargo_id = ISNULL(@cargo_id, cargo_id),
            departamento_id = ISNULL(@departamento_id, departamento_id),
            gerencia_id = ISNULL(@gerencia_id, gerencia_id),
            turno_id = ISNULL(@turno_id, turno_id),
            estado_civil_id = ISNULL(@estado_civil_id, estado_civil_id),
            tipo_documento_id = ISNULL(@tipo_documento_id, tipo_documento_id),
            sexo = ISNULL(@sexo, sexo),
            modificado_por = @modificado_por,
            fecha_modificacion = GETDATE()
        WHERE id = @empleado_id;
        
        -- Actualizar email del usuario si existe
        IF @email IS NOT NULL
        BEGIN
            UPDATE usuarios 
            SET 
                email = @email,
                modificado_por = @modificado_por,
                fecha_modificacion = GETDATE()
            WHERE empleado_id = @empleado_id;
        END
        
        COMMIT TRANSACTION;
        
        SELECT 'Empleado actualizado exitosamente' as mensaje;
        
    END TRY
    BEGIN CATCH
        ROLLBACK TRANSACTION;
        THROW;
    END CATCH
END;

-- Procedimiento para gestionar direcciones de empleados
CREATE PROCEDURE SP_GestionarDireccionEmpleado
    @empleado_id INT,
    @tipo_direccion VARCHAR(20), -- ACTUAL, RENIEC, LABORAL
    @direccion_completa VARCHAR(500),
    @ubigeo_id VARCHAR(6) = NULL,
    @referencia VARCHAR(255) = NULL,
    @es_principal BIT = 0,
    @accion VARCHAR(10), -- INSERT, UPDATE, DELETE
    @direccion_id INT = NULL, -- Para UPDATE y DELETE
    @usuario_id INT
AS
BEGIN
    SET NOCOUNT ON;
    
    BEGIN TRY
        BEGIN TRANSACTION;
        
        IF @accion = 'INSERT'
        BEGIN
            -- Si es principal, quitar principal de otras direcciones del mismo tipo
            IF @es_principal = 1
            BEGIN
                UPDATE direcciones_empleado 
                SET es_principal = 0
                WHERE empleado_id = @empleado_id AND tipo_direccion = @tipo_direccion;
            END
            
            -- Insertar nueva dirección
            INSERT INTO direcciones_empleado (
                empleado_id, tipo_direccion, direccion_completa, ubigeo_id, 
                referencia, es_principal, creado_por
            )
            VALUES (
                @empleado_id, @tipo_direccion, @direccion_completa, @ubigeo_id,
                @referencia, @es_principal, @usuario_id
            );
            
            SELECT SCOPE_IDENTITY() as direccion_id, 'Dirección creada exitosamente' as mensaje;
        END
        
        ELSE IF @accion = 'UPDATE'
        BEGIN
            -- Si es principal, quitar principal de otras direcciones del mismo tipo
            IF @es_principal = 1
            BEGIN
                UPDATE direcciones_empleado 
                SET es_principal = 0
                WHERE empleado_id = @empleado_id AND tipo_direccion = @tipo_direccion AND id != @direccion_id;
            END
            
            -- Actualizar dirección
            UPDATE direcciones_empleado 
            SET 
                direccion_completa = @direccion_completa,
                ubigeo_id = @ubigeo_id,
                referencia = @referencia,
                es_principal = @es_principal,
                modificado_por = @usuario_id,
                fecha_modificacion = GETDATE()
            WHERE id = @direccion_id AND empleado_id = @empleado_id;
            
            SELECT 'Dirección actualizada exitosamente' as mensaje;
        END
        
        ELSE IF @accion = 'DELETE'
        BEGIN
            -- Eliminar dirección (soft delete)
            UPDATE direcciones_empleado 
            SET 
                estado = 0,
                modificado_por = @usuario_id,
                fecha_modificacion = GETDATE()
            WHERE id = @direccion_id AND empleado_id = @empleado_id;
            
            SELECT 'Dirección eliminada exitosamente' as mensaje;
        END
        
        COMMIT TRANSACTION;
        
    END TRY
    BEGIN CATCH
        ROLLBACK TRANSACTION;
        THROW;
    END CATCH
END;
