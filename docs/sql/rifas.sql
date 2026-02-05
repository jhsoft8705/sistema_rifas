-- =============================================
-- STORED PROCEDURES PARA MANTENIMIENTO DE RIFAS / SORTEOS (MySQL)
-- Basado en la estructura definida en docs/sql/bd_rifas_mysql.sql
-- =============================================

DELIMITER //

-- ==========================================================
-- 1. LISTAR RIFAS POR SEDE (CON INFORMACIÓN RESUMIDA)
-- ==========================================================
DROP PROCEDURE IF EXISTS list_rifas //
CREATE PROCEDURE list_rifas (
    IN p_sede_id INT,
    IN p_estado VARCHAR(30)
)
BEGIN
    SELECT
        r.id,
        r.sede_id,
        s.nombre AS sede_nombre,
        r.premio_id AS premio_principal_id,
        p.nombre AS premio_principal_nombre,
        r.codigo,
        r.nombre,
        r.descripcion,
        r.numero_intentos,
        r.intento_ganador,
        r.precio_ticket,
        r.cantidad_maxima_tickets,
        r.tickets_vendidos,
        r.cantidad_maxima_por_persona,
        r.usa_numeracion_boletos,
        r.tipo_numeracion,
        r.numero_inicial,
        r.numero_final,
        r.cantidad_digitos,
        r.prefijo_numero,
        r.sufijo_numero,
        r.permitir_seleccion_numero,
        r.asignacion_automatica,
        r.mostrar_numeros_disponibles,
        r.fecha_inicio_venta,
        r.fecha_fin_venta,
        r.fecha_sorteo,
        r.fecha_sorteo_realizado,
        r.mostrar_contador,
        r.mostrar_participantes,
        r.mostrar_tickets_vendidos,
        r.texto_promocional,
        r.reglas_participacion,
        r.terminos_condiciones,
        r.estado,
        r.estado_activo,
        r.creado_por,
        r.fecha_creacion,
        r.modificado_por,
        r.fecha_modificacion,
        (SELECT COUNT(*) FROM rifas_premios rp WHERE rp.rifa_id = r.id AND rp.estado = 1) AS total_premios,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id) AS total_numeros,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id AND nr.estado = 'DISPONIBLE') AS numeros_disponibles,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id AND nr.estado = 'RESERVADO') AS numeros_reservados,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id AND nr.estado = 'VENDIDO') AS numeros_vendidos
    FROM rifas r
    INNER JOIN sedes s ON r.sede_id = s.id
    LEFT JOIN premios p ON r.premio_id = p.id
    WHERE r.sede_id = p_sede_id
      AND (p_estado IS NULL OR p_estado = '' OR r.estado = p_estado)
    ORDER BY r.fecha_creacion DESC;
END //

-- ==========================================================
-- 1.1. LISTAR RIFAS PÚBLICAS (PARA LANDING PAGE)
-- ==========================================================
DROP PROCEDURE IF EXISTS list_rifas_publicas //
CREATE PROCEDURE list_rifas_publicas (
    IN p_sede_id INT
)
BEGIN
    SELECT
        r.id,
        r.sede_id,
        s.nombre AS sede_nombre,
        r.premio_id AS premio_principal_id,
        p.nombre AS premio_principal_nombre,
        r.codigo,
        r.nombre,
        r.descripcion,
        r.precio_ticket,
        r.cantidad_maxima_tickets,
        r.tickets_vendidos,
        r.cantidad_maxima_por_persona,
        r.numero_inicial,
        r.numero_final,
        r.cantidad_digitos,
        r.prefijo_numero,
        r.sufijo_numero,
        r.fecha_inicio_venta,
        r.fecha_fin_venta,
        r.fecha_sorteo,
        r.mostrar_contador,
        r.mostrar_participantes,
        r.mostrar_tickets_vendidos,
        r.texto_promocional,
        r.reglas_participacion,
        r.terminos_condiciones,
        (SELECT COUNT(*) FROM rifas_premios rp WHERE rp.rifa_id = r.id AND rp.estado = 1) AS total_premios,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id) AS total_numeros,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id AND nr.estado = 'DISPONIBLE') AS numeros_disponibles,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id AND nr.estado = 'VENDIDO') AS numeros_vendidos
    FROM rifas r
    INNER JOIN sedes s ON r.sede_id = s.id
    LEFT JOIN premios p ON r.premio_id = p.id
    WHERE r.estado IN ('PUBLICADA', 'EN_VENTA')
      AND r.estado_activo = 1
      AND (p_sede_id IS NULL OR r.sede_id = p_sede_id)
      AND r.fecha_inicio_venta <= NOW()
      AND r.fecha_fin_venta >= NOW()
    ORDER BY r.fecha_sorteo ASC, r.fecha_creacion DESC;
END //

-- ==========================================================
-- 1.2. OBTENER PRÓXIMO SORTEO (UNA SOLA RIFA, LIGERO - PARA HERO LANDING)
-- ==========================================================
DROP PROCEDURE IF EXISTS get_proximo_sorteo //
CREATE PROCEDURE get_proximo_sorteo (
    IN p_sede_id INT
)
BEGIN
    SELECT
        r.id,
        r.nombre,
        r.descripcion,
        r.fecha_sorteo
    FROM rifas r
    WHERE r.estado IN ('PUBLICADA', 'EN_VENTA')
      AND r.estado_activo = 1
      AND (p_sede_id IS NULL OR r.sede_id = p_sede_id)
      AND r.fecha_inicio_venta <= NOW()
      AND r.fecha_fin_venta >= NOW()
    ORDER BY r.fecha_sorteo ASC
    LIMIT 1;
END //

-- ==========================================================
-- 2. OBTENER RIFA POR ID (DETALLE COMPLETO)
-- ==========================================================
DROP PROCEDURE IF EXISTS list_rifa_by_id //
CREATE PROCEDURE list_rifa_by_id (
    IN p_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        r.*,
        s.nombre AS sede_nombre,
        p.nombre AS premio_principal_nombre,
        (SELECT COUNT(*) FROM rifas_premios rp WHERE rp.rifa_id = r.id AND rp.estado = 1) AS total_premios,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id) AS total_numeros,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id AND nr.estado = 'DISPONIBLE') AS numeros_disponibles,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id AND nr.estado = 'RESERVADO') AS numeros_reservados,
        (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id AND nr.estado = 'VENDIDO') AS numeros_vendidos
    FROM rifas r
    INNER JOIN sedes s ON r.sede_id = s.id
    LEFT JOIN premios p ON r.premio_id = p.id
    WHERE r.id = p_id
      AND r.sede_id = p_sede_id;
END //

-- ==========================================================
-- 3. GENERAR NUMERACIÓN PARA UNA RIFA
-- ==========================================================
DROP PROCEDURE IF EXISTS generate_rifa_numeros //
CREATE PROCEDURE generate_rifa_numeros (
    IN p_rifa_id INT,
    IN p_sede_id INT,
    IN p_numero_inicial INT,
    IN p_numero_final INT,
    IN p_cantidad_digitos INT,
    IN p_prefijo_numero VARCHAR(20),
    IN p_sufijo_numero VARCHAR(20),
    IN p_creado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_numero INT;
    DECLARE v_formateado VARCHAR(50);
    DECLARE v_insertados INT DEFAULT 0;
    DECLARE v_error_msg VARCHAR(255);

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1
            v_error_msg = MESSAGE_TEXT;
        SET p_mensaje = CONCAT('Error al generar los números de la rifa: ', IFNULL(v_error_msg, 'Error desconocido'));
        -- NO hacer ROLLBACK aquí porque el procedimiento padre maneja la transacción
    END;

    -- NO usar START TRANSACTION aquí porque este procedimiento se llama desde otros que ya tienen transacción
    -- El procedimiento padre (update_rifa o register_rifa) maneja la transacción

    SET v_numero = p_numero_inicial;
    WHILE v_numero <= p_numero_final DO
        SET v_formateado = CONCAT(IFNULL(p_prefijo_numero, ''), LPAD(v_numero, IFNULL(p_cantidad_digitos, 4), '0'), IFNULL(p_sufijo_numero, ''));
        
        INSERT IGNORE INTO numeros_rifa (
            sede_id,
            rifa_id,
            numero_entero,
            numero_formateado,
            estado
        ) VALUES (
            p_sede_id,
            p_rifa_id,
            v_numero,
            v_formateado,
            'DISPONIBLE'
        );
        
        -- Contar insertados (INSERT IGNORE retorna 0 si el número ya existe, 1 si se insertó)
        IF ROW_COUNT() > 0 THEN
            SET v_insertados = v_insertados + 1;
        END IF;

        SET v_numero = v_numero + 1;
    END WHILE;

    -- NO hacer COMMIT aquí - el procedimiento padre lo hace
    IF v_insertados > 0 THEN
        SET p_mensaje = CONCAT('Números generados correctamente. Se insertaron ', v_insertados, ' número(s) nuevo(s) del ', p_numero_inicial, ' al ', p_numero_final, '.');
    ELSE
        SET p_mensaje = CONCAT('Todos los números del rango ', p_numero_inicial, ' al ', p_numero_final, ' ya existen. No se insertaron números nuevos.');
    END IF;
END proc //

-- ==========================================================
-- 4. REGISTRAR UNA RIFA
-- ==========================================================
DROP PROCEDURE IF EXISTS register_rifa //
CREATE PROCEDURE register_rifa (
    IN p_sede_id INT,
    IN p_premio_id INT,
    IN p_codigo VARCHAR(50),
    IN p_nombre VARCHAR(200),
    IN p_descripcion TEXT,
    IN p_numero_intentos INT,
    IN p_intento_ganador INT,
    IN p_precio_ticket DECIMAL(10,2),
    IN p_cantidad_maxima_tickets INT,
    IN p_cantidad_maxima_por_persona INT,
    IN p_usa_numeracion_boletos TINYINT,
    IN p_tipo_numeracion VARCHAR(20),
    IN p_numero_inicial INT,
    IN p_numero_final INT,
    IN p_cantidad_digitos INT,
    IN p_prefijo_numero VARCHAR(20),
    IN p_sufijo_numero VARCHAR(20),
    IN p_permitir_seleccion_numero TINYINT,
    IN p_asignacion_automatica TINYINT,
    IN p_mostrar_numeros_disponibles TINYINT,
    IN p_numeros_bloqueados TEXT,
    IN p_numeros_especiales TEXT,
    IN p_fecha_inicio_venta DATETIME,
    IN p_fecha_fin_venta DATETIME,
    IN p_fecha_sorteo DATETIME,
    IN p_mostrar_contador TINYINT,
    IN p_mostrar_participantes TINYINT,
    IN p_mostrar_tickets_vendidos TINYINT,
    IN p_texto_promocional TEXT,
    IN p_reglas_participacion TEXT,
    IN p_terminos_condiciones TEXT,
    IN p_estado VARCHAR(30),
    IN p_creado_por VARCHAR(50),
    OUT p_rifa_id INT,
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_mensaje_numeros VARCHAR(255);
    DECLARE v_codigo_generado VARCHAR(50);
    DECLARE v_ultimo_numero INT DEFAULT 0;
    DECLARE v_nuevo_numero INT DEFAULT 1;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_rifa_id = NULL;
        SET p_mensaje = 'Error al registrar la rifa';
    END;

    START TRANSACTION;

    IF NOT EXISTS (SELECT 1 FROM sedes WHERE id = p_sede_id) THEN
        SET p_mensaje = 'La sede no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF p_premio_id IS NOT NULL THEN
        IF NOT EXISTS (SELECT 1 FROM premios WHERE id = p_premio_id AND sede_id = p_sede_id AND estado = 1) THEN
            SET p_mensaje = 'El premio principal no existe o está inactivo';
            ROLLBACK;
            LEAVE proc;
        END IF;
    END IF;

    -- Generar código automáticamente si no se proporciona o está vacío
    IF p_codigo IS NULL OR (p_codigo IS NOT NULL AND TRIM(p_codigo) = '') THEN
        -- Obtener el último número de código para esta sede
        SELECT COALESCE(MAX(CAST(SUBSTRING(codigo, 5) AS UNSIGNED)), 0) INTO v_ultimo_numero
        FROM rifas
        WHERE sede_id = p_sede_id
          AND codigo LIKE 'RIFA-%'
          AND SUBSTRING(codigo, 5) REGEXP '^[0-9]+$';
        
        SET v_nuevo_numero = v_ultimo_numero + 1;
        SET v_codigo_generado = CONCAT('RIFA-', LPAD(v_nuevo_numero, 6, '0'));
        
        -- Verificar que el código generado no exista (por si acaso)
        WHILE EXISTS (
            SELECT 1 FROM rifas
            WHERE sede_id = p_sede_id AND codigo = v_codigo_generado
        ) DO
            SET v_nuevo_numero = v_nuevo_numero + 1;
            SET v_codigo_generado = CONCAT('RIFA-', LPAD(v_nuevo_numero, 6, '0'));
        END WHILE;
        
        SET p_codigo = v_codigo_generado;
    ELSE
        -- Validar que el código proporcionado no exista
        IF EXISTS (
            SELECT 1
            FROM rifas
            WHERE sede_id = p_sede_id
              AND codigo = p_codigo
        ) THEN
            SET p_mensaje = 'El código de la rifa ya existe en esta sede';
            ROLLBACK;
            LEAVE proc;
        END IF;
    END IF;

    IF p_numero_inicial >= p_numero_final THEN
        SET p_mensaje = 'El número inicial debe ser menor al número final';
        ROLLBACK;
        LEAVE proc;
    END IF;

    INSERT INTO rifas (
        sede_id,
        premio_id,
        codigo,
        nombre,
        descripcion,
        numero_intentos,
        intento_ganador,
        precio_ticket,
        cantidad_maxima_tickets,
        tickets_vendidos,
        cantidad_maxima_por_persona,
        usa_numeracion_boletos,
        tipo_numeracion,
        numero_inicial,
        numero_final,
        cantidad_digitos,
        prefijo_numero,
        sufijo_numero,
        permitir_seleccion_numero,
        asignacion_automatica,
        mostrar_numeros_disponibles,
        numeros_bloqueados,
        numeros_especiales,
        fecha_inicio_venta,
        fecha_fin_venta,
        fecha_sorteo,
        mostrar_contador,
        mostrar_participantes,
        mostrar_tickets_vendidos,
        texto_promocional,
        reglas_participacion,
        terminos_condiciones,
        estado,
        estado_activo,
        creado_por,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        p_premio_id,
        p_codigo,
        p_nombre,
        p_descripcion,
        IFNULL(p_numero_intentos, 5),
        IFNULL(p_intento_ganador, IFNULL(p_numero_intentos, 5)),
        p_precio_ticket,
        p_cantidad_maxima_tickets,
        0,
        IFNULL(p_cantidad_maxima_por_persona, 1),
        IFNULL(p_usa_numeracion_boletos, 1),
        IFNULL(p_tipo_numeracion, 'CORRELATIVO'),
        p_numero_inicial,
        p_numero_final,
        IFNULL(p_cantidad_digitos, 4),
        p_prefijo_numero,
        p_sufijo_numero,
        IFNULL(p_permitir_seleccion_numero, 1),
        IFNULL(p_asignacion_automatica, 1),
        IFNULL(p_mostrar_numeros_disponibles, 1),
        p_numeros_bloqueados,
        p_numeros_especiales,
        p_fecha_inicio_venta,
        p_fecha_fin_venta,
        p_fecha_sorteo,
        IFNULL(p_mostrar_contador, 1),
        IFNULL(p_mostrar_participantes, 1),
        IFNULL(p_mostrar_tickets_vendidos, 1),
        p_texto_promocional,
        p_reglas_participacion,
        p_terminos_condiciones,
        IFNULL(p_estado, 'BORRADOR'),
        1,
        p_creado_por,
        NOW(),
        NOW()
    );

    SET p_rifa_id = LAST_INSERT_ID();

    CALL generate_rifa_numeros(
        p_rifa_id,
        p_sede_id,
        p_numero_inicial,
        p_numero_final,
        IFNULL(p_cantidad_digitos, 4),
        p_prefijo_numero,
        p_sufijo_numero,
        p_creado_por,
        v_mensaje_numeros
    );

    COMMIT;
    SET p_mensaje = 'Rifa registrada correctamente';
END proc //

-- ==========================================================
-- 5. ACTUALIZAR UNA RIFA
-- ==========================================================
DROP PROCEDURE IF EXISTS update_rifa //
CREATE PROCEDURE update_rifa (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_premio_id INT,
    IN p_codigo VARCHAR(50),
    IN p_nombre VARCHAR(200),
    IN p_descripcion TEXT,
    IN p_numero_intentos INT,
    IN p_intento_ganador INT,
    IN p_precio_ticket DECIMAL(10,2),
    IN p_cantidad_maxima_tickets INT,
    IN p_cantidad_maxima_por_persona INT,
    IN p_usa_numeracion_boletos TINYINT,
    IN p_tipo_numeracion VARCHAR(20),
    IN p_numero_inicial INT,
    IN p_numero_final INT,
    IN p_cantidad_digitos INT,
    IN p_prefijo_numero VARCHAR(20),
    IN p_sufijo_numero VARCHAR(20),
    IN p_permitir_seleccion_numero TINYINT,
    IN p_asignacion_automatica TINYINT,
    IN p_mostrar_numeros_disponibles TINYINT,
    IN p_numeros_bloqueados TEXT,
    IN p_numeros_especiales TEXT,
    IN p_fecha_inicio_venta DATETIME,
    IN p_fecha_fin_venta DATETIME,
    IN p_fecha_sorteo DATETIME,
    IN p_fecha_sorteo_realizado DATETIME,
    IN p_mostrar_contador TINYINT,
    IN p_mostrar_participantes TINYINT,
    IN p_mostrar_tickets_vendidos TINYINT,
    IN p_texto_promocional TEXT,
    IN p_reglas_participacion TEXT,
    IN p_terminos_condiciones TEXT,
    IN p_estado VARCHAR(30),
    IN p_estado_activo INT,
    IN p_regenerar_numeros TINYINT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_tickets_asignados INT DEFAULT 0;
    DECLARE v_mensaje_numeros VARCHAR(255);
    DECLARE v_numero_inicial_actual INT;
    DECLARE v_numero_final_actual INT;
    DECLARE v_tickets_fuera_rango INT DEFAULT 0;
    DECLARE v_mensaje_cambio VARCHAR(255) DEFAULT '';

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar la rifa';
    END;

    START TRANSACTION;

    IF NOT EXISTS (SELECT 1 FROM rifas WHERE id = p_id AND sede_id = p_sede_id) THEN
        SET p_mensaje = 'La rifa no existe en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Obtener valores originales ANTES del UPDATE para detectar cambios en numeración
    SELECT numero_inicial, numero_final INTO v_numero_inicial_actual, v_numero_final_actual
    FROM rifas WHERE id = p_id AND sede_id = p_sede_id;

    -- Validar que no se pueda cambiar el estado de una rifa cerrada o finalizada
    IF EXISTS (
        SELECT 1 FROM rifas 
        WHERE id = p_id 
          AND sede_id = p_sede_id 
          AND estado IN ('CERRADA', 'FINALIZADA')
          AND p_estado NOT IN ('CERRADA', 'FINALIZADA')
    ) THEN
        SET p_mensaje = 'No se puede cambiar el estado de una rifa cerrada o finalizada. Use la opción "Reabrir Rifa" si es necesario.';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM rifas
        WHERE sede_id = p_sede_id
          AND codigo = p_codigo
          AND id <> p_id
    ) THEN
        SET p_mensaje = 'El código de la rifa ya está en uso en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF p_numero_inicial >= p_numero_final THEN
        SET p_mensaje = 'El número inicial debe ser menor al número final';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE rifas
    SET
        premio_id = p_premio_id,
        codigo = p_codigo,
        nombre = p_nombre,
        descripcion = p_descripcion,
        numero_intentos = p_numero_intentos,
        intento_ganador = p_intento_ganador,
        precio_ticket = p_precio_ticket,
        cantidad_maxima_tickets = p_cantidad_maxima_tickets,
        cantidad_maxima_por_persona = p_cantidad_maxima_por_persona,
        usa_numeracion_boletos = p_usa_numeracion_boletos,
        tipo_numeracion = p_tipo_numeracion,
        numero_inicial = p_numero_inicial,
        numero_final = p_numero_final,
        cantidad_digitos = p_cantidad_digitos,
        prefijo_numero = p_prefijo_numero,
        sufijo_numero = p_sufijo_numero,
        permitir_seleccion_numero = p_permitir_seleccion_numero,
        asignacion_automatica = p_asignacion_automatica,
        mostrar_numeros_disponibles = p_mostrar_numeros_disponibles,
        numeros_bloqueados = p_numeros_bloqueados,
        numeros_especiales = p_numeros_especiales,
        fecha_inicio_venta = p_fecha_inicio_venta,
        fecha_fin_venta = p_fecha_fin_venta,
        fecha_sorteo = p_fecha_sorteo,
        fecha_sorteo_realizado = p_fecha_sorteo_realizado,
        mostrar_contador = p_mostrar_contador,
        mostrar_participantes = p_mostrar_participantes,
        mostrar_tickets_vendidos = p_mostrar_tickets_vendidos,
        texto_promocional = p_texto_promocional,
        reglas_participacion = p_reglas_participacion,
        terminos_condiciones = p_terminos_condiciones,
        estado = p_estado,
        estado_activo = p_estado_activo,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    -- Manejo inteligente de cambios en numeración
    -- Solo procesar cambios si usa numeración de boletos
    IF p_usa_numeracion_boletos = 1 THEN
        -- Verificar si cambió el rango de números (manejar NULLs correctamente)
        -- Usar comparación explícita para evitar problemas con NULLs
        IF ((v_numero_inicial_actual IS NULL AND p_numero_inicial IS NOT NULL) 
            OR (v_numero_inicial_actual IS NOT NULL AND p_numero_inicial IS NULL)
            OR (v_numero_inicial_actual IS NOT NULL AND p_numero_inicial IS NOT NULL AND v_numero_inicial_actual != p_numero_inicial)
            OR (v_numero_final_actual IS NULL AND p_numero_final IS NOT NULL)
            OR (v_numero_final_actual IS NOT NULL AND p_numero_final IS NULL)
            OR (v_numero_final_actual IS NOT NULL AND p_numero_final IS NOT NULL AND v_numero_final_actual != p_numero_final)) THEN
            SET v_mensaje_cambio = '';
            
            -- Asegurar que los valores originales no sean NULL para comparaciones (usar 0 como default)
            SET v_numero_inicial_actual = COALESCE(v_numero_inicial_actual, 0);
            SET v_numero_final_actual = COALESCE(v_numero_final_actual, 0);
        
        -- CASO 1: AUMENTO DEL RANGO (numero_final mayor) - Agregar solo números nuevos
        IF p_numero_final > v_numero_final_actual THEN
            -- Agregar solo los números nuevos (desde el final actual + 1 hasta el nuevo final)
            CALL generate_rifa_numeros(
                p_id,
                p_sede_id,
                v_numero_final_actual + 1,
                p_numero_final,
                IFNULL(p_cantidad_digitos, 4),
                p_prefijo_numero,
                p_sufijo_numero,
                p_modificado_por,
                v_mensaje_numeros
            );
            
            -- Verificar si hubo error en la generación
            IF v_mensaje_numeros LIKE '%Error%' OR v_mensaje_numeros LIKE '%error%' THEN
                SET p_mensaje = CONCAT('Error al agregar números nuevos: ', v_mensaje_numeros);
                ROLLBACK;
                LEAVE proc;
            END IF;
            
            SET v_mensaje_cambio = CONCAT(v_mensaje_cambio, IF(v_mensaje_cambio != '', '. ', ''), 'Se agregaron números del ', (v_numero_final_actual + 1), ' al ', p_numero_final);
        END IF;
        
        -- CASO 2: REDUCCIÓN DEL RANGO (numero_final menor) - Eliminar solo números DISPONIBLES fuera del nuevo rango
        -- Los números VENDIDOS se mantienen aunque estén fuera del nuevo rango (por seguridad e integridad)
        IF p_numero_final < v_numero_final_actual THEN
            -- Contar tickets vendidos fuera del nuevo rango (solo informativo)
            SELECT COUNT(*)
            INTO v_tickets_fuera_rango
            FROM numeros_rifa
            WHERE rifa_id = p_id
              AND ticket_id IS NOT NULL
              AND numero_entero > p_numero_final;
            
            -- Eliminar SOLO números disponibles fuera del nuevo rango (los vendidos se mantienen)
            DELETE FROM numeros_rifa 
            WHERE rifa_id = p_id 
              AND numero_entero > p_numero_final
              AND ticket_id IS NULL
              AND estado = 'DISPONIBLE';
            
            IF v_tickets_fuera_rango > 0 THEN
                SET v_mensaje_cambio = CONCAT(v_mensaje_cambio, IF(v_mensaje_cambio != '', '. ', ''), 
                    'Rango reducido a ', p_numero_final, '. Se eliminaron números disponibles fuera del rango. ',
                    'Nota: Se mantienen ', v_tickets_fuera_rango, ' ticket(s) vendido(s) fuera del nuevo rango.');
            ELSE
                SET v_mensaje_cambio = CONCAT(v_mensaje_cambio, IF(v_mensaje_cambio != '', '. ', ''), 
                    'Rango reducido a ', p_numero_final, ' (números disponibles eliminados)');
            END IF;
        END IF;
        
        -- CASO 3: CAMBIO EN numero_inicial (menor) - Agregar números desde el nuevo inicial hasta el inicial anterior - 1
        IF p_numero_inicial < v_numero_inicial_actual THEN
            CALL generate_rifa_numeros(
                p_id,
                p_sede_id,
                p_numero_inicial,
                v_numero_inicial_actual - 1,
                IFNULL(p_cantidad_digitos, 4),
                p_prefijo_numero,
                p_sufijo_numero,
                p_modificado_por,
                v_mensaje_numeros
            );
            
            -- Verificar si hubo error en la generación
            IF v_mensaje_numeros LIKE '%Error%' OR v_mensaje_numeros LIKE '%error%' THEN
                SET p_mensaje = CONCAT('Error al agregar números iniciales: ', v_mensaje_numeros);
                ROLLBACK;
                LEAVE proc;
            END IF;
            
            SET v_mensaje_cambio = CONCAT(v_mensaje_cambio, IF(v_mensaje_cambio != '', '. ', ''), 'Se agregaron números del ', p_numero_inicial, ' al ', (v_numero_inicial_actual - 1));
        END IF;
        
        -- CASO 4: CAMBIO EN numero_inicial (mayor) - Eliminar solo números DISPONIBLES antes del nuevo inicial
        -- Los números VENDIDOS se mantienen aunque estén fuera del nuevo rango
        IF p_numero_inicial > v_numero_inicial_actual THEN
            -- Contar tickets vendidos antes del nuevo inicial (solo informativo)
            SELECT COUNT(*)
            INTO v_tickets_fuera_rango
            FROM numeros_rifa
            WHERE rifa_id = p_id
              AND ticket_id IS NOT NULL
              AND numero_entero < p_numero_inicial;
            
            -- Eliminar SOLO números disponibles antes del nuevo inicial (los vendidos se mantienen)
            DELETE FROM numeros_rifa 
            WHERE rifa_id = p_id 
              AND numero_entero < p_numero_inicial
              AND ticket_id IS NULL
              AND estado = 'DISPONIBLE';
            
            IF v_tickets_fuera_rango > 0 THEN
                SET v_mensaje_cambio = CONCAT(v_mensaje_cambio, IF(v_mensaje_cambio != '', '. ', ''), 
                    'Número inicial aumentado a ', p_numero_inicial, '. Se eliminaron números disponibles antes del nuevo inicial. ',
                    'Nota: Se mantienen ', v_tickets_fuera_rango, ' ticket(s) vendido(s) fuera del nuevo rango.');
            ELSE
                SET v_mensaje_cambio = CONCAT(v_mensaje_cambio, IF(v_mensaje_cambio != '', '. ', ''), 
                    'Número inicial aumentado a ', p_numero_inicial, ' (números disponibles eliminados)');
            END IF;
        END IF;
        
        -- Si se cambió prefijo/sufijo/cantidad_digitos, actualizar formato de números existentes
        IF (SELECT COUNT(*) FROM numeros_rifa WHERE rifa_id = p_id AND ticket_id IS NULL) > 0 THEN
            UPDATE numeros_rifa
            SET numero_formateado = CONCAT(
                IFNULL(p_prefijo_numero, ''),
                LPAD(numero_entero, IFNULL(p_cantidad_digitos, 4), '0'),
                IFNULL(p_sufijo_numero, '')
            )
            WHERE rifa_id = p_id
              AND ticket_id IS NULL;
        END IF;
        
            -- Asignar mensaje de cambio si existe
            IF v_mensaje_cambio != '' THEN
                SET p_mensaje = CONCAT('Rifa actualizada correctamente. ', v_mensaje_cambio);
            END IF;
        END IF;
    END IF;
    
    -- REGENERACIÓN COMPLETA (solo si se marca explícitamente el checkbox y NO hay tickets)
    IF p_regenerar_numeros = 1 THEN
        SELECT COUNT(*)
        INTO v_tickets_asignados
        FROM numeros_rifa
        WHERE rifa_id = p_id
          AND ticket_id IS NOT NULL;

        IF v_tickets_asignados > 0 THEN
            SET p_mensaje = 'No se puede regenerar completamente la numeración porque existen tickets asignados. Use el cambio de rango para agregar números nuevos.';
            ROLLBACK;
            LEAVE proc;
        END IF;

        DELETE FROM numeros_rifa WHERE rifa_id = p_id;

        CALL generate_rifa_numeros(
            p_id,
            p_sede_id,
            p_numero_inicial,
            p_numero_final,
            IFNULL(p_cantidad_digitos, 4),
            p_prefijo_numero,
            p_sufijo_numero,
            p_modificado_por,
            v_mensaje_numeros
        );
        
        IF p_mensaje IS NULL OR p_mensaje = '' THEN
            SET p_mensaje = 'Rifa actualizada correctamente. Numeración regenerada completamente.';
        END IF;
    END IF;

    COMMIT;
    IF p_mensaje IS NULL OR p_mensaje = '' THEN
        SET p_mensaje = 'Rifa actualizada correctamente';
    END IF;
END proc //

-- ==========================================================
-- 6. DESACTIVAR / ELIMINAR LÓGICAMENTE UNA RIFA
-- ==========================================================
DROP PROCEDURE IF EXISTS delete_rifa //
CREATE PROCEDURE delete_rifa (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_tickets_activos INT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al eliminar la rifa';
    END;

    START TRANSACTION;

    IF NOT EXISTS (
        SELECT 1
        FROM rifas
        WHERE id = p_id
          AND sede_id = p_sede_id
          AND estado_activo = 1
    ) THEN
        SET p_mensaje = 'La rifa no existe en esta sede o ya está inactiva';
        ROLLBACK;
        LEAVE proc;
    END IF;

    SELECT COUNT(*)
    INTO v_tickets_activos
    FROM tickets
    WHERE rifa_id = p_id
      AND sede_id = p_sede_id
      AND estado IN ('PAGO_SUBIDO', 'VALIDANDO', 'APROBADO', 'PARTICIPANDO', 'GANADOR');

    IF v_tickets_activos > 0 THEN
        SET p_mensaje = 'No se puede eliminar la rifa porque existen tickets activos';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE rifas
    SET
        estado = 'CANCELADA',
        estado_activo = 0,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    COMMIT;
    SET p_mensaje = 'Rifa eliminada correctamente';
END proc //

-- ==========================================================
-- 7. GESTIÓN DE PREMIOS ASOCIADOS A UNA RIFA
-- ==========================================================
DROP PROCEDURE IF EXISTS list_rifa_premios //
CREATE PROCEDURE list_rifa_premios (
    IN p_rifa_id INT
)
BEGIN
    SELECT
        rp.id,
        rp.rifa_id,
        rp.premio_id,
        rp.sede_id,
        rp.orden,
        rp.es_principal,
        rp.titulo,
        rp.descripcion,
        rp.cantidad,
        rp.valor_estimado,
        rp.estado,
        rp.creado_por,
        rp.fecha_creacion,
        rp.modificado_por,
        rp.fecha_modificacion,
        pr.nombre AS premio_nombre,
        pr.descripcion AS premio_descripcion,
        pr.imagen_principal AS premio_imagen,
        pr.imagen_secundaria AS premio_imagen_secundaria,
        pr.galeria_imagenes AS premio_galeria_imagenes
    FROM rifas_premios rp
    INNER JOIN premios pr ON rp.premio_id = pr.id
    WHERE rp.rifa_id = p_rifa_id
      AND rp.estado = 1
    ORDER BY rp.es_principal DESC, rp.orden ASC, rp.id ASC;
END //

DROP PROCEDURE IF EXISTS register_rifa_premio //
CREATE PROCEDURE register_rifa_premio (
    IN p_rifa_id INT,
    IN p_sede_id INT,
    IN p_premio_id INT,
    IN p_orden INT,
    IN p_es_principal TINYINT,
    IN p_titulo VARCHAR(200),
    IN p_descripcion TEXT,
    IN p_cantidad INT,
    IN p_valor_estimado DECIMAL(12,2),
    IN p_creado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al registrar el premio de la rifa';
    END;

    START TRANSACTION;

    IF NOT EXISTS (SELECT 1 FROM rifas WHERE id = p_rifa_id AND sede_id = p_sede_id) THEN
        SET p_mensaje = 'La rifa no existe en la sede indicada';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF NOT EXISTS (SELECT 1 FROM premios WHERE id = p_premio_id AND sede_id = p_sede_id AND estado = 1) THEN
        SET p_mensaje = 'El premio seleccionado no existe o está inactivo';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1 FROM rifas_premios
        WHERE rifa_id = p_rifa_id
          AND premio_id = p_premio_id
    ) THEN
        SET p_mensaje = 'El premio ya está asociado a esta rifa';
        ROLLBACK;
        LEAVE proc;
    END IF;

    INSERT INTO rifas_premios (
        sede_id,
        rifa_id,
        premio_id,
        orden,
        es_principal,
        titulo,
        descripcion,
        cantidad,
        valor_estimado,
        estado,
        creado_por,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        p_rifa_id,
        p_premio_id,
        IFNULL(p_orden, 1),
        IFNULL(p_es_principal, 0),
        p_titulo,
        p_descripcion,
        IFNULL(p_cantidad, 1),
        p_valor_estimado,
        1,
        p_creado_por,
        NOW(),
        NOW()
    );

    IF IFNULL(p_es_principal, 0) = 1 THEN
        UPDATE rifas_premios
        SET es_principal = 0,
            modificado_por = p_creado_por,
            fecha_modificacion = NOW()
        WHERE rifa_id = p_rifa_id
          AND id <> LAST_INSERT_ID();

        UPDATE rifas
        SET premio_id = p_premio_id,
            modificado_por = p_creado_por,
            fecha_modificacion = NOW()
        WHERE id = p_rifa_id;
    END IF;

    COMMIT;
    SET p_mensaje = 'Premio asociado correctamente a la rifa';
END proc //

DROP PROCEDURE IF EXISTS update_rifa_premio //
CREATE PROCEDURE update_rifa_premio (
    IN p_id INT,
    IN p_rifa_id INT,
    IN p_sede_id INT,
    IN p_orden INT,
    IN p_es_principal TINYINT,
    IN p_titulo VARCHAR(200),
    IN p_descripcion TEXT,
    IN p_cantidad INT,
    IN p_valor_estimado DECIMAL(12,2),
    IN p_estado INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar el premio de la rifa';
    END;

    START TRANSACTION;

    IF NOT EXISTS (
        SELECT 1 FROM rifas_premios
        WHERE id = p_id
          AND rifa_id = p_rifa_id
          AND sede_id = p_sede_id
    ) THEN
        SET p_mensaje = 'El premio asociado no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE rifas_premios
    SET
        orden = IFNULL(p_orden, orden),
        es_principal = IFNULL(p_es_principal, es_principal),
        titulo = p_titulo,
        descripcion = p_descripcion,
        cantidad = IFNULL(p_cantidad, cantidad),
        valor_estimado = p_valor_estimado,
        estado = IFNULL(p_estado, estado),
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id;

    IF IFNULL(p_es_principal, 0) = 1 THEN
        UPDATE rifas_premios
        SET es_principal = 0,
            modificado_por = p_modificado_por,
            fecha_modificacion = NOW()
        WHERE rifa_id = p_rifa_id
          AND id <> p_id;

        UPDATE rifas
        SET premio_id = (SELECT premio_id FROM rifas_premios WHERE id = p_id),
            modificado_por = p_modificado_por,
            fecha_modificacion = NOW()
        WHERE id = p_rifa_id;
    END IF;

    COMMIT;
    SET p_mensaje = 'Premio de la rifa actualizado correctamente';
END proc //

DROP PROCEDURE IF EXISTS delete_rifa_premio //
CREATE PROCEDURE delete_rifa_premio (
    IN p_id INT,
    IN p_rifa_id INT,
    IN p_sede_id INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_es_principal TINYINT DEFAULT 0;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al eliminar el premio de la rifa';
    END;

    START TRANSACTION;

    SELECT es_principal
    INTO v_es_principal
    FROM rifas_premios
    WHERE id = p_id
      AND rifa_id = p_rifa_id
      AND sede_id = p_sede_id;

    IF v_es_principal IS NULL THEN
        SET p_mensaje = 'El premio asociado no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE rifas_premios
    SET estado = 0,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id;

    IF v_es_principal = 1 THEN
        UPDATE rifas
        SET premio_id = NULL,
            modificado_por = p_modificado_por,
            fecha_modificacion = NOW()
        WHERE id = p_rifa_id;
    END IF;

    COMMIT;
    SET p_mensaje = 'Premio eliminado de la rifa correctamente';
END proc //

-- ==========================================================
-- 8. GESTIÓN DE NÚMEROS / CARTILLAS
-- ==========================================================
DROP PROCEDURE IF EXISTS list_rifa_numeros //
CREATE PROCEDURE list_rifa_numeros (
    IN p_rifa_id INT,
    IN p_estado VARCHAR(20)
)
BEGIN
    SELECT
        nr.*,
        p.nombres,
        p.apellidos,
        p.numero_documento,
        p.email,
        p.telefono
    FROM numeros_rifa nr
    LEFT JOIN tickets t ON nr.ticket_id = t.id
    LEFT JOIN personas p ON t.persona_id = p.id
    WHERE nr.rifa_id = p_rifa_id
      AND (p_estado IS NULL OR p_estado = '' OR nr.estado = p_estado)
    ORDER BY nr.numero_entero ASC;
END //

DROP PROCEDURE IF EXISTS update_estado_numero_rifa //
CREATE PROCEDURE update_estado_numero_rifa (
    IN p_numero_id INT,
    IN p_rifa_id INT,
    IN p_estado VARCHAR(20),
    IN p_ticket_id INT,
    IN p_reservado_hasta DATETIME,
    IN p_reservado_por_sesion VARCHAR(255),
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar el estado del número';
    END;

    START TRANSACTION;

    IF NOT EXISTS (
        SELECT 1 FROM numeros_rifa
        WHERE id = p_numero_id
          AND rifa_id = p_rifa_id
    ) THEN
        SET p_mensaje = 'El número seleccionado no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE numeros_rifa
    SET
        estado = p_estado,
        ticket_id = p_ticket_id,
        reservado_hasta = p_reservado_hasta,
        reservado_por_sesion = p_reservado_por_sesion,
        fecha_reserva = CASE WHEN p_estado = 'RESERVADO' THEN NOW() ELSE fecha_reserva END,
        fecha_venta = CASE WHEN p_estado = 'VENDIDO' THEN NOW() ELSE fecha_venta END,
        fecha_modificacion = NOW()
    WHERE id = p_numero_id;

    COMMIT;
    SET p_mensaje = 'Estado del número actualizado correctamente';
END proc //

-- ==========================================================
-- PROCEDIMIENTO PARA CERRAR RIFA
-- ==========================================================
DROP PROCEDURE IF EXISTS cerrar_rifa //
CREATE PROCEDURE cerrar_rifa (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al cerrar la rifa';
    END;

    START TRANSACTION;

    IF NOT EXISTS (SELECT 1 FROM rifas WHERE id = p_id AND sede_id = p_sede_id) THEN
        SET p_mensaje = 'La rifa no existe en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE rifas
    SET estado = 'CERRADA',
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    COMMIT;
    SET p_mensaje = 'Rifa cerrada correctamente';
END //

DELIMITER ;


