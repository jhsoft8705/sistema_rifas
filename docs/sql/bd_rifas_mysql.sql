-- =====================================================
-- SISTEMA DE RIFAS MULTISEDE - PROFESIONAL
-- Base de datos MySQL
-- Escalable y Personalizable para diferentes países
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS intentos_acceso;
DROP TABLE IF EXISTS sesiones;
DROP TABLE IF EXISTS usuario_permisos;
DROP TABLE IF EXISTS usuario_roles;
DROP TABLE IF EXISTS rol_permisos;
DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS permisos;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS ganadores;
DROP TABLE IF EXISTS intentos_sorteo;
DROP TABLE IF EXISTS participantes;
DROP TABLE IF EXISTS comprobantes_pago;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS rifas;
DROP TABLE IF EXISTS premios;
DROP TABLE IF EXISTS categorias_premios;
DROP TABLE IF EXISTS metodos_pago;
DROP TABLE IF EXISTS estados_ticket;
DROP TABLE IF EXISTS ubicaciones_rifa;
DROP TABLE IF EXISTS configuracion_sede;
DROP TABLE IF EXISTS cargos;
DROP TABLE IF EXISTS sedes;
DROP TABLE IF EXISTS audit_logs;
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- 1. TABLA BASE - SEDES (Multi-país)
-- =====================================================
CREATE TABLE sedes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) UNIQUE NOT NULL,
    nombre VARCHAR(200) NOT NULL COMMENT 'Nombre de la sede/país',
    pais VARCHAR(100) NOT NULL COMMENT 'Perú, Colombia, Chile, etc.',
    descripcion TEXT NULL,
    direccion VARCHAR(500) NULL,
    telefono VARCHAR(15) NULL,
    email VARCHAR(100) NULL,
    es_principal TINYINT(1) DEFAULT 0,
    
    -- URLs y recursos
    url_logo VARCHAR(255) NULL,
    url_favicon VARCHAR(255) NULL,
    url_landing VARCHAR(255) NULL COMMENT 'URL de la landing page',
    
    -- Configuración financiera por país
    moneda VARCHAR(50) DEFAULT 'Soles' COMMENT 'Soles, Pesos, Dólares, etc.',
    simbolo_moneda VARCHAR(10) DEFAULT 'S/.' COMMENT 'S/., $, COP, etc.',
    codigo_moneda VARCHAR(3) DEFAULT 'PEN' COMMENT 'ISO 4217: PEN, COP, USD',
    zona_horaria VARCHAR(50) DEFAULT 'America/Lima',
    
    -- Configuración de pagos
    requiere_aprobacion_manual TINYINT(1) DEFAULT 1 COMMENT 'Si requiere validación manual de pagos',
    dias_validez_ticket INT DEFAULT 90 COMMENT 'Días de validez del ticket',
    
    -- Configuración de red
    ip_red VARCHAR(45) NULL,
    mascara_red VARCHAR(45) NULL,
    
    -- Control
    estado INT NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    INDEX idx_sede_estado (estado),
    INDEX idx_sede_pais (pais)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla de sedes por país';

-- =====================================================
-- 2. TABLAS DE AUTENTICACIÓN (Multi-sede)
-- =====================================================

-- Tabla de roles del sistema
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255) NULL,
    nivel_acceso INT NOT NULL DEFAULT 1 COMMENT '1=Básico, 2=Intermedio, 3=Avanzado, 4=Admin',
    estado INT NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rol_sede (sede_id, nombre),
    INDEX idx_roles_sede (sede_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de permisos del sistema
CREATE TABLE permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NULL,
    modulo VARCHAR(50) NOT NULL COMMENT 'RIFAS, PREMIOS, PARTICIPANTES, REPORTES, CONFIG',
    accion VARCHAR(50) NOT NULL COMMENT 'CREAR, LEER, ACTUALIZAR, ELIMINAR, APROBAR',
    estado INT NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_permiso_sede (sede_id, nombre),
    INDEX idx_permisos_sede (sede_id),
    INDEX idx_permisos_modulo (modulo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios del sistema
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    username VARCHAR(50) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    
    -- Información personal
    primer_nombre VARCHAR(50) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50) NULL,
    telefono VARCHAR(15) NULL,
    
    -- Control de sesión
    ultimo_acceso DATETIME NULL,
    intentos_fallidos INT DEFAULT 0,
    cuenta_bloqueada TINYINT(1) DEFAULT 0,
    fecha_bloqueo DATETIME NULL,
    debe_cambiar_password TINYINT(1) DEFAULT 1,
    fecha_expiracion_password DATETIME NULL,
    
    -- Control
    estado INT NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo, 2=Bloqueado',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_username_sede (sede_id, username),
    UNIQUE KEY unique_email_sede (sede_id, email),
    INDEX idx_usuarios_sede (sede_id),
    INDEX idx_usuarios_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de relación usuario-rol
CREATE TABLE usuario_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    usuario_id INT NOT NULL,
    rol_id INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento DATETIME NULL,
    estado INT NOT NULL DEFAULT 1,
    asignado_por VARCHAR(50) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_rol_sede (sede_id, usuario_id, rol_id),
    INDEX idx_usuario_roles_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de relación usuario-permiso
CREATE TABLE usuario_permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    usuario_id INT NOT NULL,
    permiso_id INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento DATETIME NULL,
    estado INT NOT NULL DEFAULT 1,
    asignado_por VARCHAR(50) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usuario_permiso_sede (sede_id, usuario_id, permiso_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de relación rol-permiso
CREATE TABLE rol_permisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    rol_id INT NOT NULL,
    permiso_id INT NOT NULL,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    estado INT NOT NULL DEFAULT 1,
    asignado_por VARCHAR(50) NOT NULL,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permiso_id) REFERENCES permisos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rol_permiso_sede (sede_id, rol_id, permiso_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de sesiones activas
CREATE TABLE sesiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    usuario_id INT NOT NULL,
    token_sesion VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(500) NULL,
    fecha_inicio DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_actividad DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_expiracion DATETIME NOT NULL,
    activa TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_token_sede (sede_id, token_sesion),
    INDEX idx_sesiones_usuario (usuario_id),
    INDEX idx_sesiones_activa (activa, fecha_expiracion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de intentos de acceso
CREATE TABLE intentos_acceso (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NULL,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent VARCHAR(500) NULL,
    exito TINYINT(1) NOT NULL,
    motivo_fallo VARCHAR(255) NULL,
    fecha_intento DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE SET NULL,
    INDEX idx_intentos_username (username, fecha_intento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 3. TABLA DE CARGOS (TEMPORAL - REFERENCIA)
-- =====================================================
CREATE TABLE cargos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    nombre_cargo VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) NULL,
    salario_base DECIMAL(10, 2) NULL,
    estado INT NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cargo_sede (sede_id, nombre_cargo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='TABLA TEMPORAL - SERÁ ELIMINADA';

-- =====================================================
-- 4. TABLAS DEL SISTEMA DE RIFAS
-- =====================================================

-- Tabla de configuración específica por sede
CREATE TABLE configuracion_sede (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    clave VARCHAR(100) NOT NULL,
    valor TEXT NULL,
    descripcion VARCHAR(255) NULL,
    tipo_dato VARCHAR(20) DEFAULT 'STRING' COMMENT 'STRING, INTEGER, DECIMAL, BOOLEAN, JSON',
    es_obligatorio TINYINT(1) DEFAULT 0,
    estado INT NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_config_sede (sede_id, clave),
    INDEX idx_config_sede (sede_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Configuraciones personalizables por sede (colores, textos, límites, etc.)';

-- Tabla de ubicaciones de rifa
CREATE TABLE ubicaciones_rifa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    nombre VARCHAR(200) NOT NULL COMMENT 'Lima Centro, Chiclayo, Arequipa, etc.',
    direccion VARCHAR(500) NULL,
    ciudad VARCHAR(100) NOT NULL,
    departamento_region VARCHAR(100) NULL,
    pais VARCHAR(100) NOT NULL,
    codigo_postal VARCHAR(20) NULL,
    telefono VARCHAR(15) NULL,
    email VARCHAR(100) NULL,
    
    -- Ubicación geográfica
    latitud DECIMAL(10, 8) NULL,
    longitud DECIMAL(11, 8) NULL,
    url_mapa VARCHAR(255) NULL,
    
    -- Información adicional
    descripcion TEXT NULL,
    horario_atencion VARCHAR(500) NULL,
    es_principal TINYINT(1) DEFAULT 0,
    
    -- Control
    estado INT NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    INDEX idx_ubicaciones_sede (sede_id),
    INDEX idx_ubicaciones_ciudad (ciudad)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Direcciones físicas donde se realizan las rifas';

-- Tabla de estados de ticket
CREATE TABLE estados_ticket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    nombre VARCHAR(50) NOT NULL COMMENT 'PENDIENTE, PAGADO, APROBADO, RECHAZADO, PARTICIPANDO, GANADOR, EXPIRADO',
    descripcion VARCHAR(255) NULL,
    color_hex VARCHAR(7) NULL COMMENT 'Color para UI: #FF0000',
    orden INT DEFAULT 0,
    estado INT NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_estado_sede (sede_id, nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de métodos de pago
CREATE TABLE metodos_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL COMMENT 'Transferencia, Yape, Plin, PayPal, etc.',
    descripcion TEXT NULL,
    requiere_comprobante TINYINT(1) DEFAULT 1,
    
    -- Información de la cuenta/método
    numero_cuenta VARCHAR(50) NULL,
    numero_cci VARCHAR(50) NULL,
    titular_cuenta VARCHAR(200) NULL,
    banco VARCHAR(100) NULL,
    tipo_cuenta VARCHAR(50) NULL COMMENT 'Ahorros, Corriente',
    
    -- Para pagos digitales
    numero_celular VARCHAR(15) NULL COMMENT 'Para Yape, Plin, etc.',
    email_cuenta VARCHAR(100) NULL COMMENT 'Para PayPal, etc.',
    qr_code_url VARCHAR(255) NULL COMMENT 'URL del código QR',
    
    -- Configuración
    instrucciones TEXT NULL COMMENT 'Instrucciones para el usuario',
    orden INT DEFAULT 0 COMMENT 'Orden de visualización',
    
    -- Control
    estado INT NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    INDEX idx_metodos_pago_sede (sede_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de categorías de premios
CREATE TABLE categorias_premios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL COMMENT 'Electrónica, Vehículos, Viajes, Dinero',
    descripcion TEXT NULL,
    icono VARCHAR(100) NULL COMMENT 'Clase de icono o URL',
    color_hex VARCHAR(7) NULL,
    orden INT DEFAULT 0,
    estado INT NOT NULL DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_categoria_sede (sede_id, nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de premios
CREATE TABLE premios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    categoria_id INT NULL,
    
    -- Información del premio
    codigo VARCHAR(50) NOT NULL COMMENT 'Código único del premio',
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT NULL,
    valor_estimado DECIMAL(12, 2) NULL COMMENT 'Valor del premio en moneda local',
    
    -- Recursos visuales
    imagen_principal VARCHAR(255) NULL,
    imagen_secundaria VARCHAR(255) NULL,
    galeria_imagenes TEXT NULL COMMENT 'JSON con URLs de imágenes adicionales',
    video_url VARCHAR(255) NULL,
    
    -- Características del premio
    marca VARCHAR(100) NULL,
    modelo VARCHAR(100) NULL,
    color VARCHAR(50) NULL,
    especificaciones TEXT NULL COMMENT 'Detalles técnicos, características',
    
    -- Información adicional
    terminos_condiciones TEXT NULL,
    restricciones TEXT NULL,
    
    -- Destacado y promoción
    es_destacado TINYINT(1) DEFAULT 0,
    orden_visualizacion INT DEFAULT 0,
    
    -- Control
    estado INT NOT NULL DEFAULT 1 COMMENT '1=Disponible, 0=No disponible, 2=Agotado',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (categoria_id) REFERENCES categorias_premios(id) ON DELETE SET NULL,
    UNIQUE KEY unique_codigo_premio_sede (sede_id, codigo),
    INDEX idx_premios_sede (sede_id),
    INDEX idx_premios_categoria (categoria_id),
    INDEX idx_premios_destacado (es_destacado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de rifas/sorteos
CREATE TABLE rifas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    premio_id INT NOT NULL,
    ubicacion_id INT NULL,
    
    -- Información de la rifa
    codigo VARCHAR(50) NOT NULL COMMENT 'Código único de la rifa',
    nombre VARCHAR(200) NOT NULL,
    descripcion TEXT NULL,
    
    -- Configuración del sorteo
    numero_intentos INT NOT NULL DEFAULT 5 COMMENT 'Cantidad de sorteos antes del ganador',
    intento_ganador INT NOT NULL DEFAULT 5 COMMENT 'En qué intento se determina el ganador',
    
    -- Precio y cupos
    precio_ticket DECIMAL(10, 2) NOT NULL,
    cantidad_maxima_tickets INT NULL COMMENT 'NULL = ilimitado',
    tickets_vendidos INT DEFAULT 0,
    cantidad_maxima_por_persona INT DEFAULT 1 COMMENT 'Máximo de tickets por participante',
    
    -- Fechas importantes
    fecha_inicio_venta DATETIME NOT NULL,
    fecha_fin_venta DATETIME NOT NULL,
    fecha_sorteo DATETIME NOT NULL,
    fecha_sorteo_realizado DATETIME NULL,
    
    -- Configuración del contador
    mostrar_contador TINYINT(1) DEFAULT 1,
    mostrar_participantes TINYINT(1) DEFAULT 1,
    mostrar_tickets_vendidos TINYINT(1) DEFAULT 1,
    
    -- Publicidad y promoción
    tipo_publicidad VARCHAR(50) NULL COMMENT 'Banner, Popup, Destacado',
    url_banner VARCHAR(255) NULL,
    texto_promocional TEXT NULL,
    
    -- Reglas y términos
    reglas_participacion TEXT NULL,
    terminos_condiciones TEXT NULL,
    
    -- Estado de la rifa
    estado VARCHAR(30) NOT NULL DEFAULT 'BORRADOR' 
        COMMENT 'BORRADOR, PUBLICADA, EN_VENTA, CERRADA, SORTEO_REALIZADO, FINALIZADA, CANCELADA',
    estado_activo INT DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
    
    -- Control
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (premio_id) REFERENCES premios(id) ON DELETE RESTRICT,
    FOREIGN KEY (ubicacion_id) REFERENCES ubicaciones_rifa(id) ON DELETE SET NULL,
    UNIQUE KEY unique_codigo_rifa_sede (sede_id, codigo),
    INDEX idx_rifas_sede (sede_id),
    INDEX idx_rifas_premio (premio_id),
    INDEX idx_rifas_estado (estado),
    INDEX idx_rifas_fechas (fecha_inicio_venta, fecha_fin_venta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de tickets (compras de participación)
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    rifa_id INT NOT NULL,
    estado_ticket_id INT NULL,
    
    -- Código único del ticket
    codigo_ticket VARCHAR(50) NOT NULL COMMENT 'Código único para validación',
    
    -- Información del participante (usuario final)
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    tipo_documento VARCHAR(20) NOT NULL COMMENT 'DNI, CE, Pasaporte',
    numero_documento VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    direccion VARCHAR(500) NULL,
    ciudad VARCHAR(100) NULL,
    pais VARCHAR(100) NULL,
    
    -- Información de compra
    precio_pagado DECIMAL(10, 2) NOT NULL,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_compra VARCHAR(45) NULL,
    
    -- Estado del ticket
    estado VARCHAR(30) NOT NULL DEFAULT 'PENDIENTE_PAGO' 
        COMMENT 'PENDIENTE_PAGO, PAGO_SUBIDO, VALIDANDO, APROBADO, RECHAZADO, PARTICIPANDO, GANADOR, EXPIRADO',
    
    -- Aprobación
    aprobado_por VARCHAR(50) NULL,
    fecha_aprobacion DATETIME NULL,
    rechazado_por VARCHAR(50) NULL,
    fecha_rechazo DATETIME NULL,
    motivo_rechazo TEXT NULL,
    
    -- Notificaciones
    notificado_compra TINYINT(1) DEFAULT 0,
    notificado_aprobacion TINYINT(1) DEFAULT 0,
    notificado_sorteo TINYINT(1) DEFAULT 0,
    
    -- Validación de ticket
    fecha_validez DATETIME NULL COMMENT 'Fecha hasta la cual el ticket es válido',
    validado TINYINT(1) DEFAULT 0,
    fecha_validacion DATETIME NULL,
    
    -- Control
    estado_activo INT DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (rifa_id) REFERENCES rifas(id) ON DELETE RESTRICT,
    FOREIGN KEY (estado_ticket_id) REFERENCES estados_ticket(id) ON DELETE SET NULL,
    UNIQUE KEY unique_codigo_ticket (codigo_ticket),
    INDEX idx_tickets_sede (sede_id),
    INDEX idx_tickets_rifa (rifa_id),
    INDEX idx_tickets_documento (numero_documento),
    INDEX idx_tickets_email (email),
    INDEX idx_tickets_estado (estado),
    INDEX idx_tickets_codigo (codigo_ticket)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de comprobantes de pago
CREATE TABLE comprobantes_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    ticket_id INT NOT NULL,
    metodo_pago_id INT NULL,
    
    -- Información del comprobante
    numero_operacion VARCHAR(100) NULL,
    monto DECIMAL(10, 2) NOT NULL,
    fecha_pago DATETIME NULL,
    
    -- Archivo del comprobante
    archivo_comprobante VARCHAR(255) NULL COMMENT 'URL o path del comprobante subido',
    tipo_archivo VARCHAR(10) NULL COMMENT 'jpg, png, pdf',
    tamano_archivo INT NULL COMMENT 'Tamaño en bytes',
    
    -- Información adicional
    banco_origen VARCHAR(100) NULL,
    cuenta_origen VARCHAR(50) NULL,
    titular_origen VARCHAR(200) NULL,
    observaciones TEXT NULL,
    
    -- Validación
    estado VARCHAR(30) NOT NULL DEFAULT 'PENDIENTE' 
        COMMENT 'PENDIENTE, VALIDANDO, APROBADO, RECHAZADO, INVALIDO',
    validado_por VARCHAR(50) NULL,
    fecha_validacion DATETIME NULL,
    motivo_rechazo TEXT NULL,
    
    -- Control
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (metodo_pago_id) REFERENCES metodos_pago(id) ON DELETE SET NULL,
    INDEX idx_comprobantes_sede (sede_id),
    INDEX idx_comprobantes_ticket (ticket_id),
    INDEX idx_comprobantes_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de participantes (tickets aprobados listos para sorteo)
CREATE TABLE participantes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    rifa_id INT NOT NULL,
    ticket_id INT NOT NULL,
    
    -- Número de participación
    numero_participacion INT NOT NULL COMMENT 'Número secuencial para el sorteo',
    
    -- Estado
    esta_activo TINYINT(1) DEFAULT 1,
    fue_seleccionado_intento TINYINT(1) DEFAULT 0 COMMENT 'Si fue seleccionado en algún intento',
    numero_intento_seleccionado INT NULL COMMENT 'En qué intento fue seleccionado',
    
    -- Control
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (rifa_id) REFERENCES rifas(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    UNIQUE KEY unique_ticket_rifa (rifa_id, ticket_id),
    INDEX idx_participantes_sede (sede_id),
    INDEX idx_participantes_rifa (rifa_id),
    INDEX idx_participantes_numero (rifa_id, numero_participacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de intentos de sorteo
CREATE TABLE intentos_sorteo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    rifa_id INT NOT NULL,
    participante_id INT NOT NULL,
    
    -- Información del intento
    numero_intento INT NOT NULL COMMENT 'Número del intento (1, 2, 3...)',
    es_ganador TINYINT(1) DEFAULT 0 COMMENT 'Si este intento determinó al ganador',
    
    -- Datos del sorteo
    fecha_intento DATETIME DEFAULT CURRENT_TIMESTAMP,
    numero_sorteado INT NOT NULL COMMENT 'Número aleatorio sorteado',
    hash_verificacion VARCHAR(255) NULL COMMENT 'Hash para verificar transparencia del sorteo',
    
    -- Metadatos
    ip_sorteo VARCHAR(45) NULL,
    realizado_por VARCHAR(50) NULL,
    
    -- Control
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (rifa_id) REFERENCES rifas(id) ON DELETE CASCADE,
    FOREIGN KEY (participante_id) REFERENCES participantes(id) ON DELETE CASCADE,
    INDEX idx_intentos_sede (sede_id),
    INDEX idx_intentos_rifa (rifa_id),
    INDEX idx_intentos_numero (rifa_id, numero_intento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de ganadores
CREATE TABLE ganadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    rifa_id INT NOT NULL,
    participante_id INT NOT NULL,
    ticket_id INT NOT NULL,
    intento_sorteo_id INT NOT NULL,
    
    -- Información del ganador
    nombres_completos VARCHAR(200) NOT NULL,
    numero_documento VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    
    -- Información del premio
    premio_nombre VARCHAR(200) NOT NULL,
    premio_valor DECIMAL(12, 2) NULL,
    
    -- Entrega del premio
    fecha_ganador DATETIME DEFAULT CURRENT_TIMESTAMP,
    premio_entregado TINYINT(1) DEFAULT 0,
    fecha_entrega DATETIME NULL,
    lugar_entrega VARCHAR(500) NULL,
    entregado_por VARCHAR(100) NULL,
    
    -- Documentación
    foto_entrega VARCHAR(255) NULL,
    documento_entrega VARCHAR(255) NULL COMMENT 'Acta de entrega',
    observaciones TEXT NULL,
    
    -- Notificaciones
    notificado_email TINYINT(1) DEFAULT 0,
    notificado_sms TINYINT(1) DEFAULT 0,
    fecha_notificacion DATETIME NULL,
    
    -- Publicación (opcional)
    publicar_ganador TINYINT(1) DEFAULT 1 COMMENT 'Si se publica en la web',
    mensaje_felicitacion TEXT NULL,
    
    -- Control
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (rifa_id) REFERENCES rifas(id) ON DELETE RESTRICT,
    FOREIGN KEY (participante_id) REFERENCES participantes(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (intento_sorteo_id) REFERENCES intentos_sorteo(id) ON DELETE CASCADE,
    UNIQUE KEY unique_ganador_rifa (rifa_id),
    INDEX idx_ganadores_sede (sede_id),
    INDEX idx_ganadores_rifa (rifa_id),
    INDEX idx_ganadores_publicar (publicar_ganador)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 5. TABLA DE AUDITORÍA
-- =====================================================
CREATE TABLE audit_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NULL,
    tabla_afectada VARCHAR(100) NOT NULL,
    registro_id INT NOT NULL,
    operacion VARCHAR(20) NOT NULL COMMENT 'INSERT, UPDATE, DELETE, LOGIN, LOGOUT',
    datos_anteriores TEXT NULL,
    datos_nuevos TEXT NULL,
    usuario_id VARCHAR(50) NULL,
    ip_address VARCHAR(45) NULL,
    user_agent VARCHAR(500) NULL,
    fecha_operacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE SET NULL,
    INDEX idx_audit_tabla (tabla_afectada, fecha_operacion),
    INDEX idx_audit_usuario (usuario_id, fecha_operacion),
    INDEX idx_audit_fecha (fecha_operacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- 6. DATOS INICIALES
-- =====================================================

-- Insertar sede principal (Perú - Lima)
INSERT INTO sedes (codigo, nombre, pais, moneda, simbolo_moneda, codigo_moneda, zona_horaria, es_principal, estado, creado_por) 
VALUES 
('PERU-01', 'Sede Principal Lima', 'Perú', 'Soles', 'S/.', 'PEN', 'America/Lima', 1, 1, 'SYSTEM'),
('COL-01', 'Sede Colombia', 'Colombia', 'Pesos', '$', 'COP', 'America/Bogota', 0, 1, 'SYSTEM'),
('CHI-01', 'Sede Chile', 'Chile', 'Pesos', '$', 'CLP', 'America/Santiago', 0, 1, 'SYSTEM');

-- Estados de ticket predefinidos
INSERT INTO estados_ticket (sede_id, nombre, descripcion, color_hex, orden, creado_por)
SELECT id, 'PENDIENTE', 'Pendiente de pago', '#FFA500', 1, 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PAGADO', 'Pago registrado', '#2196F3', 2, 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'APROBADO', 'Ticket aprobado', '#4CAF50', 3, 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'RECHAZADO', 'Ticket rechazado', '#F44336', 4, 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PARTICIPANDO', 'En sorteo', '#9C27B0', 5, 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'GANADOR', 'Ganador del sorteo', '#FFD700', 6, 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'EXPIRADO', 'Ticket expirado', '#9E9E9E', 7, 'SYSTEM' FROM sedes;

-- Roles predefinidos
INSERT INTO roles (sede_id, nombre, descripcion, nivel_acceso, creado_por)
SELECT id, 'SUPERADMIN', 'Administrador del sistema', 4, 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'ADMIN', 'Administrador de sede', 3, 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'VALIDADOR', 'Validador de pagos', 2, 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'OPERADOR', 'Operador de rifas', 2, 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'CONSULTA', 'Solo consulta', 1, 'SYSTEM' FROM sedes;

-- Permisos predefinidos
INSERT INTO permisos (sede_id, nombre, descripcion, modulo, accion, creado_por)
SELECT id, 'RIFAS_CREAR', 'Crear rifas', 'RIFAS', 'CREAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'RIFAS_EDITAR', 'Editar rifas', 'RIFAS', 'ACTUALIZAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'RIFAS_ELIMINAR', 'Eliminar rifas', 'RIFAS', 'ELIMINAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'RIFAS_VER', 'Ver rifas', 'RIFAS', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PREMIOS_CREAR', 'Crear premios', 'PREMIOS', 'CREAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PREMIOS_EDITAR', 'Editar premios', 'PREMIOS', 'ACTUALIZAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PAGOS_APROBAR', 'Aprobar pagos', 'PARTICIPANTES', 'APROBAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PAGOS_RECHAZAR', 'Rechazar pagos', 'PARTICIPANTES', 'RECHAZAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'SORTEO_REALIZAR', 'Realizar sorteos', 'RIFAS', 'SORTEAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'REPORTES_VER', 'Ver reportes', 'REPORTES', 'LEER', 'SYSTEM' FROM sedes;

-- Usuario administrador por defecto (password: admin123)
-- Nota: En producción usar un hash real y cambiar contraseña
INSERT INTO usuarios (sede_id, username, password_hash, email, primer_nombre, apellido_paterno, debe_cambiar_password, estado, creado_por)
SELECT 
    id, 
    'admin', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: admin123
    'admin@rifas.com', 
    'Administrador', 
    'Sistema', 
    1, 
    1, 
    'SYSTEM' 
FROM sedes 
WHERE codigo = 'PERU-01';

COMMIT;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================

