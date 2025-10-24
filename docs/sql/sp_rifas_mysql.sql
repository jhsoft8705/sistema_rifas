-- =====================================================
-- PROCEDIMIENTOS ALMACENADOS - SISTEMA DE RIFAS
-- MySQL Version
-- =====================================================

DELIMITER $$

-- =====================================================
-- 1. PROCEDIMIENTO DE LOGIN
-- =====================================================
DROP PROCEDURE IF EXISTS sp_Login$$
CREATE PROCEDURE sp_Login(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_ip_address VARCHAR(45),
    IN p_user_agent VARCHAR(500),
    IN p_sede_id INT
)
BEGIN
    DECLARE v_usuario_id INT DEFAULT NULL;
    DECLARE v_password_hash VARCHAR(255);
    DECLARE v_usuario_sede_id INT;
    DECLARE v_estado_usuario INT;
    DECLARE v_cuenta_bloqueada TINYINT(1);
    DECLARE v_intentos_fallidos INT;
    DECLARE v_fecha_expiracion_password DATETIME;
    DECLARE v_token_sesion VARCHAR(255);
    DECLARE v_fecha_expiracion DATETIME;
    DECLARE v_resultado TINYINT(1) DEFAULT 0;
    DECLARE v_mensaje VARCHAR(255) DEFAULT '';
    DECLARE v_nombre_completo VARCHAR(200) DEFAULT '';
    DECLARE v_sede_nombre VARCHAR(200) DEFAULT '';
    DECLARE v_rol_id INT DEFAULT NULL;
    DECLARE v_rol_nombre VARCHAR(50) DEFAULT '';
    
    -- Buscar usuario por username
    SELECT 
        id, password_hash, sede_id, estado, cuenta_bloqueada,
        intentos_fallidos, fecha_expiracion_password,
        CONCAT(primer_nombre, ' ', apellido_paterno)
    INTO
        v_usuario_id, v_password_hash, v_usuario_sede_id, v_estado_usuario,
        v_cuenta_bloqueada, v_intentos_fallidos, v_fecha_expiracion_password,
        v_nombre_completo
    FROM usuarios 
    WHERE username = p_username AND sede_id = p_sede_id
    LIMIT 1;
    
    -- Verificar usuario
    IF v_usuario_id IS NULL THEN
        INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
        VALUES (p_sede_id, p_username, p_ip_address, p_user_agent, 0, 'Usuario no encontrado');
        SET v_mensaje = 'Usuario no encontrado';
        
    ELSEIF v_cuenta_bloqueada = 1 THEN
        INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
        VALUES (p_sede_id, p_username, p_ip_address, p_user_agent, 0, 'Cuenta bloqueada');
        SET v_mensaje = 'Cuenta bloqueada';
        
    ELSEIF v_estado_usuario != 1 THEN
        INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
        VALUES (p_sede_id, p_username, p_ip_address, p_user_agent, 0, 'Usuario inactivo');
        SET v_mensaje = 'Usuario inactivo';
        
    ELSEIF v_password_hash != p_password THEN
        -- Incrementar intentos fallidos
        UPDATE usuarios 
        SET intentos_fallidos = intentos_fallidos + 1
        WHERE id = v_usuario_id;
        
        -- Bloquear cuenta si supera 5 intentos
        IF (v_intentos_fallidos + 1) >= 5 THEN
            UPDATE usuarios 
            SET cuenta_bloqueada = 1, fecha_bloqueo = NOW()
            WHERE id = v_usuario_id;
            SET v_mensaje = 'Contraseña incorrecta. Cuenta bloqueada por múltiples intentos fallidos';
        ELSE
            SET v_mensaje = 'Contraseña incorrecta';
        END IF;
        
        INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
        VALUES (p_sede_id, p_username, p_ip_address, p_user_agent, 0, 'Contraseña incorrecta');
        
    ELSE
        -- Login exitoso
        SET v_resultado = 1;
        SET v_mensaje = 'Login exitoso';
        
        -- Invalidar sesiones anteriores
        UPDATE sesiones 
        SET activa = 0
        WHERE usuario_id = v_usuario_id AND activa = 1;
        
        -- Resetear intentos fallidos
        UPDATE usuarios 
        SET intentos_fallidos = 0, cuenta_bloqueada = 0, 
            fecha_bloqueo = NULL, ultimo_acceso = NOW()
        WHERE id = v_usuario_id;
        
        -- Obtener información de la sede
        SELECT nombre INTO v_sede_nombre FROM sedes WHERE id = v_usuario_sede_id;
        
        -- Obtener rol del usuario
        SELECT r.id, r.nombre
        INTO v_rol_id, v_rol_nombre
        FROM usuario_roles ur
        INNER JOIN roles r ON ur.rol_id = r.id
        WHERE ur.usuario_id = v_usuario_id 
          AND ur.sede_id = v_usuario_sede_id
          AND ur.estado = 1
        ORDER BY r.nivel_acceso DESC
        LIMIT 1;
        
        -- Generar token de sesión
        SET v_token_sesion = UUID();
        SET v_fecha_expiracion = DATE_ADD(NOW(), INTERVAL 8 HOUR);
        
        -- Crear sesión
        INSERT INTO sesiones (sede_id, usuario_id, token_sesion, ip_address, user_agent, fecha_expiracion)
        VALUES (v_usuario_sede_id, v_usuario_id, v_token_sesion, p_ip_address, p_user_agent, v_fecha_expiracion);
        
        -- Registrar intento exitoso
        INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito)
        VALUES (p_sede_id, p_username, p_ip_address, p_user_agent, 1);
    END IF;
    
    -- Retornar resultado
    SELECT 
        v_resultado AS resultado,
        v_mensaje AS mensaje,
        v_usuario_id AS usuario_id,
        v_token_sesion AS token_sesion,
        v_fecha_expiracion AS fecha_expiracion,
        v_usuario_sede_id AS sede_id,
        v_sede_nombre AS sede_nombre,
        v_nombre_completo AS nombre_completo,
        v_rol_id AS rol_id,
        v_rol_nombre AS rol_nombre,
        v_fecha_expiracion_password AS debe_cambiar_password;
END$$

-- =====================================================
-- 2. PROCEDIMIENTO DE LOGOUT
-- =====================================================
DROP PROCEDURE IF EXISTS sp_Logout$$
CREATE PROCEDURE sp_Logout(
    IN p_token_sesion VARCHAR(255),
    IN p_ip_address VARCHAR(45)
)
BEGIN
    DECLARE v_usuario_id INT;
    DECLARE v_resultado TINYINT(1) DEFAULT 0;
    DECLARE v_mensaje VARCHAR(255) DEFAULT '';
    
    -- Buscar sesión activa
    SELECT usuario_id INTO v_usuario_id
    FROM sesiones 
    WHERE token_sesion = p_token_sesion AND activa = 1
    LIMIT 1;
    
    IF v_usuario_id IS NOT NULL THEN
        -- Marcar sesión como inactiva
        UPDATE sesiones 
        SET activa = 0, fecha_ultima_actividad = NOW()
        WHERE token_sesion = p_token_sesion AND activa = 1;
        
        SET v_resultado = 1;
        SET v_mensaje = 'Logout exitoso';
    ELSE
        SET v_mensaje = 'Sesión no encontrada o ya inactiva';
    END IF;
    
    SELECT v_resultado AS resultado, v_mensaje AS mensaje;
END$$

-- =====================================================
-- 3. PROCEDIMIENTO PARA VALIDAR SESIÓN
-- =====================================================
DROP PROCEDURE IF EXISTS sp_ValidarSesion$$
CREATE PROCEDURE sp_ValidarSesion(
    IN p_token_sesion VARCHAR(255)
)
BEGIN
    DECLARE v_usuario_id INT;
    DECLARE v_sede_id INT;
    DECLARE v_fecha_expiracion DATETIME;
    DECLARE v_activa TINYINT(1);
    DECLARE v_username VARCHAR(50);
    DECLARE v_resultado TINYINT(1) DEFAULT 0;
    DECLARE v_mensaje VARCHAR(255) DEFAULT '';
    
    -- Buscar sesión
    SELECT 
        s.usuario_id, s.sede_id, s.fecha_expiracion, s.activa, u.username
    INTO
        v_usuario_id, v_sede_id, v_fecha_expiracion, v_activa, v_username
    FROM sesiones s
    INNER JOIN usuarios u ON s.usuario_id = u.id
    WHERE s.token_sesion = p_token_sesion
    LIMIT 1;
    
    IF v_usuario_id IS NULL THEN
        SET v_mensaje = 'Token de sesión no encontrado';
    ELSEIF v_activa = 0 THEN
        SET v_mensaje = 'Sesión inactiva';
    ELSEIF v_fecha_expiracion < NOW() THEN
        -- Marcar sesión como expirada
        UPDATE sesiones 
        SET activa = 0, fecha_ultima_actividad = NOW()
        WHERE token_sesion = p_token_sesion;
        SET v_mensaje = 'Sesión expirada';
    ELSE
        -- Sesión válida, actualizar última actividad
        UPDATE sesiones 
        SET fecha_ultima_actividad = NOW()
        WHERE token_sesion = p_token_sesion;
        
        SET v_resultado = 1;
        SET v_mensaje = 'Sesión válida';
    END IF;
    
    SELECT 
        v_resultado AS resultado,
        v_mensaje AS mensaje,
        v_usuario_id AS usuario_id,
        v_sede_id AS sede_id,
        v_username AS username,
        v_fecha_expiracion AS fecha_expiracion;
END$$

-- =====================================================
-- 4. PROCEDIMIENTO PARA GENERAR CÓDIGO DE TICKET
-- =====================================================
DROP PROCEDURE IF EXISTS sp_GenerarCodigoTicket$$
CREATE PROCEDURE sp_GenerarCodigoTicket(
    IN p_sede_id INT,
    OUT p_codigo_ticket VARCHAR(50)
)
BEGIN
    DECLARE v_existe INT DEFAULT 1;
    DECLARE v_contador INT DEFAULT 0;
    DECLARE v_prefijo VARCHAR(10);
    
    -- Obtener código de sede
    SELECT codigo INTO v_prefijo FROM sedes WHERE id = p_sede_id LIMIT 1;
    SET v_prefijo = SUBSTRING(v_prefijo, 1, 4);
    
    -- Generar código único
    WHILE v_existe > 0 AND v_contador < 100 DO
        SET p_codigo_ticket = CONCAT(
            v_prefijo, '-',
            DATE_FORMAT(NOW(), '%Y%m%d'), '-',
            LPAD(FLOOR(RAND() * 999999), 6, '0')
        );
        
        SELECT COUNT(*) INTO v_existe FROM tickets WHERE codigo_ticket = p_codigo_ticket;
        SET v_contador = v_contador + 1;
    END WHILE;
END$$

-- =====================================================
-- 5. PROCEDIMIENTO PARA REGISTRAR COMPRA DE TICKET
-- =====================================================
DROP PROCEDURE IF EXISTS sp_RegistrarCompraTicket$$
CREATE PROCEDURE sp_RegistrarCompraTicket(
    IN p_sede_id INT,
    IN p_rifa_id INT,
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_tipo_documento VARCHAR(20),
    IN p_numero_documento VARCHAR(20),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_direccion VARCHAR(500),
    IN p_ciudad VARCHAR(100),
    IN p_pais VARCHAR(100),
    IN p_precio_pagado DECIMAL(10,2),
    IN p_ip_compra VARCHAR(45),
    OUT p_resultado TINYINT(1),
    OUT p_mensaje VARCHAR(255),
    OUT p_codigo_ticket VARCHAR(50)
)
BEGIN
    DECLARE v_rifa_estado VARCHAR(30);
    DECLARE v_ticket_id INT;
    DECLARE v_fecha_validez DATETIME;
    DECLARE v_dias_validez INT;
    
    SET p_resultado = 0;
    
    -- Verificar que la rifa existe y está en venta
    SELECT estado INTO v_rifa_estado FROM rifas WHERE id = p_rifa_id AND sede_id = p_sede_id LIMIT 1;
    
    IF v_rifa_estado IS NULL THEN
        SET p_mensaje = 'La rifa no existe';
    ELSEIF v_rifa_estado NOT IN ('PUBLICADA', 'EN_VENTA') THEN
        SET p_mensaje = 'La rifa no está disponible para compra';
    ELSE
        -- Generar código de ticket
        CALL sp_GenerarCodigoTicket(p_sede_id, p_codigo_ticket);
        
        -- Calcular fecha de validez
        SELECT dias_validez_ticket INTO v_dias_validez FROM sedes WHERE id = p_sede_id;
        SET v_fecha_validez = DATE_ADD(NOW(), INTERVAL v_dias_validez DAY);
        
        -- Insertar ticket
        INSERT INTO tickets (
            sede_id, rifa_id, codigo_ticket, nombres, apellidos,
            tipo_documento, numero_documento, email, telefono,
            direccion, ciudad, pais, precio_pagado, ip_compra,
            estado, fecha_validez
        ) VALUES (
            p_sede_id, p_rifa_id, p_codigo_ticket, p_nombres, p_apellidos,
            p_tipo_documento, p_numero_documento, p_email, p_telefono,
            p_direccion, p_ciudad, p_pais, p_precio_pagado, p_ip_compra,
            'PENDIENTE_PAGO', v_fecha_validez
        );
        
        SET v_ticket_id = LAST_INSERT_ID();
        SET p_resultado = 1;
        SET p_mensaje = 'Ticket registrado exitosamente';
        
        -- Registrar en auditoría
        INSERT INTO audit_logs (sede_id, tabla_afectada, registro_id, operacion, usuario_id)
        VALUES (p_sede_id, 'tickets', v_ticket_id, 'INSERT', p_email);
    END IF;
END$$

-- =====================================================
-- 6. PROCEDIMIENTO PARA VALIDAR TICKET
-- =====================================================
DROP PROCEDURE IF EXISTS sp_ValidarTicket$$
CREATE PROCEDURE sp_ValidarTicket(
    IN p_codigo_ticket VARCHAR(50)
)
BEGIN
    DECLARE v_existe INT;
    
    SELECT COUNT(*) INTO v_existe FROM tickets WHERE codigo_ticket = p_codigo_ticket;
    
    IF v_existe > 0 THEN
        SELECT 
            t.id,
            t.codigo_ticket,
            t.nombres,
            t.apellidos,
            t.numero_documento,
            t.email,
            t.telefono,
            t.estado,
            t.fecha_compra,
            t.fecha_validez,
            r.nombre AS rifa_nombre,
            r.fecha_sorteo,
            p.nombre AS premio_nombre,
            s.nombre AS sede_nombre,
            1 AS valido,
            'Ticket encontrado' AS mensaje
        FROM tickets t
        INNER JOIN rifas r ON t.rifa_id = r.id
        INNER JOIN premios p ON r.premio_id = p.id
        INNER JOIN sedes s ON t.sede_id = s.id
        WHERE t.codigo_ticket = p_codigo_ticket
        LIMIT 1;
    ELSE
        SELECT 
            NULL AS id,
            p_codigo_ticket AS codigo_ticket,
            0 AS valido,
            'Ticket no encontrado' AS mensaje;
    END IF;
END$$

-- =====================================================
-- 7. PROCEDIMIENTO PARA APROBAR TICKET
-- =====================================================
DROP PROCEDURE IF EXISTS sp_AprobarTicket$$
CREATE PROCEDURE sp_AprobarTicket(
    IN p_ticket_id INT,
    IN p_aprobado_por VARCHAR(50),
    OUT p_resultado TINYINT(1),
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_rifa_id INT;
    DECLARE v_ticket_estado VARCHAR(30);
    DECLARE v_numero_participacion INT;
    
    SET p_resultado = 0;
    
    -- Obtener información del ticket
    SELECT rifa_id, estado INTO v_rifa_id, v_ticket_estado 
    FROM tickets 
    WHERE id = p_ticket_id
    LIMIT 1;
    
    IF v_ticket_estado IS NULL THEN
        SET p_mensaje = 'Ticket no encontrado';
    ELSEIF v_ticket_estado != 'PAGO_SUBIDO' THEN
        SET p_mensaje = 'El ticket no está en estado válido para aprobación';
    ELSE
        -- Actualizar ticket
        UPDATE tickets 
        SET estado = 'APROBADO',
            aprobado_por = p_aprobado_por,
            fecha_aprobacion = NOW()
        WHERE id = p_ticket_id;
        
        -- Obtener próximo número de participación
        SELECT COALESCE(MAX(numero_participacion), 0) + 1 
        INTO v_numero_participacion
        FROM participantes 
        WHERE rifa_id = v_rifa_id;
        
        -- Registrar como participante
        INSERT INTO participantes (sede_id, rifa_id, ticket_id, numero_participacion)
        SELECT sede_id, rifa_id, id, v_numero_participacion
        FROM tickets
        WHERE id = p_ticket_id;
        
        -- Actualizar contador de tickets vendidos
        UPDATE rifas 
        SET tickets_vendidos = tickets_vendidos + 1
        WHERE id = v_rifa_id;
        
        SET p_resultado = 1;
        SET p_mensaje = 'Ticket aprobado exitosamente';
    END IF;
END$$

-- =====================================================
-- 8. PROCEDIMIENTO PARA RECHAZAR TICKET
-- =====================================================
DROP PROCEDURE IF EXISTS sp_RechazarTicket$$
CREATE PROCEDURE sp_RechazarTicket(
    IN p_ticket_id INT,
    IN p_rechazado_por VARCHAR(50),
    IN p_motivo_rechazo TEXT,
    OUT p_resultado TINYINT(1),
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_ticket_estado VARCHAR(30);
    
    SET p_resultado = 0;
    
    -- Obtener estado del ticket
    SELECT estado INTO v_ticket_estado FROM tickets WHERE id = p_ticket_id LIMIT 1;
    
    IF v_ticket_estado IS NULL THEN
        SET p_mensaje = 'Ticket no encontrado';
    ELSE
        -- Actualizar ticket
        UPDATE tickets 
        SET estado = 'RECHAZADO',
            rechazado_por = p_rechazado_por,
            fecha_rechazo = NOW(),
            motivo_rechazo = p_motivo_rechazo
        WHERE id = p_ticket_id;
        
        SET p_resultado = 1;
        SET p_mensaje = 'Ticket rechazado';
    END IF;
END$$

-- =====================================================
-- 9. PROCEDIMIENTO PARA LISTAR RIFAS ACTIVAS
-- =====================================================
DROP PROCEDURE IF EXISTS sp_ListarRifasActivas$$
CREATE PROCEDURE sp_ListarRifasActivas(
    IN p_sede_id INT
)
BEGIN
    SELECT 
        r.id,
        r.codigo,
        r.nombre,
        r.descripcion,
        r.precio_ticket,
        r.fecha_inicio_venta,
        r.fecha_fin_venta,
        r.fecha_sorteo,
        r.tickets_vendidos,
        r.cantidad_maxima_tickets,
        r.estado,
        p.nombre AS premio_nombre,
        p.imagen_principal AS premio_imagen,
        p.valor_estimado AS premio_valor,
        u.nombre AS ubicacion_nombre,
        DATEDIFF(r.fecha_sorteo, NOW()) AS dias_restantes
    FROM rifas r
    INNER JOIN premios p ON r.premio_id = p.id
    LEFT JOIN ubicaciones_rifa u ON r.ubicacion_id = u.id
    WHERE r.sede_id = p_sede_id
      AND r.estado_activo = 1
      AND r.estado IN ('PUBLICADA', 'EN_VENTA')
      AND r.fecha_fin_venta > NOW()
    ORDER BY r.fecha_sorteo ASC;
END$$

-- =====================================================
-- 10. PROCEDIMIENTO PARA OBTENER ESTADÍSTICAS DE RIFA
-- =====================================================
DROP PROCEDURE IF EXISTS sp_EstadisticasRifa$$
CREATE PROCEDURE sp_EstadisticasRifa(
    IN p_rifa_id INT
)
BEGIN
    SELECT 
        r.id,
        r.nombre,
        r.codigo,
        r.estado,
        r.precio_ticket,
        r.tickets_vendidos,
        r.cantidad_maxima_tickets,
        COUNT(DISTINCT t.id) AS total_tickets,
        COUNT(DISTINCT CASE WHEN t.estado = 'APROBADO' THEN t.id END) AS tickets_aprobados,
        COUNT(DISTINCT CASE WHEN t.estado = 'PENDIENTE_PAGO' THEN t.id END) AS tickets_pendientes,
        COUNT(DISTINCT CASE WHEN t.estado = 'PAGO_SUBIDO' THEN t.id END) AS tickets_validando,
        COUNT(DISTINCT CASE WHEN t.estado = 'RECHAZADO' THEN t.id END) AS tickets_rechazados,
        COUNT(DISTINCT p.id) AS total_participantes,
        SUM(t.precio_pagado) AS total_recaudado,
        DATEDIFF(r.fecha_sorteo, NOW()) AS dias_restantes
    FROM rifas r
    LEFT JOIN tickets t ON r.id = t.rifa_id
    LEFT JOIN participantes p ON r.id = p.rifa_id
    WHERE r.id = p_rifa_id
    GROUP BY r.id;
END$$

DELIMITER ;

-- =====================================================
-- FIN DE PROCEDIMIENTOS ALMACENADOS
-- =====================================================

