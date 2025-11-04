-- =====================================================
-- STORED PROCEDURES Y FUNCIONES
-- Sistema de Numeración de Boletos
-- =====================================================

DELIMITER $$

-- =====================================================
-- 1. GENERAR NÚMEROS DE RIFA
-- =====================================================
-- Genera todos los números para una rifa basado en su configuración
DROP PROCEDURE IF EXISTS sp_generar_numeros_rifa$$
CREATE PROCEDURE sp_generar_numeros_rifa(
    IN p_rifa_id INT
)
BEGIN
    DECLARE v_sede_id INT;
    DECLARE v_numero_inicial INT;
    DECLARE v_numero_final INT;
    DECLARE v_cantidad_digitos INT;
    DECLARE v_prefijo VARCHAR(20);
    DECLARE v_sufijo VARCHAR(20);
    DECLARE v_numero_actual INT;
    DECLARE v_numero_formateado VARCHAR(50);
    DECLARE v_usa_numeracion TINYINT;
    
    -- Obtener configuración de la rifa
    SELECT 
        sede_id,
        numero_inicial,
        numero_final,
        cantidad_digitos,
        prefijo_numero,
        sufijo_numero,
        usa_numeracion_boletos
    INTO
        v_sede_id,
        v_numero_inicial,
        v_numero_final,
        v_cantidad_digitos,
        v_prefijo,
        v_sufijo,
        v_usa_numeracion
    FROM rifas
    WHERE id = p_rifa_id;
    
    -- Validar que la rifa use numeración
    IF v_usa_numeracion = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Esta rifa no utiliza sistema de numeración de boletos';
    END IF;
    
    -- Validar rango
    IF v_numero_inicial >= v_numero_final THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'El número inicial debe ser menor que el número final';
    END IF;
    
    -- Limpiar números existentes (si los hay)
    DELETE FROM numeros_rifa WHERE rifa_id = p_rifa_id;
    
    -- Generar números
    SET v_numero_actual = v_numero_inicial;
    
    WHILE v_numero_actual <= v_numero_final DO
        -- Formatear número
        SET v_numero_formateado = LPAD(v_numero_actual, v_cantidad_digitos, '0');
        
        -- Agregar prefijo y sufijo si existen
        IF v_prefijo IS NOT NULL THEN
            SET v_numero_formateado = CONCAT(v_prefijo, v_numero_formateado);
        END IF;
        
        IF v_sufijo IS NOT NULL THEN
            SET v_numero_formateado = CONCAT(v_numero_formateado, v_sufijo);
        END IF;
        
        -- Insertar número
        INSERT INTO numeros_rifa (
            sede_id,
            rifa_id,
            numero_entero,
            numero_formateado,
            estado
        ) VALUES (
            v_sede_id,
            p_rifa_id,
            v_numero_actual,
            v_numero_formateado,
            'DISPONIBLE'
        );
        
        SET v_numero_actual = v_numero_actual + 1;
    END WHILE;
    
    -- Actualizar cantidad máxima de tickets en la rifa
    UPDATE rifas 
    SET cantidad_maxima_tickets = (v_numero_final - v_numero_inicial + 1)
    WHERE id = p_rifa_id;
    
    SELECT CONCAT('Se generaron ', (v_numero_final - v_numero_inicial + 1), ' números correctamente') AS resultado;
END$$

-- =====================================================
-- 2. RESERVAR NÚMERO DE BOLETO
-- =====================================================
-- Reserva un número temporalmente durante el proceso de compra
DROP PROCEDURE IF EXISTS sp_reservar_numero$$
CREATE PROCEDURE sp_reservar_numero(
    IN p_rifa_id INT,
    IN p_numero_entero INT,
    IN p_sesion_id VARCHAR(255),
    IN p_minutos_reserva INT,
    OUT p_exito TINYINT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_estado_actual VARCHAR(20);
    DECLARE v_reservado_hasta DATETIME;
    
    -- Verificar estado actual del número
    SELECT estado, reservado_hasta
    INTO v_estado_actual, v_reservado_hasta
    FROM numeros_rifa
    WHERE rifa_id = p_rifa_id AND numero_entero = p_numero_entero
    FOR UPDATE;
    
    -- Si no existe el número
    IF v_estado_actual IS NULL THEN
        SET p_exito = 0;
        SET p_mensaje = 'El número solicitado no existe';
    -- Si ya está vendido
    ELSEIF v_estado_actual = 'VENDIDO' THEN
        SET p_exito = 0;
        SET p_mensaje = 'El número ya fue vendido';
    -- Si está bloqueado
    ELSEIF v_estado_actual = 'BLOQUEADO' THEN
        SET p_exito = 0;
        SET p_mensaje = 'El número está bloqueado';
    -- Si está reservado y no ha expirado
    ELSEIF v_estado_actual = 'RESERVADO' AND v_reservado_hasta > NOW() THEN
        SET p_exito = 0;
        SET p_mensaje = 'El número está reservado por otro usuario';
    -- Disponible o reserva expirada
    ELSE
        UPDATE numeros_rifa
        SET 
            estado = 'RESERVADO',
            reservado_hasta = DATE_ADD(NOW(), INTERVAL p_minutos_reserva MINUTE),
            reservado_por_sesion = p_sesion_id,
            fecha_reserva = NOW()
        WHERE rifa_id = p_rifa_id AND numero_entero = p_numero_entero;
        
        SET p_exito = 1;
        SET p_mensaje = 'Número reservado exitosamente';
    END IF;
END$$

-- =====================================================
-- 3. ASIGNAR NÚMERO ALEATORIO DISPONIBLE
-- =====================================================
-- Asigna un número aleatorio disponible de una rifa
DROP PROCEDURE IF EXISTS sp_asignar_numero_aleatorio$$
CREATE PROCEDURE sp_asignar_numero_aleatorio(
    IN p_rifa_id INT,
    IN p_sesion_id VARCHAR(255),
    IN p_minutos_reserva INT,
    OUT p_numero_asignado INT,
    OUT p_numero_formateado VARCHAR(50),
    OUT p_exito TINYINT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_numero_id INT;
    
    -- Buscar un número disponible al azar
    SELECT id, numero_entero, numero_formateado
    INTO v_numero_id, p_numero_asignado, p_numero_formateado
    FROM numeros_rifa
    WHERE rifa_id = p_rifa_id 
      AND estado = 'DISPONIBLE'
    ORDER BY RAND()
    LIMIT 1
    FOR UPDATE;
    
    IF v_numero_id IS NULL THEN
        SET p_exito = 0;
        SET p_mensaje = 'No hay números disponibles';
        SET p_numero_asignado = NULL;
        SET p_numero_formateado = NULL;
    ELSE
        -- Reservar el número
        UPDATE numeros_rifa
        SET 
            estado = 'RESERVADO',
            reservado_hasta = DATE_ADD(NOW(), INTERVAL p_minutos_reserva MINUTE),
            reservado_por_sesion = p_sesion_id,
            fecha_reserva = NOW()
        WHERE id = v_numero_id;
        
        SET p_exito = 1;
        SET p_mensaje = 'Número asignado exitosamente';
    END IF;
END$$

-- =====================================================
-- 4. CONFIRMAR VENTA DE NÚMERO
-- =====================================================
-- Marca un número como vendido cuando se confirma el pago
DROP PROCEDURE IF EXISTS sp_confirmar_venta_numero$$
CREATE PROCEDURE sp_confirmar_venta_numero(
    IN p_rifa_id INT,
    IN p_numero_entero INT,
    IN p_ticket_id INT,
    OUT p_exito TINYINT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_estado VARCHAR(20);
    
    -- Verificar estado
    SELECT estado INTO v_estado
    FROM numeros_rifa
    WHERE rifa_id = p_rifa_id AND numero_entero = p_numero_entero
    FOR UPDATE;
    
    IF v_estado IS NULL THEN
        SET p_exito = 0;
        SET p_mensaje = 'Número no encontrado';
    ELSEIF v_estado = 'VENDIDO' THEN
        SET p_exito = 0;
        SET p_mensaje = 'El número ya fue vendido';
    ELSE
        -- Marcar como vendido
        UPDATE numeros_rifa
        SET 
            estado = 'VENDIDO',
            ticket_id = p_ticket_id,
            fecha_venta = NOW(),
            reservado_hasta = NULL,
            reservado_por_sesion = NULL
        WHERE rifa_id = p_rifa_id AND numero_entero = p_numero_entero;
        
        -- Actualizar contador en rifas
        UPDATE rifas
        SET tickets_vendidos = tickets_vendidos + 1
        WHERE id = p_rifa_id;
        
        SET p_exito = 1;
        SET p_mensaje = 'Venta confirmada exitosamente';
    END IF;
END$$

-- =====================================================
-- 5. LIBERAR RESERVAS EXPIRADAS
-- =====================================================
-- Libera números reservados cuyo tiempo de reserva expiró
DROP PROCEDURE IF EXISTS sp_liberar_reservas_expiradas$$
CREATE PROCEDURE sp_liberar_reservas_expiradas()
BEGIN
    DECLARE v_cantidad INT;
    
    UPDATE numeros_rifa
    SET 
        estado = 'DISPONIBLE',
        reservado_hasta = NULL,
        reservado_por_sesion = NULL
    WHERE estado = 'RESERVADO'
      AND reservado_hasta < NOW();
    
    SET v_cantidad = ROW_COUNT();
    
    SELECT CONCAT('Se liberaron ', v_cantidad, ' reservas expiradas') AS resultado;
END$$

-- =====================================================
-- 6. CREAR VOLANTARIO
-- =====================================================
-- Crea un nuevo volantario con un rango de números
DROP PROCEDURE IF EXISTS sp_crear_volantario$$
CREATE PROCEDURE sp_crear_volantario(
    IN p_sede_id INT,
    IN p_rifa_id INT,
    IN p_codigo_volantario VARCHAR(50),
    IN p_numero_inicial INT,
    IN p_numero_final INT,
    IN p_vendedor_id INT,
    OUT p_volantario_id INT,
    OUT p_exito TINYINT,
    OUT p_mensaje VARCHAR(255)
)
BEGIN
    DECLARE v_cantidad_numeros INT;
    DECLARE v_numeros_ocupados INT;
    
    -- Calcular cantidad de números
    SET v_cantidad_numeros = p_numero_final - p_numero_inicial + 1;
    
    -- Verificar que los números estén disponibles
    SELECT COUNT(*)
    INTO v_numeros_ocupados
    FROM numeros_rifa
    WHERE rifa_id = p_rifa_id
      AND numero_entero BETWEEN p_numero_inicial AND p_numero_final
      AND estado != 'DISPONIBLE';
    
    IF v_numeros_ocupados > 0 THEN
        SET p_exito = 0;
        SET p_mensaje = CONCAT('Hay ', v_numeros_ocupados, ' números no disponibles en el rango seleccionado');
        SET p_volantario_id = NULL;
    ELSE
        -- Crear volantario
        INSERT INTO volantarios (
            sede_id,
            rifa_id,
            codigo_volantario,
            numero_inicial,
            numero_final,
            cantidad_numeros,
            numeros_disponibles,
            asignado_vendedor_id,
            estado,
            creado_por
        ) VALUES (
            p_sede_id,
            p_rifa_id,
            p_codigo_volantario,
            p_numero_inicial,
            p_numero_final,
            v_cantidad_numeros,
            v_cantidad_numeros,
            p_vendedor_id,
            'GENERADO',
            'SYSTEM'
        );
        
        SET p_volantario_id = LAST_INSERT_ID();
        
        -- Asociar números al volantario
        UPDATE numeros_rifa
        SET volantario_id = p_volantario_id
        WHERE rifa_id = p_rifa_id
          AND numero_entero BETWEEN p_numero_inicial AND p_numero_final;
        
        SET p_exito = 1;
        SET p_mensaje = 'Volantario creado exitosamente';
    END IF;
END$$

-- =====================================================
-- 7. OBTENER NÚMEROS DISPONIBLES
-- =====================================================
-- Obtiene lista de números disponibles para una rifa
DROP PROCEDURE IF EXISTS sp_obtener_numeros_disponibles$$
CREATE PROCEDURE sp_obtener_numeros_disponibles(
    IN p_rifa_id INT,
    IN p_limite INT
)
BEGIN
    IF p_limite IS NULL OR p_limite <= 0 THEN
        SET p_limite = 100;
    END IF;
    
    SELECT 
        numero_entero,
        numero_formateado,
        estado,
        CASE 
            WHEN es_numero_especial = 1 THEN descripcion_especial
            ELSE NULL
        END as descripcion
    FROM numeros_rifa
    WHERE rifa_id = p_rifa_id
      AND (estado = 'DISPONIBLE' OR (estado = 'RESERVADO' AND reservado_hasta < NOW()))
    ORDER BY numero_entero
    LIMIT p_limite;
END$$

-- =====================================================
-- 8. OBTENER ESTADÍSTICAS DE NUMERACIÓN
-- =====================================================
-- Obtiene estadísticas de venta de números para una rifa
DROP PROCEDURE IF EXISTS sp_estadisticas_numeros_rifa$$
CREATE PROCEDURE sp_estadisticas_numeros_rifa(
    IN p_rifa_id INT
)
BEGIN
    SELECT 
        r.nombre as rifa_nombre,
        r.numero_inicial,
        r.numero_final,
        COUNT(nr.id) as total_numeros,
        SUM(CASE WHEN nr.estado = 'DISPONIBLE' THEN 1 ELSE 0 END) as disponibles,
        SUM(CASE WHEN nr.estado = 'RESERVADO' THEN 1 ELSE 0 END) as reservados,
        SUM(CASE WHEN nr.estado = 'VENDIDO' THEN 1 ELSE 0 END) as vendidos,
        SUM(CASE WHEN nr.estado = 'BLOQUEADO' THEN 1 ELSE 0 END) as bloqueados,
        ROUND((SUM(CASE WHEN nr.estado = 'VENDIDO' THEN 1 ELSE 0 END) / COUNT(nr.id)) * 100, 2) as porcentaje_vendido
    FROM rifas r
    LEFT JOIN numeros_rifa nr ON r.id = nr.rifa_id
    WHERE r.id = p_rifa_id
    GROUP BY r.id, r.nombre, r.numero_inicial, r.numero_final;
END$$

-- =====================================================
-- 9. BLOQUEAR NÚMEROS
-- =====================================================
-- Bloquea números específicos (para sorteos, promociones, etc.)
DROP PROCEDURE IF EXISTS sp_bloquear_numeros$$
CREATE PROCEDURE sp_bloquear_numeros(
    IN p_rifa_id INT,
    IN p_numero_inicial INT,
    IN p_numero_final INT,
    IN p_motivo VARCHAR(255)
)
BEGIN
    UPDATE numeros_rifa
    SET 
        estado = 'BLOQUEADO',
        motivo_bloqueo = p_motivo,
        fecha_bloqueo = NOW()
    WHERE rifa_id = p_rifa_id
      AND numero_entero BETWEEN p_numero_inicial AND p_numero_final
      AND estado = 'DISPONIBLE';
    
    SELECT CONCAT('Se bloquearon ', ROW_COUNT(), ' números') AS resultado;
END$$

-- =====================================================
-- 10. DESBLOQUEAR NÚMEROS
-- =====================================================
DROP PROCEDURE IF EXISTS sp_desbloquear_numeros$$
CREATE PROCEDURE sp_desbloquear_numeros(
    IN p_rifa_id INT,
    IN p_numero_inicial INT,
    IN p_numero_final INT
)
BEGIN
    UPDATE numeros_rifa
    SET 
        estado = 'DISPONIBLE',
        motivo_bloqueo = NULL,
        fecha_bloqueo = NULL
    WHERE rifa_id = p_rifa_id
      AND numero_entero BETWEEN p_numero_inicial AND p_numero_final
      AND estado = 'BLOQUEADO';
    
    SELECT CONCAT('Se desbloquearon ', ROW_COUNT(), ' números') AS resultado;
END$$

DELIMITER ;

-- =====================================================
-- EJEMPLOS DE USO
-- =====================================================

-- 1. Generar números para una rifa
-- CALL sp_generar_numeros_rifa(1);

-- 2. Reservar un número específico
-- CALL sp_reservar_numero(1, 523, 'session-xyz', 10, @exito, @mensaje);
-- SELECT @exito, @mensaje;

-- 3. Asignar número aleatorio
-- CALL sp_asignar_numero_aleatorio(1, 'session-abc', 10, @numero, @formateado, @exito, @mensaje);
-- SELECT @numero, @formateado, @exito, @mensaje;

-- 4. Confirmar venta
-- CALL sp_confirmar_venta_numero(1, 523, 1001, @exito, @mensaje);
-- SELECT @exito, @mensaje;

-- 5. Liberar reservas expiradas (ejecutar periódicamente)
-- CALL sp_liberar_reservas_expiradas();

-- 6. Crear volantario
-- CALL sp_crear_volantario(1, 1, 'VOL-001', 1, 100, 5, @vol_id, @exito, @mensaje);
-- SELECT @vol_id, @exito, @mensaje;

-- 7. Obtener números disponibles
-- CALL sp_obtener_numeros_disponibles(1, 50);

-- 8. Ver estadísticas
-- CALL sp_estadisticas_numeros_rifa(1);

-- 9. Bloquear números para sorteo
-- CALL sp_bloquear_numeros(1, 100, 150, 'Reservado para evento especial');

-- 10. Desbloquear números
-- CALL sp_desbloquear_numeros(1, 100, 150);

