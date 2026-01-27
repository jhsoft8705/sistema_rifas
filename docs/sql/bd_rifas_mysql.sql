-- =====================================================
-- SISTEMA DE RIFAS MULTISEDE - REFACTORIZADO
-- Base de datos MySQL optimizada para el flujo:
-- 1. Premios por categoría → asociados a rifas
-- 2. Personas compran números de rifa
-- 3. Se generan tickets (compras)
-- 4. Se suben comprobantes de pago
-- 5. Operador valida comprobantes → cambia estado
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar tablas existentes
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
DROP TABLE IF EXISTS numeros_rifa;
DROP TABLE IF EXISTS tickets;
DROP TABLE IF EXISTS personas;
DROP TABLE IF EXISTS rifas_premios;
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
    
    -- Control
    estado INT NOT NULL DEFAULT 1 COMMENT '1=Activo, 0=Inactivo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    INDEX idx_sede_estado (estado),
    INDEX idx_sede_pais (pais)
) COMMENT='Tabla de sedes por país';

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
);

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
);

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
);

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
);

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
);

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
);

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
);

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
);

-- =====================================================
-- 3. TABLAS DEL SISTEMA DE RIFAS (CORE)
-- =====================================================

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
);

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
    galeria_imagenes TEXT NULL COMMENT 'JSON con rutas de imágenes adicionales',
    video_url VARCHAR(255) NULL,
    
    -- Características del premio
    marca VARCHAR(100) NULL,
    modelo VARCHAR(100) NULL,
    color VARCHAR(50) NULL,
    especificaciones TEXT NULL COMMENT 'Detalles técnicos, características',
    
    -- Información adicional
    terminos_condiciones TEXT NULL COMMENT 'Condiciones de entrega o uso del premio',
    restricciones TEXT NULL COMMENT 'Restricciones o limitaciones del premio',
    
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
);

-- Tabla de rifas/sorteos
CREATE TABLE rifas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    premio_id INT NULL COMMENT 'Premio principal (opcional, puede tener múltiples)',
    
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
    
    -- ====== SISTEMA DE NUMERACIÓN DE BOLETOS ======
    -- Control de números de boletos
    usa_numeracion_boletos TINYINT(1) DEFAULT 1 COMMENT 'Si usa sistema de números de boletos',
    tipo_numeracion VARCHAR(20) DEFAULT 'CORRELATIVO' COMMENT 'CORRELATIVO, ALEATORIO, PERSONALIZADO',
    numero_inicial INT NOT NULL DEFAULT 1 COMMENT 'Número inicial del rango (ej: 1, 100, 1000)',
    numero_final INT NOT NULL DEFAULT 1000 COMMENT 'Número final del rango (ej: 500, 9999)',
    cantidad_digitos INT DEFAULT 4 COMMENT 'Dígitos para formato: 4=0001, 5=00001',
    prefijo_numero VARCHAR(20) NULL COMMENT 'Prefijo opcional (ej: RIFA-, BOL-)',
    sufijo_numero VARCHAR(20) NULL COMMENT 'Sufijo opcional (ej: -2025, -A)',
    
    -- Configuración de selección
    permitir_seleccion_numero TINYINT(1) DEFAULT 1 COMMENT 'Permitir que usuario elija número',
    asignacion_automatica TINYINT(1) DEFAULT 1 COMMENT 'Asignar número automático si no elige',
    mostrar_numeros_disponibles TINYINT(1) DEFAULT 1 COMMENT 'Mostrar números disponibles en web',
    
    -- Bloqueo de números
    numeros_bloqueados TEXT NULL COMMENT 'JSON con números bloqueados/reservados',
    numeros_especiales TEXT NULL COMMENT 'JSON con números especiales (ej: promocionales, regalos)',
    
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
    FOREIGN KEY (premio_id) REFERENCES premios(id) ON DELETE SET NULL,
    UNIQUE KEY unique_codigo_rifa_sede (sede_id, codigo),
    INDEX idx_rifas_sede (sede_id),
    INDEX idx_rifas_premio (premio_id),
    INDEX idx_rifas_estado (estado),
    INDEX idx_rifas_fechas (fecha_inicio_venta, fecha_fin_venta)
);

-- Tabla relación rifas-premios (múltiples premios por sorteo)
CREATE TABLE rifas_premios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    rifa_id INT NOT NULL,
    premio_id INT NOT NULL,
    orden INT DEFAULT 1 COMMENT 'Orden de entrega del premio',
    es_principal TINYINT(1) DEFAULT 0 COMMENT 'Indica si es el premio principal del sorteo',
    titulo VARCHAR(200) NULL COMMENT 'Título personalizado para el premio dentro de la rifa',
    descripcion TEXT NULL,
    cantidad INT DEFAULT 1 COMMENT 'Cantidad de premios iguales',
    valor_estimado DECIMAL(12, 2) NULL,
    estado INT DEFAULT 1 COMMENT '1=Activo,0=Inactivo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (rifa_id) REFERENCES rifas(id) ON DELETE CASCADE,
    FOREIGN KEY (premio_id) REFERENCES premios(id) ON DELETE CASCADE,
    UNIQUE KEY unique_rifa_premio (rifa_id, premio_id),
    INDEX idx_rifas_premios_rifa (rifa_id),
    INDEX idx_rifas_premios_premio (premio_id),
    INDEX idx_rifas_premios_principal (rifa_id, es_principal)
) COMMENT='Premios asociados a cada rifa';

-- Tabla de personas/clientes (información única por número de documento)
CREATE TABLE personas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    
    -- Información personal
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    tipo_documento VARCHAR(20) NOT NULL COMMENT 'DNI, CE, Pasaporte',
    numero_documento VARCHAR(20) NOT NULL,
    email VARCHAR(100) NULL,
    telefono VARCHAR(15) NULL,
    direccion VARCHAR(500) NULL,
    ciudad VARCHAR(100) NULL,
    pais VARCHAR(100) NULL,
    
    -- Control
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_persona_documento (sede_id, tipo_documento, numero_documento),
    INDEX idx_personas_sede (sede_id),
    INDEX idx_personas_documento (numero_documento),
    INDEX idx_personas_email (email)
) COMMENT='Información única de personas/clientes por número de documento';

-- Tabla de tickets (compras de participación)
-- REFACTORIZADA: Solo referencia a persona_id, sin duplicar datos
-- IMPORTANTE: Se crea antes de numeros_rifa porque numeros_rifa tiene FK a tickets
CREATE TABLE tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    rifa_id INT NOT NULL,
    
    -- Código único del ticket
    codigo_ticket VARCHAR(50) NOT NULL COMMENT 'Código único para validación',
    
    -- Referencia a persona (cliente) - REFACTORIZADO: solo referencia
    persona_id INT NOT NULL COMMENT 'Referencia a la tabla personas',
    
    -- ====== NÚMERO DE BOLETO ======
    numero_boleto VARCHAR(50) NULL COMMENT 'Número del boleto asignado (ej: 0001, RIFA-0523)',
    numero_boleto_entero INT NULL COMMENT 'Número entero para búsquedas y ordenamiento',
    numero_seleccionado_usuario TINYINT(1) DEFAULT 0 COMMENT 'Si el usuario eligió el número o fue asignado',
    
    -- Información de compra
    precio_pagado DECIMAL(10, 2) NOT NULL,
    fecha_compra DATETIME DEFAULT CURRENT_TIMESTAMP,
    ip_compra VARCHAR(45) NULL,
    canal_venta VARCHAR(20) DEFAULT 'WEB' COMMENT 'WEB, FISICO, TELEFONO, WHATSAPP, ADMINISTRATIVO',
    vendedor_id INT NULL COMMENT 'ID usuario que realizó la venta (para venta física)',
    
    -- Estado del ticket (simplificado con ENUM)
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
    FOREIGN KEY (persona_id) REFERENCES personas(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_codigo_ticket (codigo_ticket),
    UNIQUE KEY unique_numero_boleto_rifa (rifa_id, numero_boleto_entero),
    INDEX idx_tickets_sede (sede_id),
    INDEX idx_tickets_rifa (rifa_id),
    INDEX idx_tickets_persona (persona_id),
    INDEX idx_tickets_estado (estado),
    INDEX idx_tickets_codigo (codigo_ticket),
    INDEX idx_tickets_numero_boleto (numero_boleto),
    INDEX idx_tickets_canal (canal_venta)
);

-- Tabla de números de rifa/boletos
-- IMPORTANTE: Se crea después de tickets porque tiene FK a tickets
CREATE TABLE numeros_rifa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    rifa_id INT NOT NULL,
    ticket_id INT NULL COMMENT 'NULL si está disponible, ID si está vendido',
    
    -- Información del número
    numero_entero INT NOT NULL COMMENT 'Número entero del boleto (ej: 523)',
    numero_formateado VARCHAR(50) NOT NULL COMMENT 'Número con formato (ej: RIFA-0523, 0523)',
    
    -- Estado del número
    estado VARCHAR(20) NOT NULL DEFAULT 'DISPONIBLE' 
        COMMENT 'DISPONIBLE, RESERVADO, VENDIDO, BLOQUEADO',
    motivo_bloqueo VARCHAR(255) NULL COMMENT 'Razón si está bloqueado',
    
    -- Reserva temporal (para proceso de compra online)
    reservado_hasta DATETIME NULL COMMENT 'Fecha hasta que está reservado (timeout compra)',
    reservado_por_sesion VARCHAR(255) NULL COMMENT 'ID de sesión que reservó',
    
    -- Fechas
    fecha_reserva DATETIME NULL,
    fecha_venta DATETIME NULL,
    fecha_bloqueo DATETIME NULL,
    
    -- Control
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (rifa_id) REFERENCES rifas(id) ON DELETE CASCADE,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE SET NULL,
    UNIQUE KEY unique_numero_rifa (rifa_id, numero_entero),
    INDEX idx_numeros_sede (sede_id),
    INDEX idx_numeros_rifa (rifa_id),
    INDEX idx_numeros_estado (estado),
    INDEX idx_numeros_disponibles (rifa_id, estado)
);

-- Tabla de comprobantes de pago
CREATE TABLE comprobantes_pago (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    ticket_id INT NOT NULL,
    metodo_pago_id INT NULL COMMENT 'Opcional: método de pago usado',
    
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
    
    -- Validación (estado simplificado)
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
    INDEX idx_comprobantes_sede (sede_id),
    INDEX idx_comprobantes_ticket (ticket_id),
    INDEX idx_comprobantes_estado (estado)
);



-- Tabla de historial de intentos (para contar intentos antes del ganador)
CREATE TABLE IF NOT EXISTS intentos_juego (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rifa_id INT NOT NULL,
    rifa_premio_id INT NOT NULL,
    persona_id INT NOT NULL,
    numero_id INT NULL COMMENT 'ID del número seleccionado (nuevo: selección por número)',
    intento_numero INT NOT NULL COMMENT 'Número de intento (1, 2, 3, etc.)',
    es_ganador TINYINT(1) DEFAULT 0,
    fecha_intento DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    jugado_por VARCHAR(50) NULL,
    FOREIGN KEY (rifa_id) REFERENCES rifas(id) ON DELETE CASCADE,
    FOREIGN KEY (rifa_premio_id) REFERENCES rifas_premios(id) ON DELETE CASCADE,
    FOREIGN KEY (persona_id) REFERENCES personas(id) ON DELETE RESTRICT,
    FOREIGN KEY (numero_id) REFERENCES numeros_rifa(id) ON DELETE SET NULL,
    INDEX idx_intentos_rifa_premio (rifa_id, rifa_premio_id),
    INDEX idx_intentos_persona (persona_id),
    INDEX idx_intentos_numero (numero_id)
) COMMENT='Historial de intentos de juego por premio';

-- Tabla de ganadores (si no existe)
-- Tabla de ganadores (si no existe)
CREATE TABLE IF NOT EXISTS ganadores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sede_id INT NOT NULL,
    rifa_id INT NOT NULL,
    rifa_premio_id INT NOT NULL COMMENT 'ID de rifas_premios (premio específico de la rifa)',
    premio_id INT NOT NULL COMMENT 'ID del premio ganado',
    numero_id INT NULL COMMENT 'Número ganador específico' ,
    persona_id INT NOT NULL COMMENT 'Persona ganadora',
    ticket_id INT NULL COMMENT 'Ticket ganador (opcional, puede haber múltiples)',
    
    -- Información del ganador
    nombre_completo VARCHAR(200) NOT NULL,
    documento_completo VARCHAR(50) NOT NULL,
    email VARCHAR(100) NULL,
    telefono VARCHAR(15) NULL,
    
    -- Dirección de envío (opcional)
    direccion_envio VARCHAR(500) NULL COMMENT 'Dirección para envío del premio',
    ciudad_envio VARCHAR(100) NULL,
    pais_envio VARCHAR(100) NULL,
    
    -- Publicación
    publicar_web TINYINT(1) DEFAULT 0 COMMENT 'Si se publica el ganador en la web',
    
    -- Información del juego
    intento_ganador INT NOT NULL COMMENT 'En qué intento ganó',
    fecha_ganador DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    jugado_por VARCHAR(50) NULL COMMENT 'Usuario que ejecutó el juego',
    
    -- Control
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    creado_por VARCHAR(50) NULL,
    modificado_por VARCHAR(50) NULL,
    
    FOREIGN KEY (sede_id) REFERENCES sedes(id) ON DELETE CASCADE,
    FOREIGN KEY (rifa_id) REFERENCES rifas(id) ON DELETE CASCADE,
    FOREIGN KEY (rifa_premio_id) REFERENCES rifas_premios(id) ON DELETE CASCADE,
    FOREIGN KEY (premio_id) REFERENCES premios(id) ON DELETE RESTRICT,
    FOREIGN KEY (persona_id) REFERENCES personas(id) ON DELETE RESTRICT,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE SET NULL,
    FOREIGN KEY (numero_id) REFERENCES numeros_rifa(id) ON DELETE SET NULL,
    INDEX idx_ganadores_rifa (rifa_id),
    INDEX idx_ganadores_premio (premio_id),
    INDEX idx_ganadores_persona (persona_id),
    INDEX idx_ganadores_numero (numero_id),
    INDEX idx_ganadores_publicar (publicar_web)
) COMMENT='Ganadores de premios en rifas';

-- =====================================================
-- 4. DATOS INICIALES
-- =====================================================

-- Insertar sede principal (Perú - Lima)
INSERT INTO sedes (codigo, nombre, pais, moneda, simbolo_moneda, codigo_moneda, zona_horaria, es_principal, estado, creado_por) 
VALUES 
('PERU-01', 'Sede Principal Lima', 'Perú', 'Soles', 'S/.', 'PEN', 'America/Lima', 1, 1, 'SYSTEM');

-- Roles predefinidos
INSERT INTO roles (sede_id, nombre, descripcion, nivel_acceso, creado_por) VALUES
(1, 'SUPERADMIN', 'Administrador del sistema', 4, 'SYSTEM'),
(1, 'ADMIN', 'Administrador de sede', 3, 'SYSTEM'),
(1, 'VALIDADOR', 'Validador de pagos', 2, 'SYSTEM'),
(1, 'OPERADOR', 'Operador de rifas', 2, 'SYSTEM'),
(1, 'CONSULTA', 'Solo consulta', 1, 'SYSTEM');

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
SELECT id, 'REPORTES_VER', 'Ver reportes', 'REPORTES', 'LEER', 'SYSTEM' FROM sedes;

-- Usuario administrador por defecto (password: admin123)
INSERT INTO usuarios (sede_id, username, password_hash, email, primer_nombre, apellido_paterno, debe_cambiar_password, estado, creado_por)
VALUES
    (1, 'zed_admin', '$2y$10$9rR0ZrEaFxR29HsrlaobmeB8g34E/mAajSvBjnwpYs3rO6lGzB5cG', 'zed_admin@rifas.com', 'zed_admin', 'Administrador', 1, 1, 'SYSTEM');

-- Asignar rol SUPERADMIN al usuario zed_admin
INSERT INTO usuario_roles (sede_id, usuario_id, rol_id, asignado_por)
SELECT 
    1,
    u.id,
    r.id,
    'SYSTEM'
FROM usuarios u
CROSS JOIN roles r
WHERE u.username = 'zed_admin'
  AND u.sede_id = 1
  AND r.nombre = 'SUPERADMIN'
  AND r.sede_id = 1;

-- =====================================================
-- RESUMEN DE REFACTORIZACIÓN
-- =====================================================
-- TABLAS ELIMINADAS (innecesarias para el flujo básico):
-- - participantes (redundante con tickets aprobados)
-- - intentos_sorteo (solo si hay sistema de sorteo múltiple)
-- - ganadores (solo si hay sistema de sorteo)
-- - ubicaciones_rifa (no mencionada en flujo)
-- - estados_ticket (simplificado con ENUM en tickets)
-- - metodos_pago (opcional, puede agregarse después si se necesita)
-- - configuracion_sede (no mencionada en flujo básico)
-- - audit_logs (útil pero no crítico para flujo básico)
--
-- CAMBIOS PRINCIPALES:
-- 1. tickets ahora solo referencia persona_id (sin duplicar datos)
-- 2. Estado simplificado con VARCHAR/ENUM en lugar de tabla separada
-- 3. Estructura optimizada para el flujo: Premios → Rifas → Personas → Tickets → Comprobantes
-- =====================================================
