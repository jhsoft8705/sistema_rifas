-- =====================================================
-- SISTEMA DE CONTROL DE ASISTENCIA Y PLANILLA - CAFED
-- Base de datos jerárquica para SQL Server
-- =====================================================
-- =====================================================
-- 1. TABLAS BASE
-- =====================================================
-- Tabla de sedes
CREATE TABLE
    sedes (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        codigo VARCHAR(20) UNIQUE NOT NULL,
        nombre VARCHAR(200) NOT NULL, -- "Sede Principal", "Cafed"
        direccion VARCHAR(500) NULL, -- "Av. Principal 123, Lima"
        telefono VARCHAR(15) NULL,
        email VARCHAR(100) NULL,
        es_principal BIT DEFAULT 0,
        url_foto VARCHAR(255) NULL,
        url_mapa VARCHAR(255) NULL,
        url_favicon VARCHAR(255) NULL,
        -- Configuración financiera por sede
        moneda VARCHAR(10) DEFAULT 'Soles',
        simbolo_moneda VARCHAR(10) DEFAULT 'S/.',
        zona_horaria VARCHAR(50) DEFAULT 'America/Lima',
        tolerancia_entrada_minutos INT DEFAULT 15,
        tolerancia_salida_minutos INT DEFAULT 15,
        requiere_biometrico BIT DEFAULT 1,
        permite_marcacion_manual BIT DEFAULT 0,
        ip_red VARCHAR(45) NULL, -- IP de la red local de la sede
        mascara_red VARCHAR(45) NULL,
        -- Control
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL
    );

-- Tabla de UBIGEO (División geográfica por sede/país)
CREATE TABLE
    ubigeo (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        codigo VARCHAR(20) NOT NULL, -- Código de división geográfica (INEI para Perú, DANE para Colombia, etc.)
        nivel_1 VARCHAR(100) NOT NULL, -- Departamento/Estado/Región
        nivel_2 VARCHAR(100) NOT NULL, -- Provincia/Ciudad
        nivel_3 VARCHAR(100) NOT NULL, -- Distrito/Municipio
        descripcion VARCHAR(255) NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_ubigeo_sede UNIQUE (sede_id, codigo)
    );

-- =====================================================
-- 2. TABLAS DE USUARIOS Y AUTENTICACIÓN (por sede)
-- =====================================================
-- Tabla de roles del sistema (por sede)
CREATE TABLE
    roles (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        nombre VARCHAR(50) NOT NULL,
        descripcion VARCHAR(255) NULL,
        nivel_acceso INT NOT NULL DEFAULT 1, -- 1=Básico, 2=Intermedio, 3=Avanzado, 4=Administrador
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_rol_sede UNIQUE (sede_id, nombre)
    );

-- Tabla de permisos del sistema (por sede)
CREATE TABLE
    permisos (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        nombre VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NULL,
        modulo VARCHAR(50) NOT NULL, -- EMPLEADOS, ASISTENCIA, PLANILLA, REPORTES, CONFIGURACION
        accion VARCHAR(50) NOT NULL, -- CREAR, LEER, ACTUALIZAR, ELIMINAR, EXPORTAR, IMPORTAR
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_permiso_sede UNIQUE (sede_id, nombre)
    );

-- Tabla de usuarios del sistema (por sede)
CREATE TABLE
    usuarios (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        empleado_id INT NULL, -- NULL para usuarios administrativos, NOT NULL para empleados
        username VARCHAR(50) NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        -- Campos opcionales solo para usuarios administrativos (cuando empleado_id IS NULL)
        primer_nombre VARCHAR(50) NULL, -- Solo para usuarios administrativos
        apellido_paterno VARCHAR(50) NULL, -- Solo para usuarios administrativos
        apellido_materno VARCHAR(50) NULL, -- Solo para usuarios administrativos
        telefono VARCHAR(15) NULL,
        ultimo_acceso DATETIME NULL,
        intentos_fallidos INT DEFAULT 0,
        cuenta_bloqueada BIT DEFAULT 0,
        fecha_bloqueo DATETIME NULL,
        debe_cambiar_password BIT DEFAULT 1,
        fecha_expiracion_password DATETIME NULL,
        estado INT NOT NULL DEFAULT 1, -- 1=Activo, 0=Inactivo, 2=Bloqueado
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        FOREIGN KEY (empleado_id) REFERENCES empleados (id),
        -- Constraint: Si es empleado, empleado_id debe ser NOT NULL
        -- Si es administrativo, empleado_id debe ser NULL y nombres deben estar llenos
        CONSTRAINT CK_usuarios_tipo CHECK (
            (
                empleado_id IS NOT NULL
                AND primer_nombre IS NULL
                AND apellido_paterno IS NULL
            )
            OR (
                empleado_id IS NULL
                AND primer_nombre IS NOT NULL
                AND apellido_paterno IS NOT NULL
            )
        ),
        CONSTRAINT unique_username_sede UNIQUE (sede_id, username),
        CONSTRAINT unique_email_sede UNIQUE (sede_id, email)
    );

-- Tabla de relación usuario-rol (por sede)
CREATE TABLE
    usuario_roles (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        usuario_id INT NOT NULL,
        rol_id INT NOT NULL,
        fecha_asignacion DATETIME DEFAULT GETDATE (),
        fecha_vencimiento DATETIME NULL,
        estado INT NOT NULL DEFAULT 1,
        asignado_por VARCHAR(50) NOT NULL,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ,
        FOREIGN KEY (rol_id) REFERENCES roles (id),
        CONSTRAINT unique_usuario_rol_sede UNIQUE (sede_id, usuario_id, rol_id)
    );

-- Tabla de relación usuario-permiso (por sede)
CREATE TABLE
    usuario_permisos (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        usuario_id INT NOT NULL,
        permiso_id INT NOT NULL,
        fecha_asignacion DATETIME DEFAULT GETDATE (),
        fecha_vencimiento DATETIME NULL,
        estado INT NOT NULL DEFAULT 1,
        asignado_por VARCHAR(50) NOT NULL,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ,
        FOREIGN KEY (permiso_id) REFERENCES permisos (id),
        CONSTRAINT unique_usuario_permiso_sede UNIQUE (sede_id, usuario_id, permiso_id)
    );

-- Tabla de relación rol-permiso (por sede)
CREATE TABLE
    rol_permisos (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        rol_id INT NOT NULL,
        permiso_id INT NOT NULL,
        fecha_asignacion DATETIME DEFAULT GETDATE (),
        estado INT NOT NULL DEFAULT 1,
        asignado_por VARCHAR(50) NOT NULL,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        FOREIGN KEY (rol_id) REFERENCES roles (id) ON DELETE CASCADE,
        FOREIGN KEY (permiso_id) REFERENCES permisos (id),
        CONSTRAINT unique_rol_permiso_sede UNIQUE (sede_id, rol_id, permiso_id)
    );

-- Tabla de sesiones activas (por sede)
CREATE TABLE
    sesiones (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        usuario_id INT NOT NULL,
        token_sesion VARCHAR(255) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent VARCHAR(500) NULL,
        fecha_inicio DATETIME DEFAULT GETDATE (),
        fecha_ultima_actividad DATETIME DEFAULT GETDATE (),
        fecha_expiracion DATETIME NOT NULL,
        activa BIT DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ,
        CONSTRAINT unique_token_sede UNIQUE (sede_id, token_sesion)
    );

-- Tabla de intentos de acceso
CREATE TABLE
    intentos_acceso (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NULL, -- CORREGIDO: Agregado para multi-sede (NULL para intentos de login)
        username VARCHAR(50) NOT NULL,
        ip_address VARCHAR(45) NOT NULL,
        user_agent VARCHAR(500) NULL,
        exito BIT NOT NULL,
        motivo_fallo VARCHAR(255) NULL,
        fecha_intento DATETIME DEFAULT GETDATE (),
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE SET NULL -- Permite NULL para intentos de login
    );

-- =====================================================
-- 1. TABLAS MAESTRAS (ACTUALIZADAS CON AUDITORÍA)
-- =====================================================
-- Tabla de gerencias/unidades organizacionales (por sede)
CREATE TABLE
    gerencias (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        codigo VARCHAR(20) NOT NULL,
        nombre VARCHAR(200) NOT NULL,
        descripcion VARCHAR(500) NULL,
        gerente_id INT NULL, -- Empleado gerente
        id_gerencia_padre INT NULL, -- CORREGIDO: Indentación
        nivel_jerarquico TINYINT DEFAULT 1,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT FK_gerencia_padre FOREIGN KEY (id_gerencia_padre) REFERENCES gerencias (id),
        CONSTRAINT unique_gerencia_sede UNIQUE (sede_id, codigo)
    );

-- Tabla de cargos (por sede)
CREATE TABLE
    cargos (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        nombre_cargo VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NULL,
        salario_base DECIMAL(10, 2) NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_cargo_sede UNIQUE (sede_id, nombre_cargo)
    );

-- Tabla de tipo de ausentismo (por sede)
CREATE TABLE
    tipo_ausentismo (
        id_tipo INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        nombre_tipo VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_tipo_ausentismo_sede UNIQUE (sede_id, nombre_tipo)
    );

-- Tabla de motivo de ausentismo (por sede)
CREATE TABLE
    motivo_ausentismo (
        id_motivo INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        tipo_ausentismo_id INT NOT NULL,
        nombre_motivo VARCHAR(100) NOT NULL,
        compensacion BIT DEFAULT 0,
        tardanza BIT DEFAULT 0,
        descripcion VARCHAR(255) NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        FOREIGN KEY (tipo_ausentismo_id) REFERENCES tipo_ausentismo (id_tipo),
        CONSTRAINT unique_motivo_ausentismo_sede UNIQUE (sede_id, nombre_motivo)
    );

-- Tabla de horarios generales (por sede)
CREATE TABLE
    horarios (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        nombre_horario VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_horario_sede UNIQUE (sede_id, nombre_horario)
    );

-- Tabla de turnos laborables (por sede)
CREATE TABLE
    turnos_laborables (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        nombre_turno VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_turno_sede UNIQUE (sede_id, nombre_turno)
    );

-- Tabla de períodos de asistencia (por sede)
CREATE TABLE
    periodo_asistencia (
        id_periodo INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        anio INT NOT NULL,
        tipo VARCHAR(20) NOT NULL,
        periodo VARCHAR(50) NOT NULL,
        fecha_inicio DATE NOT NULL,
        fecha_termino DATE NOT NULL,
        nombre VARCHAR(100) NOT NULL,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_periodo_sede UNIQUE (sede_id, anio, tipo, periodo)
    );

-- Tabla de configuración específica por sede
CREATE TABLE
    configuracion_sede (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        clave VARCHAR(100) NOT NULL,
        valor NVARCHAR (MAX) NULL,
        descripcion VARCHAR(255) NULL,
        tipo_dato VARCHAR(20) DEFAULT 'STRING' CHECK (
            tipo_dato IN (
                'STRING',
                'INTEGER',
                'DECIMAL',
                'BOOLEAN',
                'DATE',
                'JSON'
            )
        ),
        es_obligatorio BIT DEFAULT 0,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_config_sede UNIQUE (sede_id, clave)
    );

-- =====================================================
-- 2. TABLAS DE CONFIGURACIÓN DE HORARIOS (ACTUALIZADAS)
-- =====================================================
-- Tabla para establecer horario semanal por turno
CREATE TABLE
    horario_semanal (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        turno_id INT NOT NULL,
        dia_semana VARCHAR(20) NOT NULL CHECK (
            dia_semana IN (
                'Lunes',
                'Martes',
                'Miercoles',
                'Jueves',
                'Viernes',
                'Sabado',
                'Domingo'
            )
        ),
        horario_id INT NOT NULL,
        hora_inicio TIME NOT NULL,
        hora_fin TIME NOT NULL,
        es_laborable BIT DEFAULT 1,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (turno_id) REFERENCES turnos_laborables (id) ON DELETE CASCADE,
        FOREIGN KEY (horario_id) REFERENCES horarios (id),
        CONSTRAINT unique_turno_dia_sede UNIQUE (sede_id, turno_id, dia_semana)
    );

-- Tabla para configuración especial de días
CREATE TABLE
    configuracion_dias (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        turno_id INT NOT NULL,
        tipo_dia VARCHAR(20) NOT NULL CHECK (
            tipo_dia IN ('Descanso', 'No_fiscalizado', 'Remoto')
        ),
        dias_semana NVARCHAR (MAX) NOT NULL,
        descripcion VARCHAR(255) NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (turno_id) REFERENCES turnos_laborables (id) ON DELETE CASCADE,
        CONSTRAINT unique_configuracion_dias_sede UNIQUE (sede_id, turno_id, tipo_dia)
    );

-- Tabla para configuración de entrada
CREATE TABLE
    configuracion_entrada (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        horario_id INT NOT NULL,
        hora_entrada TIME NOT NULL,
        inicio_entrada TIME NOT NULL,
        fin_entrada TIME NOT NULL,
        tolerancia_entrada INT NOT NULL DEFAULT 0,
        descontar_tolerancia_planilla BIT DEFAULT 0,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (horario_id) REFERENCES horarios (id) ON DELETE CASCADE,
        CONSTRAINT unique_configuracion_entrada_sede UNIQUE (sede_id, horario_id)
    );

-- Tabla para configuración de salida
CREATE TABLE
    configuracion_salida (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        horario_id INT NOT NULL,
        hora_salida TIME NULL,
        inicio_salida TIME NULL,
        fin_salida TIME NULL,
        restringir_salida BIT DEFAULT 0,
        tolerancia_salida INT NOT NULL DEFAULT 0,
        descontar_tolerancia_planilla BIT DEFAULT 0,
        acumular_tolerancia_horas BIT DEFAULT 0,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (horario_id) REFERENCES horarios (id) ON DELETE CASCADE,
        CONSTRAINT unique_configuracion_salida_sede UNIQUE (sede_id, horario_id)
    );

-- Tabla para configuración de refrigerio
CREATE TABLE
    configuracion_refrigerio (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        horario_id INT NOT NULL,
        inicio_refrigerio TIME NULL,
        fin_refrigerio TIME NULL,
        duracion_refrigerio INT NOT NULL DEFAULT 0,
        minutos_equivale_ref INT NOT NULL DEFAULT 0,
        tolerancia_retorno INT NOT NULL DEFAULT 0,
        permitir_marcacion_antes BIT DEFAULT 0,
        acumular_retorno_anticipado BIT DEFAULT 0,
        acumular_minutos_horas BIT DEFAULT 0,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (horario_id) REFERENCES horarios (id) ON DELETE CASCADE,
        CONSTRAINT unique_configuracion_refrigerio_sede UNIQUE (sede_id, horario_id)
    );

-- =====================================================
-- 3. TABLAS DE EMPLEADOS Y BIOMÉTRICO (ACTUALIZADAS)
-- =====================================================
CREATE TABLE
    estado_civil (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        descripcion VARCHAR(255) NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_estado_civil_sede UNIQUE (sede_id, descripcion)
    );

CREATE TABLE
    tipos_documento (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        descripcion VARCHAR(255) NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_tipos_documento_sede UNIQUE (sede_id, descripcion)
    );

CREATE TABLE
    grados_instruccion (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        descripcion VARCHAR(255) NOT NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_grados_instruccion_sede UNIQUE (sede_id, descripcion)
    );

CREATE TABLE
    profesiones (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        descripcion VARCHAR(255) NOT NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_profesiones_sede UNIQUE (sede_id, descripcion)
    );

-- =====================================================
-- TABLAS AUXILIARES PARA PLANILLAS
-- =====================================================
-- Tabla de sistemas de pensiones (por sede/país)
CREATE TABLE
    sistemas_pension (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        codigo VARCHAR(10) NOT NULL, -- ONP, AFP, etc.
        nombre VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NULL,
        porcentaje_empleado DECIMAL(5, 2) NULL, -- Porcentaje que paga el empleado
        porcentaje_empleador DECIMAL(5, 2) NULL, -- Porcentaje que paga el empleador
        es_obligatorio BIT DEFAULT 1,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_sistema_pension_sede UNIQUE (sede_id, codigo)
    );

-- Tabla de regímenes laborales (por sede/país)
CREATE TABLE
    regimenes_laborales (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        codigo VARCHAR(20) NOT NULL,
        nombre VARCHAR(200) NOT NULL,
        descripcion VARCHAR(500) NULL,
        base_legal VARCHAR(200) NULL, -- Decreto, Ley, etc.
        duracion_meses INT NULL, -- Para contratos temporales
        es_indefinido BIT DEFAULT 0,
        es_tiempo_parcial BIT DEFAULT 0,
        es_intermitente BIT DEFAULT 0,
        es_activo BIT DEFAULT 1,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_regimen_laboral_sede UNIQUE (sede_id, codigo)
    );

-- Tabla de tipos de trabajador (por sede/país)
CREATE TABLE
    tipos_trabajador (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        codigo VARCHAR(20) NOT NULL,
        nombre VARCHAR(100) NOT NULL, -- D. LEG.1057 - CAS, FUNCIONARIO, CONTRATADO, EMPLEADO, CAS, etc.
        descripcion VARCHAR(255) NULL,
        base_legal VARCHAR(200) NULL,
        es_funcionario BIT DEFAULT 0,
        es_contratado BIT DEFAULT 0,
        es_empleado BIT DEFAULT 0,
        es_cas BIT DEFAULT 0,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_tipo_trabajador_sede UNIQUE (sede_id, codigo)
    );

-- Tabla de niveles remunerativos (por sede/país)
CREATE TABLE
    niveles_remunerativos (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        codigo VARCHAR(20) NOT NULL,
        nombre VARCHAR(100) NOT NULL, -- funcionario, contratado, empleado, cas, obrero
        descripcion VARCHAR(255) NULL,
        salario_minimo DECIMAL(10, 2) NULL,
        salario_maximo DECIMAL(10, 2) NULL,
        es_contrato BIT DEFAULT 0,
        es_empleado BIT DEFAULT 0,
        es_obrero BIT DEFAULT 0,
        es_funcionario BIT DEFAULT 0,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_nivel_remunerativo_sede UNIQUE (sede_id, codigo)
    );

-- Tabla de bancos (por sede/país)
CREATE TABLE
    bancos (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        codigo VARCHAR(10) NOT NULL, -- Código del banco
        nombre VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NULL,
        codigo_swift VARCHAR(20) NULL, -- Código SWIFT internacional
        es_activo BIT DEFAULT 1,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_banco_sede UNIQUE (sede_id, codigo)
    );

-- Tabla de tipos de cuenta bancaria (por sede/país)
CREATE TABLE
    tipos_cuenta_bancaria (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        descripcion VARCHAR(50) NOT NULL, -- Ahorros, Corriente, Sueldo
        codigo VARCHAR(10) NOT NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_tipo_cuenta_sede UNIQUE (sede_id, codigo)
    );

-- Tabla de empleados
CREATE TABLE
    empleados (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        -- Datos personales
        tipo_documento_id INT NULL,
        nro_documento VARCHAR(20) NOT NULL, -- ( único por sede)
        nombre VARCHAR(100) NOT NULL,
        apellido_paterno VARCHAR(100) NOT NULL,
        apellido_materno VARCHAR(100) NULL,
        telefono VARCHAR(15) NULL,
        email VARCHAR(100) UNIQUE NOT NULL,
        sexo VARCHAR(10) NULL CHECK (sexo IN ('Masculino', 'Femenino')),
        profesion_id INT NULL,
        grado_institucion_id INT NULL,
        fecha_nacimiento DATE NULL,
        estado_civil_id INT NULL,
        coordenada_x DECIMAL(10, 2) NULL,
        coordenada_y DECIMAL(10, 2) NULL,
        url_maps VARCHAR(255) NULL,
        url_foto VARCHAR(255) NULL,
        -- Datos laborales
        fecha_ingreso DATE NOT NULL,
        fecha_cese DATE NULL,
        gerencia_id INT NULL, -- Unidad Organizacional 
        cargo_id INT NULL,
        turno_id INT NULL,
        -- Datos de planilla
        sistema_pension_id INT NULL,
        regimen_laboral_id INT NULL,
        tipo_trabajador_id INT NULL,
        nivel_remunerativo_id INT NULL,
        -- Datos bancarios
        banco_id INT NULL,
        numero_cuenta VARCHAR(20) NULL,
        numero_cci VARCHAR(25) NULL,
        -- Datos adicionales
        cuspp VARCHAR(12) NULL, -- Código Único del Sistema Privado de Pensiones
        airhsp VARCHAR(20) NULL, -- Código AIRHSP
        codigo_reloj VARCHAR(20) NULL,
        observaciones TEXT NULL,
        -- Control
        estado INT NOT NULL DEFAULT 1, -- 1=Activo, 0=Inactivo
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        -- Foreign Keys
        FOREIGN KEY (tipo_documento_id) REFERENCES tipos_documento (id),
        FOREIGN KEY (estado_civil_id) REFERENCES estado_civil (id),
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (gerencia_id) REFERENCES gerencias (id),
        FOREIGN KEY (cargo_id) REFERENCES cargos (id),
        FOREIGN KEY (turno_id) REFERENCES turnos_laborables (id),
        FOREIGN KEY (sistema_pension_id) REFERENCES sistemas_pension (id),
        FOREIGN KEY (regimen_laboral_id) REFERENCES regimenes_laborales (id),
        FOREIGN KEY (profesion_id) REFERENCES profesiones (id),
        FOREIGN KEY (grado_institucion_id) REFERENCES grados_instruccion (id),
        FOREIGN KEY (tipo_trabajador_id) REFERENCES tipos_trabajador (id),
        FOREIGN KEY (nivel_remunerativo_id) REFERENCES niveles_remunerativos (id),
        FOREIGN KEY (banco_id) REFERENCES bancos (id),
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_empleado_documento_sede UNIQUE (sede_id, nro_documento)
    );

-- Tabla de direcciones de empleados
CREATE TABLE
    direcciones_empleado (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        empleado_id INT NOT NULL,
        tipo_direccion VARCHAR(20) NOT NULL CHECK (tipo_direccion IN ('ACTUAL', 'RENIEC', 'LABORAL')),
        direccion_completa VARCHAR(500) NOT NULL,
        ubigeo_id INT NULL, -- Referencia al UBIGEO
        referencia VARCHAR(255) NULL, -- Referencias adicionales (cerca de...)
        es_principal BIT DEFAULT 0, -- Si es la dirección principal
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (empleado_id) REFERENCES empleados (id) ON DELETE CASCADE,
        FOREIGN KEY (ubigeo_id) REFERENCES ubigeo (id),
        CONSTRAINT unique_empleado_tipo_direccion_sede UNIQUE (sede_id, empleado_id, tipo_direccion)
    );

-- Tabla de dispositivos biométricos (por sede)
CREATE TABLE
    dispositivos_biometricos (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        device_id VARCHAR(100) NOT NULL,
        modelo VARCHAR(100) NULL,
        ip_address VARCHAR(45) NULL,
        ubicacion VARCHAR(200) NULL,
        puerto INT NULL,
        protocolo VARCHAR(50) NULL,
        estado INT NOT NULL DEFAULT 1,
        last_seen DATETIME NULL,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        CONSTRAINT unique_device_sede UNIQUE (sede_id, device_id)
    );

-- Tabla de mapeo empleado-biométrico 
CREATE TABLE
    empleado_biometrico (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        empleado_id INT NOT NULL,
        biometric_user_id VARCHAR(100) NOT NULL,
        device_id VARCHAR(100) NULL,
        activo BIT DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (empleado_id) REFERENCES empleados (id) ON DELETE CASCADE,
        CONSTRAINT unique_biometric_user_sede UNIQUE (sede_id, biometric_user_id, device_id)
    );

-- =====================================================
-- 4. TABLAS DE ASISTENCIA Y MARCACIONES (ACTUALIZADAS)
-- =====================================================
-- Tabla de logs brutos del biométrico (inmutables)
CREATE TABLE
    biometric_log_raw (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        device_id VARCHAR(100) NULL,
        biometric_user_id VARCHAR(100) NOT NULL,
        evento_at DATETIME NOT NULL,
        evento_tipo VARCHAR(30) NULL,
        raw_data NVARCHAR (MAX) NULL,
        procesado BIT DEFAULT 0,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
    );



 -- Tabla de marcaciones procesadas
CREATE TABLE
    marcaciones (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        empleado_id INT NOT NULL,
        fecha_marcacion DATE NOT NULL,
        minutos_refrigerio_extendido INT DEFAULT 0,
        estado_asistencia VARCHAR(20) DEFAULT 'Presente' CHECK (estado_asistencia IN ('Presente', 'Tardanza', 'Falta', 'Justificado')),
        observaciones TEXT NULL,
        tipo_marcacion VARCHAR(30) DEFAULT 'INCOMPLETO' CHECK (tipo_marcacion IN ('INCOMPLETO','DIA_NORMAL','DIA_CON_HE','DIA_CON_REFRIGERIO','DIA_COMPLETO_HE')),
        hora_entrada TIME NULL,
        hora_salida TIME NULL,
        hora_entrada_refrigerio TIME NULL,
        hora_salida_refrigerio TIME NULL,
        hora_entrada_he TIME NULL,
        hora_salida_he TIME NULL,
        minutos_tardanza INT DEFAULT 0,
        minutos_anticipo_salida INT DEFAULT 0,
        minutos_tolerancia_entrada INT DEFAULT 0,
        minutos_tolerancia_salida INT DEFAULT 0,
        fuente VARCHAR(30) DEFAULT 'BIOMETRICO',
        reconciliado BIT DEFAULT 0,
        reconciliado_por VARCHAR(50) NULL,
        reconciliado_en DATETIME NULL,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (empleado_id) REFERENCES empleados (id) ON DELETE CASCADE,
        CONSTRAINT unique_marcacion_sede UNIQUE (sede_id, empleado_id, fecha_marcacion)
);

-- Tabla de ajustes manuales de asistencia
CREATE TABLE
    marcaciones_ajuste (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        marcacion_id INT NOT NULL,
        empleado_id INT NOT NULL,
        tipo_ajuste VARCHAR(50) NOT NULL,
        descripcion TEXT NULL,
        datos_antes NVARCHAR (MAX) NULL,
        datos_despues NVARCHAR (MAX) NULL,
        registrado_por VARCHAR(50) NOT NULL,
        registrado_en DATETIME DEFAULT GETDATE (),
        aprobado_por VARCHAR(50) NULL,
        aprobado_en DATETIME NULL,
        estado VARCHAR(20) DEFAULT 'PENDIENTE' CHECK (estado IN ('PENDIENTE', 'APROBADO', 'RECHAZADO')),
        observaciones_aprobacion TEXT NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (marcacion_id) REFERENCES marcaciones (id),
        FOREIGN KEY (empleado_id) REFERENCES empleados (id)
    );



-- =====================================================
-- 5. TABLAS DE PLANILLA Y BENEFICIOS (ACTUALIZADAS)
-- =====================================================
-- Tabla de beneficios laborales (por sede)
CREATE TABLE
    beneficios_laborales (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        empleado_id INT NOT NULL,
        tipo_beneficio VARCHAR(100) NOT NULL,
        descripcion TEXT NULL,
        monto DECIMAL(12, 2) NULL,
        porcentaje DECIMAL(5, 2) NULL,
        fecha_inicio DATE NOT NULL,
        fecha_fin DATE NULL,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        FOREIGN KEY (empleado_id) REFERENCES empleados (id),
,
    );

-- Tabla de conceptos de planilla (por sede)
CREATE TABLE
    conceptos_planilla (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        codigo VARCHAR(20) NOT NULL,
        nombre VARCHAR(100) NOT NULL,
        descripcion VARCHAR(255) NULL,
        tipo VARCHAR(20) NOT NULL CHECK (tipo IN ('INGRESO', 'DESCUENTO', 'APORTE')),
        formula VARCHAR(500) NULL,
        monto_fijo DECIMAL(12, 2) NULL,
        aplica_gratificacion BIT DEFAULT 0,
        estado INT NOT NULL DEFAULT 1,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        CONSTRAINT unique_concepto_planilla_sede UNIQUE (sede_id, codigo)
    );

-- Tabla de planillas (por sede)
CREATE TABLE
    planillas (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL,
        periodo_id INT NOT NULL,
        nombre_planilla VARCHAR(100) NOT NULL,
        regimen_laboral VARCHAR(100) NOT NULL,
        tipo_planilla VARCHAR(50) NOT NULL,
        fecha_inicio DATE NOT NULL,
        fecha_fin DATE NOT NULL,
        estado VARCHAR(20) DEFAULT 'BORRADOR' CHECK (estado IN ('BORRADOR', 'PROCESADA', 'CERRADA')),
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id),
        FOREIGN KEY (periodo_id) REFERENCES periodo_asistencia (id_periodo),
        CONSTRAINT unique_planilla_sede UNIQUE (sede_id, periodo_id, tipo_planilla)
    );

-- Tabla de detalle de planilla
CREATE TABLE
    planilla_detalle (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        sede_id INT NOT NULL, -- CORREGIDO: Agregado para multi-sede
        planilla_id INT NOT NULL,
        empleado_id INT NOT NULL,
        concepto_id INT NOT NULL,
        cantidad DECIMAL(10, 2) DEFAULT 1,
        monto DECIMAL(12, 2) NOT NULL,
        fecha_creacion DATETIME DEFAULT GETDATE (),
        fecha_modificacion DATETIME DEFAULT GETDATE (),
        creado_por VARCHAR(50) NULL,
        modificado_por VARCHAR(50) NULL,
        FOREIGN KEY (sede_id) REFERENCES sedes (id) ON DELETE CASCADE,
        FOREIGN KEY (planilla_id) REFERENCES planillas (id),
        FOREIGN KEY (empleado_id) REFERENCES empleados (id),
        FOREIGN KEY (concepto_id) REFERENCES conceptos_planilla (id)
    );

-- =====================================================
-- 6. TABLAS DE AUDITORÍA Y SINCRONIZACIÓN (ACTUALIZADAS)
-- =====================================================
-- Tabla de errores del trigger debugging   
 CREATE TABLE errores_trigger (
    id INT IDENTITY (1, 1) PRIMARY KEY,
    trigger_name VARCHAR(200),
    error_message NVARCHAR (4000),
    error_number INT,
    error_severity INT,
    error_state INT,
    error_line INT,
    emp_code VARCHAR(100),
    punch_time DATETIME,
    punch_state INT,
    fecha_error DATETIME DEFAULT GETDATE ()
);

-- Tabla de logs de auditoría No ejecutado
CREATE TABLE
    audit_logs (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        tabla_afectada VARCHAR(100) NOT NULL,
        registro_id INT NOT NULL,
        operacion VARCHAR(20) NOT NULL,
        datos_anteriores NVARCHAR (MAX) NULL,
        datos_nuevos NVARCHAR (MAX) NULL,
        usuario_id VARCHAR(50) NULL,
        ip_address VARCHAR(45) NULL,
        user_agent VARCHAR(500) NULL,
        fecha_operacion DATETIME DEFAULT GETDATE ()
    );

-- =====================================================
-- 7. ÍNDICES PARA OPTIMIZACIÓN
-- =====================================================
-- Índices para búsquedas frecuentes
CREATE INDEX IX_empleados_nro_documento ON empleados (nro_documento);

CREATE INDEX IX_empleados_gerencia ON empleados (gerencia_id);

CREATE INDEX IX_empleados_sede ON empleados (sede_id);

CREATE INDEX IX_marcaciones_empleado_fecha ON marcaciones (empleado_id, fecha_marcacion);

CREATE INDEX IX_biometric_log_raw_user_evento ON biometric_log_raw (biometric_user_id, evento_at);

CREATE INDEX IX_empleado_biometrico_user ON empleado_biometrico (biometric_user_id);

CREATE INDEX IX_sync_queue_procesado ON sync_queue (procesado, fecha_creacion);

-- Índices específicos para multisede
CREATE INDEX IX_ubigeo_sede ON ubigeo (sede_id);

CREATE INDEX IX_gerencias_sede ON gerencias (sede_id);

CREATE INDEX IX_cargos_sede ON cargos (sede_id);

CREATE INDEX IX_horarios_sede ON horarios (sede_id);

CREATE INDEX IX_turnos_sede ON turnos_laborables (sede_id);

CREATE INDEX IX_dispositivos_sede ON dispositivos_biometricos (sede_id);

CREATE INDEX IX_periodos_sede ON periodo_asistencia (sede_id);

CREATE INDEX IX_config_sede ON configuracion_sede (sede_id);

CREATE INDEX IX_planillas_sede ON planillas (sede_id);

CREATE INDEX IX_tipos_trabajador_sede ON tipos_trabajador (sede_id);

CREATE INDEX IX_niveles_remunerativos_sede ON niveles_remunerativos (sede_id);

CREATE INDEX IX_sistemas_pension_sede ON sistemas_pension (sede_id);

CREATE INDEX IX_regimenes_laborales_sede ON regimenes_laborales (sede_id);

CREATE INDEX IX_bancos_sede ON bancos (sede_id);

CREATE INDEX IX_tipos_cuenta_bancaria_sede ON tipos_cuenta_bancaria (sede_id);

CREATE INDEX IX_beneficios_laborales_sede ON beneficios_laborales (sede_id);

CREATE INDEX IX_conceptos_planilla_sede ON conceptos_planilla (sede_id);

CREATE INDEX IX_tipo_ausentismo_sede ON tipo_ausentismo (sede_id);

CREATE INDEX IX_motivo_ausentismo_sede ON motivo_ausentismo (sede_id);

CREATE INDEX IX_empleados_sede_estado ON empleados (sede_id, estado);

CREATE INDEX IX_marcaciones_sede_fecha ON marcaciones (empleado_id, fecha_marcacion) INCLUDE (estado_asistencia, tipo_marcacion);

-- Índices para usuarios y autenticación (por sede)
CREATE INDEX IX_roles_sede ON roles (sede_id);

CREATE INDEX IX_permisos_sede ON permisos (sede_id);

CREATE INDEX IX_usuarios_sede ON usuarios (sede_id);

CREATE INDEX IX_usuarios_empleado ON usuarios (empleado_id);

CREATE INDEX IX_usuario_roles_sede ON usuario_roles (sede_id);

CREATE INDEX IX_usuario_permisos_sede ON usuario_permisos (sede_id);

CREATE INDEX IX_rol_permisos_sede ON rol_permisos (sede_id);

CREATE INDEX IX_sesiones_sede ON sesiones (sede_id);

CREATE INDEX IX_sesiones_usuario ON sesiones (usuario_id);

CREATE INDEX IX_sesiones_activa ON sesiones (activa, fecha_expiracion);

-- Índices para auditoría
CREATE INDEX IX_audit_logs_tabla ON audit_logs (tabla_afectada, fecha_operacion);

CREATE INDEX IX_audit_logs_usuario ON audit_logs (usuario_id, fecha_operacion);

CREATE INDEX IX_intentos_acceso_username ON intentos_acceso (username, fecha_intento);