-- =============================================
-- STORED PROCEDURES PARA GESTIÓN DE TICKETS Y COMPROBANTES
-- Basado en la estructura definida en docs/sql/bd_rifas_mysql.sql
-- =============================================

DELIMITER //

-- ==========================================================
-- 1. CREAR TICKET (COMPRA DE USUARIO FINAL)
-- ==========================================================
DROP PROCEDURE IF EXISTS register_ticket //
CREATE PROCEDURE register_ticket (
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
    IN p_cantidad_tickets INT,
    IN p_numeros_seleccionados TEXT,
    IN p_ip_compra VARCHAR(45),
    IN p_canal_venta VARCHAR(20),
    IN p_estado_inicial VARCHAR(30),
    OUT p_ticket_id INT,
    OUT p_codigo_ticket VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_numero_disponible INT;
    DECLARE v_numero_formateado VARCHAR(50);
    DECLARE v_numero_entero INT;
    DECLARE v_numero_id INT;
    DECLARE v_contador INT DEFAULT 0;
    DECLARE v_numero_seleccionado INT;
    DECLARE v_codigo VARCHAR(50);
    DECLARE v_prefijo VARCHAR(20);
    DECLARE v_sufijo VARCHAR(20);
    DECLARE v_digitos INT;
    DECLARE v_numero_inicial INT;
    DECLARE v_numero_final INT;
    DECLARE v_cantidad_maxima_por_persona INT;
    DECLARE v_tickets_existentes INT;
    DECLARE v_numeros_texto TEXT;
    DECLARE v_numero_texto VARCHAR(20);
    DECLARE v_posicion INT DEFAULT 1;
    DECLARE v_posicion_siguiente INT;
    DECLARE v_estado_inicial VARCHAR(30);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_ticket_id = NULL;
        SET p_codigo_ticket = NULL;
        SET p_mensaje = 'Error al registrar el ticket';
    END;

    START TRANSACTION;

    -- Validar que la rifa existe y está en venta
    IF NOT EXISTS (
        SELECT 1 FROM rifas
        WHERE id = p_rifa_id
          AND sede_id = p_sede_id
          AND estado IN ('PUBLICADA', 'EN_VENTA')
          AND estado_activo = 1
          AND NOW() BETWEEN fecha_inicio_venta AND fecha_fin_venta
    ) THEN
        SET p_mensaje = 'La rifa no está disponible para compra';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Obtener configuración de la rifa
    SELECT 
        prefijo_numero,
        sufijo_numero,
        cantidad_digitos,
        numero_inicial,
        numero_final,
        cantidad_maxima_por_persona
    INTO v_prefijo, v_sufijo, v_digitos, v_numero_inicial, v_numero_final, v_cantidad_maxima_por_persona
    FROM rifas
    WHERE id = p_rifa_id;

    -- Validar cantidad máxima por persona
    SELECT COUNT(*) INTO v_tickets_existentes
    FROM tickets
    WHERE rifa_id = p_rifa_id
      AND numero_documento = p_numero_documento
      AND estado IN ('PENDIENTE_PAGO', 'PAGO_SUBIDO', 'VALIDANDO', 'APROBADO', 'PARTICIPANDO');

    IF v_tickets_existentes + p_cantidad_tickets > IFNULL(v_cantidad_maxima_por_persona, 999999) THEN
        SET p_mensaje = CONCAT('Has alcanzado el límite de tickets por persona (', IFNULL(v_cantidad_maxima_por_persona, 999999), ')');
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Generar código único del ticket
    SET v_codigo = CONCAT(
        UPPER(SUBSTRING((SELECT nombre FROM sedes WHERE id = p_sede_id), 1, 4)),
        '-',
        DATE_FORMAT(NOW(), '%Y%m%d'),
        '-',
        LPAD(FLOOR(RAND() * 999999), 6, '0')
    );

    -- Verificar que el código no exista
    WHILE EXISTS (SELECT 1 FROM tickets WHERE codigo_ticket = v_codigo) DO
        SET v_codigo = CONCAT(
            UPPER(SUBSTRING((SELECT nombre FROM sedes WHERE id = p_sede_id), 1, 4)),
            '-',
            DATE_FORMAT(NOW(), '%Y%m%d'),
            '-',
            LPAD(FLOOR(RAND() * 999999), 6, '0')
        );
    END WHILE;

    -- Determinar estado inicial según canal de venta o parámetro proporcionado
    -- Si p_estado_inicial es NULL, determinar según canal_venta:
    --   - WEB: PENDIENTE_PAGO (usuario final debe subir comprobante)
    --   - ADMINISTRATIVO: PENDIENTE_PAGO (por defecto, admin puede cambiar después)
    -- Si p_estado_inicial tiene valor, usarlo directamente
    IF p_estado_inicial IS NOT NULL AND p_estado_inicial != '' THEN
        SET v_estado_inicial = p_estado_inicial;
    ELSEIF IFNULL(p_canal_venta, 'WEB') = 'WEB' THEN
        SET v_estado_inicial = 'PENDIENTE_PAGO';
    ELSE
        SET v_estado_inicial = 'PENDIENTE_PAGO';
    END IF;

    -- Crear ticket principal
    INSERT INTO tickets (
        sede_id,
        rifa_id,
        codigo_ticket,
        nombres,
        apellidos,
        tipo_documento,
        numero_documento,
        email,
        telefono,
        direccion,
        ciudad,
        pais,
        precio_pagado,
        ip_compra,
        canal_venta,
        estado,
        fecha_creacion,
        fecha_modificacion,
        creado_por
    ) VALUES (
        p_sede_id,
        p_rifa_id,
        v_codigo,
        p_nombres,
        p_apellidos,
        p_tipo_documento,
        p_numero_documento,
        p_email,
        p_telefono,
        p_direccion,
        p_ciudad,
        p_pais,
        p_precio_pagado,
        p_ip_compra,
        IFNULL(p_canal_venta, 'WEB'),
        v_estado_inicial,
        NOW(),
        NOW(),
        CONCAT(p_nombres, ' ', p_apellidos)
    );

    SET p_ticket_id = LAST_INSERT_ID();
    SET p_codigo_ticket = v_codigo;

    -- Procesar números seleccionados (formato: "1,2,3" o "[1,2,3]")
    IF p_numeros_seleccionados IS NOT NULL AND p_numeros_seleccionados != '' THEN
        -- Limpiar formato JSON si viene como array JSON
        SET v_numeros_texto = REPLACE(REPLACE(REPLACE(p_numeros_seleccionados, '[', ''), ']', ''), '"', '');
        SET v_numeros_texto = TRIM(v_numeros_texto);
        
        -- Procesar números separados por coma
        WHILE v_posicion <= LENGTH(v_numeros_texto) AND v_contador < p_cantidad_tickets DO
            -- Encontrar la siguiente coma o fin de string
            SET v_posicion_siguiente = LOCATE(',', v_numeros_texto, v_posicion);
            
            IF v_posicion_siguiente = 0 THEN
                -- Último número
                SET v_numero_texto = TRIM(SUBSTRING(v_numeros_texto, v_posicion));
                SET v_posicion = LENGTH(v_numeros_texto) + 1;
            ELSE
                -- Número con coma siguiente
                SET v_numero_texto = TRIM(SUBSTRING(v_numeros_texto, v_posicion, v_posicion_siguiente - v_posicion));
                SET v_posicion = v_posicion_siguiente + 1;
            END IF;
            
            -- Convertir a número entero
            IF v_numero_texto != '' AND v_numero_texto REGEXP '^[0-9]+$' THEN
                SET v_numero_seleccionado = CAST(v_numero_texto AS UNSIGNED);
                
                -- Buscar número disponible
                SELECT id, numero_entero, numero_formateado INTO v_numero_id, v_numero_entero, v_numero_formateado
                FROM numeros_rifa
                WHERE rifa_id = p_rifa_id
                  AND numero_entero = v_numero_seleccionado
                  AND estado = 'DISPONIBLE'
                LIMIT 1;
                
                IF v_numero_id IS NOT NULL THEN
                    -- Si el ticket está aprobado directamente, marcar número como VENDIDO
                    -- Si está pendiente, marcar como RESERVADO
                    IF v_estado_inicial = 'APROBADO' THEN
                        UPDATE numeros_rifa
                        SET estado = 'VENDIDO',
                            ticket_id = p_ticket_id,
                            fecha_venta = NOW(),
                            fecha_modificacion = NOW()
                        WHERE id = v_numero_id;
                    ELSE
                        UPDATE numeros_rifa
                        SET estado = 'RESERVADO',
                            ticket_id = p_ticket_id,
                            reservado_hasta = DATE_ADD(NOW(), INTERVAL 30 MINUTE),
                            reservado_por_sesion = CONCAT('TICKET-', p_ticket_id),
                            fecha_reserva = NOW(),
                            fecha_modificacion = NOW()
                        WHERE id = v_numero_id;
                    END IF;
                    
                    -- Actualizar ticket con número
                    UPDATE tickets
                    SET numero_boleto = v_numero_formateado,
                        numero_boleto_entero = v_numero_entero,
                        numero_seleccionado_usuario = 1
                    WHERE id = p_ticket_id;
                    
                    SET v_contador = v_contador + 1;
                END IF;
            END IF;
        END WHILE;
    END IF;

    -- Si no se seleccionaron números y hay asignación automática, asignar uno disponible
    IF (p_numeros_seleccionados IS NULL OR p_numeros_seleccionados = '') AND p_cantidad_tickets = 1 THEN
        SELECT id, numero_entero, numero_formateado INTO v_numero_id, v_numero_entero, v_numero_formateado
        FROM numeros_rifa
        WHERE rifa_id = p_rifa_id
          AND estado = 'DISPONIBLE'
        ORDER BY numero_entero ASC
        LIMIT 1;
        
        IF v_numero_id IS NOT NULL THEN
            -- Si el ticket está aprobado directamente, marcar número como VENDIDO
            IF v_estado_inicial = 'APROBADO' THEN
                UPDATE numeros_rifa
                SET estado = 'VENDIDO',
                    ticket_id = p_ticket_id,
                    fecha_venta = NOW(),
                    fecha_modificacion = NOW()
                WHERE id = v_numero_id;
            ELSE
                UPDATE numeros_rifa
                SET estado = 'RESERVADO',
                    ticket_id = p_ticket_id,
                    reservado_hasta = DATE_ADD(NOW(), INTERVAL 30 MINUTE),
                    reservado_por_sesion = CONCAT('TICKET-', p_ticket_id),
                    fecha_reserva = NOW(),
                    fecha_modificacion = NOW()
                WHERE id = v_numero_id;
            END IF;
            
            UPDATE tickets
            SET numero_boleto = v_numero_formateado,
                numero_boleto_entero = v_numero_entero,
                numero_seleccionado_usuario = 0
            WHERE id = p_ticket_id;
        END IF;
    END IF;

    COMMIT;
    
    -- Mensaje según estado inicial
    IF v_estado_inicial = 'APROBADO' THEN
        SET p_mensaje = 'Ticket creado y aprobado correctamente. El participante puede participar en el sorteo.';
    ELSE
        SET p_mensaje = 'Ticket creado correctamente. Sube tu comprobante de pago para validar.';
    END IF;
END proc //

-- ==========================================================
-- 2. LISTAR TICKETS POR SEDE Y ESTADO
-- ==========================================================
DROP PROCEDURE IF EXISTS list_tickets //
CREATE PROCEDURE list_tickets (
    IN p_sede_id INT,
    IN p_rifa_id INT,
    IN p_estado VARCHAR(30)
)
BEGIN
    SELECT
        t.*,
        r.codigo AS rifa_codigo,
        r.nombre AS rifa_nombre,
        r.precio_ticket,
        s.nombre AS sede_nombre,
        (SELECT COUNT(*) FROM comprobantes_pago cp WHERE cp.ticket_id = t.id) AS tiene_comprobante,
        (SELECT estado FROM comprobantes_pago cp WHERE cp.ticket_id = t.id ORDER BY cp.fecha_creacion DESC LIMIT 1) AS estado_comprobante
    FROM tickets t
    INNER JOIN rifas r ON t.rifa_id = r.id
    INNER JOIN sedes s ON t.sede_id = s.id
    WHERE t.sede_id = p_sede_id
      AND (p_rifa_id IS NULL OR t.rifa_id = p_rifa_id)
      AND (p_estado IS NULL OR p_estado = '' OR t.estado = p_estado)
    ORDER BY t.fecha_creacion DESC;
END //

-- ==========================================================
-- 3. OBTENER TICKET POR CÓDIGO (PARA USUARIO FINAL)
-- ==========================================================
DROP PROCEDURE IF EXISTS get_ticket_by_codigo //
CREATE PROCEDURE get_ticket_by_codigo (
    IN p_codigo_ticket VARCHAR(50)
)
BEGIN
    SELECT
        t.*,
        r.codigo AS rifa_codigo,
        r.nombre AS rifa_nombre,
        r.descripcion AS rifa_descripcion,
        r.precio_ticket,
        r.fecha_sorteo,
        s.nombre AS sede_nombre,
        (SELECT archivo_comprobante FROM comprobantes_pago cp WHERE cp.ticket_id = t.id ORDER BY cp.fecha_creacion DESC LIMIT 1) AS archivo_comprobante,
        (SELECT estado FROM comprobantes_pago cp WHERE cp.ticket_id = t.id ORDER BY cp.fecha_creacion DESC LIMIT 1) AS estado_comprobante,
        (SELECT motivo_rechazo FROM comprobantes_pago cp WHERE cp.ticket_id = t.id ORDER BY cp.fecha_creacion DESC LIMIT 1) AS motivo_rechazo_comprobante
    FROM tickets t
    INNER JOIN rifas r ON t.rifa_id = r.id
    INNER JOIN sedes s ON t.sede_id = s.id
    WHERE t.codigo_ticket = p_codigo_ticket;
END //

-- ==========================================================
-- 4. REGISTRAR COMPROBANTE DE PAGO
-- ==========================================================
DROP PROCEDURE IF EXISTS register_comprobante_pago //
CREATE PROCEDURE register_comprobante_pago (
    IN p_sede_id INT,
    IN p_ticket_id INT,
    IN p_metodo_pago_id INT,
    IN p_numero_operacion VARCHAR(100),
    IN p_monto DECIMAL(10,2),
    IN p_fecha_pago DATETIME,
    IN p_archivo_comprobante VARCHAR(255),
    IN p_tipo_archivo VARCHAR(10),
    IN p_tamano_archivo INT,
    IN p_banco_origen VARCHAR(100),
    IN p_cuenta_origen VARCHAR(50),
    IN p_titular_origen VARCHAR(200),
    IN p_observaciones TEXT,
    IN p_creado_por VARCHAR(50),
    OUT p_comprobante_id INT,
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_comprobante_id = NULL;
        SET p_mensaje = 'Error al registrar el comprobante';
    END;

    START TRANSACTION;

    -- Validar que el ticket existe
    IF NOT EXISTS (
        SELECT 1 FROM tickets
        WHERE id = p_ticket_id
          AND sede_id = p_sede_id
    ) THEN
        SET p_mensaje = 'El ticket no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Insertar comprobante
    INSERT INTO comprobantes_pago (
        sede_id,
        ticket_id,
        metodo_pago_id,
        numero_operacion,
        monto,
        fecha_pago,
        archivo_comprobante,
        tipo_archivo,
        tamano_archivo,
        banco_origen,
        cuenta_origen,
        titular_origen,
        observaciones,
        estado,
        fecha_creacion,
        fecha_modificacion,
        creado_por
    ) VALUES (
        p_sede_id,
        p_ticket_id,
        p_metodo_pago_id,
        p_numero_operacion,
        p_monto,
        p_fecha_pago,
        p_archivo_comprobante,
        p_tipo_archivo,
        p_tamano_archivo,
        p_banco_origen,
        p_cuenta_origen,
        p_titular_origen,
        p_observaciones,
        'PENDIENTE',
        NOW(),
        NOW(),
        p_creado_por
    );

    SET p_comprobante_id = LAST_INSERT_ID();

    -- Actualizar estado del ticket
    UPDATE tickets
    SET estado = 'PAGO_SUBIDO',
        fecha_modificacion = NOW()
    WHERE id = p_ticket_id;

    COMMIT;
    SET p_mensaje = 'Comprobante registrado correctamente. Será validado por un administrador.';
END proc //

-- ==========================================================
-- 5. LISTAR COMPROBANTES PENDIENTES DE VALIDACIÓN
-- ==========================================================
DROP PROCEDURE IF EXISTS list_comprobantes_pendientes //
CREATE PROCEDURE list_comprobantes_pendientes (
    IN p_sede_id INT,
    IN p_estado VARCHAR(30)
)
BEGIN
    SELECT
        cp.*,
        t.codigo_ticket,
        t.nombres,
        t.apellidos,
        t.tipo_documento,
        t.numero_documento,
        t.email,
        t.telefono,
        t.precio_pagado,
        t.numero_boleto,
        t.numero_boleto_entero,
        t.estado AS estado_ticket,
        r.codigo AS rifa_codigo,
        r.nombre AS rifa_nombre,
        mp.nombre AS metodo_pago_nombre,
        s.nombre AS sede_nombre,
        DATEDIFF(NOW(), cp.fecha_creacion) AS dias_esperando
    FROM comprobantes_pago cp
    INNER JOIN tickets t ON cp.ticket_id = t.id
    INNER JOIN rifas r ON t.rifa_id = r.id
    INNER JOIN sedes s ON cp.sede_id = s.id
    LEFT JOIN metodos_pago mp ON cp.metodo_pago_id = mp.id
    WHERE cp.sede_id = p_sede_id
      AND (p_estado IS NULL OR p_estado = '' OR cp.estado = p_estado)
    ORDER BY cp.fecha_creacion ASC;
END //

-- ==========================================================
-- 6. VALIDAR COMPROBANTE (APROBAR O RECHAZAR)
-- ==========================================================
DROP PROCEDURE IF EXISTS validar_comprobante //
CREATE PROCEDURE validar_comprobante (
    IN p_comprobante_id INT,
    IN p_sede_id INT,
    IN p_estado VARCHAR(30),
    IN p_validado_por VARCHAR(50),
    IN p_motivo_rechazo TEXT,
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_ticket_id INT;
    DECLARE v_numero_id INT;
    DECLARE v_numero_entero INT;
    DECLARE v_numero_formateado VARCHAR(50);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al validar el comprobante';
    END;

    START TRANSACTION;

    -- Validar que el comprobante existe
    IF NOT EXISTS (
        SELECT 1 FROM comprobantes_pago
        WHERE id = p_comprobante_id
          AND sede_id = p_sede_id
    ) THEN
        SET p_mensaje = 'El comprobante no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Obtener ticket_id
    SELECT ticket_id INTO v_ticket_id
    FROM comprobantes_pago
    WHERE id = p_comprobante_id;

    -- Actualizar comprobante
    UPDATE comprobantes_pago
    SET estado = p_estado,
        validado_por = p_validado_por,
        fecha_validacion = NOW(),
        motivo_rechazo = CASE WHEN p_estado = 'RECHAZADO' THEN p_motivo_rechazo ELSE motivo_rechazo END,
        fecha_modificacion = NOW(),
        modificado_por = p_validado_por
    WHERE id = p_comprobante_id;

    -- Si se aprueba, actualizar ticket y número
    IF p_estado = 'APROBADO' THEN
        -- Obtener información del número reservado para asegurar que el ticket tenga el número asignado
        SELECT numero_entero, numero_formateado INTO v_numero_entero, v_numero_formateado
        FROM numeros_rifa
        WHERE ticket_id = v_ticket_id
          AND estado = 'RESERVADO'
        LIMIT 1;
        
        UPDATE tickets
        SET estado = 'APROBADO',
            aprobado_por = p_validado_por,
            fecha_aprobacion = NOW(),
            fecha_validez = DATE_ADD(NOW(), INTERVAL 90 DAY),
            validado = 1,
            fecha_validacion = NOW(),
            fecha_modificacion = NOW(),
            modificado_por = p_validado_por,
            -- Asegurar que el ticket tenga el número asignado (por si acaso no se asignó en register_ticket)
            numero_boleto = COALESCE(numero_boleto, v_numero_formateado),
            numero_boleto_entero = COALESCE(numero_boleto_entero, v_numero_entero)
        WHERE id = v_ticket_id;

        -- Actualizar número a VENDIDO (confirmar venta definitiva)
        UPDATE numeros_rifa
        SET estado = 'VENDIDO',
            fecha_venta = NOW(),
            reservado_hasta = NULL,
            reservado_por_sesion = NULL,
            fecha_modificacion = NOW()
        WHERE ticket_id = v_ticket_id
          AND estado = 'RESERVADO';

        -- Actualizar contador de tickets vendidos en la rifa
        UPDATE rifas
        SET tickets_vendidos = (
            SELECT COUNT(*) FROM tickets
            WHERE rifa_id = rifas.id
              AND estado = 'APROBADO'
        ),
        modificado_por = p_validado_por,
        fecha_modificacion = NOW()
        WHERE id = (SELECT rifa_id FROM tickets WHERE id = v_ticket_id);

    ELSEIF p_estado = 'RECHAZADO' THEN
        UPDATE tickets
        SET estado = 'RECHAZADO',
            rechazado_por = p_validado_por,
            fecha_rechazo = NOW(),
            motivo_rechazo = p_motivo_rechazo,
            fecha_modificacion = NOW(),
            modificado_por = p_validado_por,
            -- Limpiar número del ticket al rechazar
            numero_boleto = NULL,
            numero_boleto_entero = NULL,
            numero_seleccionado_usuario = 0
        WHERE id = v_ticket_id;

        -- Liberar número reservado (volver a DISPONIBLE)
        UPDATE numeros_rifa
        SET estado = 'DISPONIBLE',
            ticket_id = NULL,
            reservado_hasta = NULL,
            reservado_por_sesion = NULL,
            fecha_reserva = NULL,
            fecha_modificacion = NOW()
        WHERE ticket_id = v_ticket_id
          AND estado = 'RESERVADO';
    END IF;

    COMMIT;
    
    IF p_estado = 'APROBADO' THEN
        SET p_mensaje = 'Comprobante aprobado. El ticket ahora puede participar en el sorteo.';
    ELSE
        SET p_mensaje = 'Comprobante rechazado. El usuario puede subir un nuevo comprobante.';
    END IF;
END proc //

DELIMITER ;

