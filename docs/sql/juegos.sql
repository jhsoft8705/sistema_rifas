-- =============================================
-- STORED PROCEDURES PARA PROCESO DE JUEGO DE RIFAS (MySQL)
-- Basado en la estructura definida en docs/sql/bd_rifas_mysql.sql
-- =============================================

DELIMITER //

-- 1. PROCEDURE PARA LISTAR RIFAS LISTAS PARA JUGAR
-- (Todas las rifas donde todos los números están vendidos)
DROP PROCEDURE IF EXISTS list_rifas_para_jugar //
CREATE PROCEDURE list_rifas_para_jugar (
    IN p_sede_id INT
)
BEGIN
    SELECT
        r.id,
        r.sede_id,
        s.nombre AS sede_nombre,
        r.codigo,
        r.nombre,
        r.descripcion,
        r.numero_intentos,
        r.intento_ganador,
        r.precio_ticket,
        r.cantidad_maxima_tickets,
        r.tickets_vendidos,
        r.fecha_sorteo,
        r.fecha_sorteo_realizado,
        -- Contador de intentos actuales (máximo intento de todos los premios)
        (SELECT COALESCE(MAX(intento_numero), 0)
         FROM intentos_juego
         WHERE rifa_id = r.id) AS intentos_actuales,
        -- Verificar si todos los números están vendidos
        (SELECT COUNT(*) FROM numeros_rifa nr 
         WHERE nr.rifa_id = r.id 
           AND nr.estado = 'VENDIDO') AS numeros_vendidos,
        (SELECT COUNT(*) FROM numeros_rifa nr 
         WHERE nr.rifa_id = r.id) AS total_numeros,
        -- Total de participantes (personas únicas)
        (SELECT COUNT(DISTINCT t.persona_id)
         FROM tickets t
         INNER JOIN numeros_rifa nr ON nr.ticket_id = t.id
         WHERE t.rifa_id = r.id
           AND t.estado IN ('APROBADO', 'PARTICIPANDO', 'GANADOR')
           AND nr.estado = 'VENDIDO') AS total_participantes,
        -- Total de premios de la rifa
        (SELECT COUNT(*) FROM rifas_premios rp WHERE rp.rifa_id = r.id AND rp.estado = 1) AS total_premios,
        -- Premios con ganador
        (SELECT COUNT(DISTINCT g.rifa_premio_id) 
         FROM ganadores g
         INNER JOIN rifas_premios rp ON g.rifa_premio_id = rp.id
         WHERE g.rifa_id = r.id AND rp.estado = 1) AS premios_ganados,
        -- Verificar si ya hay ganador
        (SELECT COUNT(*) FROM tickets t 
         WHERE t.rifa_id = r.id 
           AND t.estado = 'GANADOR') AS tiene_ganador,
        -- Estado del juego
        CASE 
            WHEN (SELECT COUNT(*) FROM tickets t WHERE t.rifa_id = r.id AND t.estado = 'GANADOR') > 0 THEN 'GANADOR_DEFINIDO'
            WHEN (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id AND nr.estado = 'VENDIDO') = 
                 (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id) THEN 'LISTA_PARA_JUGAR'
            ELSE 'EN_VENTA'
        END AS estado_juego
    FROM rifas r
    INNER JOIN sedes s ON r.sede_id = s.id
    WHERE r.sede_id = p_sede_id
      AND r.estado_activo = 1
      -- La rifa debe tener números generados
      AND (SELECT COUNT(*) FROM numeros_rifa nr WHERE nr.rifa_id = r.id) > 0
      -- La rifa debe tener al menos un premio asociado
      AND (SELECT COUNT(*) FROM rifas_premios rp WHERE rp.rifa_id = r.id AND rp.estado = 1) > 0
    ORDER BY r.fecha_sorteo ASC, r.fecha_creacion DESC;
END //

-- 2. PROCEDURE PARA OBTENER PARTICIPANTES DE UNA RIFA
DROP PROCEDURE IF EXISTS list_participantes_rifa //
CREATE PROCEDURE list_participantes_rifa (
    IN p_rifa_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT DISTINCT
        p.id AS persona_id,
        p.nombres,
        p.apellidos,
        p.tipo_documento,
        p.numero_documento,
        p.email,
        p.telefono,
        CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo,
        CONCAT(p.tipo_documento, ': ', p.numero_documento) AS documento_completo,
        -- Contar tickets de esta persona en esta rifa
        COUNT(DISTINCT t.id) AS cantidad_tickets,
        -- Números que tiene esta persona
        GROUP_CONCAT(DISTINCT nr.numero_formateado ORDER BY nr.numero_entero ASC SEPARATOR ', ') AS numeros_comprados
    FROM tickets t
    INNER JOIN personas p ON t.persona_id = p.id
    INNER JOIN numeros_rifa nr ON nr.ticket_id = t.id
    WHERE t.rifa_id = p_rifa_id
      AND t.sede_id = p_sede_id
      AND t.estado IN ('APROBADO', 'PARTICIPANDO', 'GANADOR')
      AND nr.estado = 'VENDIDO'
    GROUP BY p.id, p.nombres, p.apellidos, p.tipo_documento, p.numero_documento, p.email, p.telefono
    ORDER BY p.apellidos ASC, p.nombres ASC;
END //

-- 3. PROCEDURE PARA JUGAR PREMIO DE RIFA (SELECCIONAR NÚMERO ALEATORIO)
DROP PROCEDURE IF EXISTS jugar_premio_rifa //
CREATE PROCEDURE jugar_premio_rifa (
    IN p_rifa_id INT,
    IN p_rifa_premio_id INT,
    IN p_sede_id INT,
    IN p_jugado_por VARCHAR(50),
    OUT p_numero_seleccionado_id INT,
    OUT p_numero_formateado VARCHAR(50),
    OUT p_persona_seleccionada_id INT,
    OUT p_nombre_completo VARCHAR(200),
    OUT p_ticket_id INT,
    OUT p_intento_actual INT,
    OUT p_es_ganador TINYINT,
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_numero_intentos INT;
    DECLARE v_intento_ganador INT;
    DECLARE v_intentos_actuales INT;
    DECLARE v_total_numeros_disponibles INT;
    DECLARE v_numero_aleatorio_id INT;
    DECLARE v_numero_formateado_val VARCHAR(50);
    DECLARE v_persona_id_val INT;
    DECLARE v_ticket_id_val INT;
    DECLARE v_nuevo_intento INT;
    DECLARE v_ya_tiene_ganador INT DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_numero_seleccionado_id = NULL;
        SET p_numero_formateado = NULL;
        SET p_persona_seleccionada_id = NULL;
        SET p_nombre_completo = NULL;
        SET p_ticket_id = NULL;
        SET p_intento_actual = 0;
        SET p_es_ganador = 0;
        SET p_mensaje = 'Error al ejecutar el juego';
    END;

    START TRANSACTION;

    -- Verificar que la rifa existe y pertenece a la sede
    IF NOT EXISTS (
        SELECT 1 FROM rifas
        WHERE id = p_rifa_id
          AND sede_id = p_sede_id
          AND estado_activo = 1
    ) THEN
        SET p_mensaje = 'La rifa no existe o no está activa';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Obtener configuración de la rifa
    SELECT numero_intentos, intento_ganador INTO v_numero_intentos, v_intento_ganador
    FROM rifas
    WHERE id = p_rifa_id;

    -- Verificar si este premio específico ya tiene ganador
    SELECT COUNT(*) INTO v_ya_tiene_ganador
    FROM ganadores
    WHERE rifa_premio_id = p_rifa_premio_id;

    IF v_ya_tiene_ganador > 0 THEN
        SET p_mensaje = 'Este premio ya tiene un ganador definido';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Contar intentos actuales para este premio específico
    SELECT COALESCE(MAX(intento_numero), 0) INTO v_intentos_actuales
    FROM intentos_juego
    WHERE rifa_id = p_rifa_id
      AND rifa_premio_id = p_rifa_premio_id;

    -- Verificar que haya números disponibles para jugar
    -- Excluir números que ya ganaron OTRO premio de esta rifa
    SELECT COUNT(*) INTO v_total_numeros_disponibles
    FROM numeros_rifa nr
    INNER JOIN tickets t ON t.id = nr.ticket_id
    WHERE nr.rifa_id = p_rifa_id
      AND nr.estado = 'VENDIDO'
      AND t.estado IN ('APROBADO', 'PARTICIPANDO')
      -- Excluir números que ya ganaron OTRO premio
      AND nr.id NOT IN (
          SELECT g.numero_id
          FROM ganadores g
          WHERE g.rifa_id = p_rifa_id
            AND g.rifa_premio_id <> p_rifa_premio_id
            AND g.numero_id IS NOT NULL
      );

    IF v_total_numeros_disponibles = 0 THEN
        SET p_mensaje = 'No hay números disponibles para jugar';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Calcular nuevo intento
    SET v_nuevo_intento = v_intentos_actuales + 1;

    -- Seleccionar NÚMERO aleatorio (no persona)
    -- Cada número tiene la misma probabilidad de ser seleccionado
    -- Excluir números que ya ganaron OTRO premio de esta rifa
    SELECT 
        nr.id,
        nr.numero_formateado,
        t.persona_id,
        t.id AS ticket_id
    INTO 
        v_numero_aleatorio_id,
        v_numero_formateado_val,
        v_persona_id_val,
        v_ticket_id_val
    FROM numeros_rifa nr
    INNER JOIN tickets t ON t.id = nr.ticket_id
    WHERE nr.rifa_id = p_rifa_id
      AND nr.estado = 'VENDIDO'
      AND t.estado IN ('APROBADO', 'PARTICIPANDO')
      -- Excluir números que ya ganaron OTRO premio
      AND nr.id NOT IN (
          SELECT g.numero_id
          FROM ganadores g
          WHERE g.rifa_id = p_rifa_id
            AND g.rifa_premio_id <> p_rifa_premio_id
            AND g.numero_id IS NOT NULL
      )
    ORDER BY RAND()
    LIMIT 1;

    IF v_numero_aleatorio_id IS NULL THEN
        SET p_mensaje = 'No se pudo seleccionar un número';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Obtener nombre completo de la persona
    SELECT CONCAT(nombres, ' ', apellidos) INTO p_nombre_completo
    FROM personas
    WHERE id = v_persona_id_val;

    -- Asignar valores de salida
    SET p_numero_seleccionado_id = v_numero_aleatorio_id;
    SET p_numero_formateado = v_numero_formateado_val;
    SET p_persona_seleccionada_id = v_persona_id_val;
    SET p_ticket_id = v_ticket_id_val;
    SET p_intento_actual = v_nuevo_intento;

    -- Registrar el intento en la tabla de historial (con número seleccionado)
    INSERT INTO intentos_juego (
        rifa_id,
        rifa_premio_id,
        persona_id,
        numero_id,
        intento_numero,
        es_ganador,
        fecha_intento,
        jugado_por
    ) VALUES (
        p_rifa_id,
        p_rifa_premio_id,
        v_persona_id_val,
        v_numero_aleatorio_id,
        v_nuevo_intento,
        0, -- Aún no sabemos si es ganador
        NOW(),
        p_jugado_por
    );

    -- Verificar si este intento es el ganador
    IF v_nuevo_intento >= v_intento_ganador THEN
        -- Este es el ganador de este premio
        SET p_es_ganador = 1;
        
        -- Actualizar el intento como ganador
        UPDATE intentos_juego
        SET es_ganador = 1
        WHERE rifa_id = p_rifa_id
          AND rifa_premio_id = p_rifa_premio_id
          AND intento_numero = v_nuevo_intento
          AND numero_id = v_numero_aleatorio_id;

        -- Actualizar rifa (solo fecha de sorteo si es el primer premio)
        IF NOT EXISTS (SELECT 1 FROM rifas WHERE id = p_rifa_id AND fecha_sorteo_realizado IS NOT NULL) THEN
            UPDATE rifas
            SET fecha_sorteo_realizado = NOW(),
                modificado_por = p_jugado_por,
                fecha_modificacion = NOW()
            WHERE id = p_rifa_id;
        END IF;

        SET p_mensaje = CONCAT('¡GANADOR! Número ', v_numero_formateado_val, ' (', p_nombre_completo, ') ha ganado el premio en el intento ', v_nuevo_intento, '. Debe registrar los datos del ganador.');
    ELSE
        -- No es ganador aún, solo número seleccionado
        SET p_es_ganador = 0;
        SET p_mensaje = CONCAT('Número seleccionado: ', v_numero_formateado_val, ' (', p_nombre_completo, ') en el intento ', v_nuevo_intento);
    END IF;

    COMMIT;
END //

-- 4. PROCEDURE PARA LISTAR PREMIOS DE UNA RIFA PARA JUGAR
DROP PROCEDURE IF EXISTS list_premios_rifa_para_jugar //
CREATE PROCEDURE list_premios_rifa_para_jugar (
    IN p_rifa_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        rp.id AS rifa_premio_id,
        rp.rifa_id,
        rp.premio_id,
        rp.orden,
        rp.es_principal,
        rp.titulo,
        rp.descripcion,
        pr.codigo AS premio_codigo,
        pr.nombre AS premio_nombre,
        pr.descripcion AS premio_descripcion,
        pr.valor_estimado,
        -- Verificar si ya tiene ganador
        (SELECT COUNT(*) FROM ganadores g WHERE g.rifa_premio_id = rp.id) AS tiene_ganador,
        -- Información del ganador si existe
        (SELECT CONCAT(g.nombre_completo, ' - ', g.documento_completo)
         FROM ganadores g
         WHERE g.rifa_premio_id = rp.id
         LIMIT 1) AS ganador_info
    FROM rifas_premios rp
    INNER JOIN premios pr ON rp.premio_id = pr.id
    WHERE rp.rifa_id = p_rifa_id
      AND rp.sede_id = p_sede_id
      AND rp.estado = 1
    ORDER BY rp.es_principal DESC, rp.orden ASC, rp.id ASC;
END //

-- 5. PROCEDURE PARA OBTENER INFORMACIÓN DEL JUEGO DE UN PREMIO
DROP PROCEDURE IF EXISTS get_info_juego_premio //
CREATE PROCEDURE get_info_juego_premio (
    IN p_rifa_id INT,
    IN p_rifa_premio_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        r.id AS rifa_id,
        r.codigo AS rifa_codigo,
        r.nombre AS rifa_nombre,
        r.numero_intentos,
        r.intento_ganador,
        r.fecha_sorteo,
        r.fecha_sorteo_realizado,
        rp.id AS rifa_premio_id,
        rp.premio_id,
        pr.nombre AS premio_nombre,
        pr.codigo AS premio_codigo,
        -- Contador de intentos actuales para este premio específico
        (SELECT COALESCE(MAX(intento_numero), 0)
         FROM intentos_juego
         WHERE rifa_id = r.id 
           AND rifa_premio_id = rp.id) AS intentos_actuales,
        -- Verificar si este premio tiene ganador
        (SELECT COUNT(*) FROM ganadores g 
         WHERE g.rifa_premio_id = rp.id) AS tiene_ganador,
        -- Información del ganador si existe
        (SELECT CONCAT(g.nombre_completo, ' - ', g.documento_completo)
         FROM ganadores g
         WHERE g.rifa_premio_id = rp.id
         LIMIT 1) AS ganador_nombre,
        -- Número ganador específico
        (SELECT nr.numero_formateado
         FROM ganadores g
         INNER JOIN numeros_rifa nr ON nr.id = g.numero_id
         WHERE g.rifa_premio_id = rp.id
         LIMIT 1) AS numero_ganador,
        -- Total de participantes de la rifa
        (SELECT COUNT(DISTINCT t.persona_id)
         FROM tickets t
         INNER JOIN numeros_rifa nr ON nr.ticket_id = t.id
         WHERE t.rifa_id = r.id
           AND t.estado IN ('APROBADO', 'PARTICIPANDO', 'GANADOR')
           AND nr.estado = 'VENDIDO') AS total_participantes,
        -- Total de números vendidos
        (SELECT COUNT(*) FROM numeros_rifa nr 
         WHERE nr.rifa_id = r.id 
           AND nr.estado = 'VENDIDO') AS numeros_vendidos,
        -- Total de números
        (SELECT COUNT(*) FROM numeros_rifa nr 
         WHERE nr.rifa_id = r.id) AS total_numeros
    FROM rifas r
    INNER JOIN rifas_premios rp ON rp.rifa_id = r.id
    INNER JOIN premios pr ON rp.premio_id = pr.id
    WHERE r.id = p_rifa_id
      AND rp.id = p_rifa_premio_id
      AND r.sede_id = p_sede_id;
END //

-- 6. PROCEDURE PARA VERIFICAR SI TODOS LOS PREMIOS TIENEN GANADOR
DROP PROCEDURE IF EXISTS check_rifa_completa //
CREATE PROCEDURE check_rifa_completa (
    IN p_rifa_id INT,
    IN p_sede_id INT,
    OUT p_todos_premios_ganados TINYINT,
    OUT p_total_premios INT,
    OUT p_premios_ganados INT
)
BEGIN
    DECLARE v_total INT DEFAULT 0;
    DECLARE v_ganados INT DEFAULT 0;
    
    -- Contar total de premios activos
    SELECT COUNT(*) INTO v_total
    FROM rifas_premios
    WHERE rifa_id = p_rifa_id
      AND sede_id = p_sede_id
      AND estado = 1;
    
    -- Contar premios con ganador
    SELECT COUNT(DISTINCT g.rifa_premio_id) INTO v_ganados
    FROM ganadores g
    INNER JOIN rifas_premios rp ON g.rifa_premio_id = rp.id
    WHERE g.rifa_id = p_rifa_id
      AND rp.estado = 1;
    
    SET p_total_premios = v_total;
    SET p_premios_ganados = v_ganados;
    SET p_todos_premios_ganados = IF(v_total > 0 AND v_ganados = v_total, 1, 0);
END //

DELIMITER ;
