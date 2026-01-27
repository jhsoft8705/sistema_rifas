-- =============================================
-- TABLA Y PROCEDIMIENTOS PARA GANADORES DE PREMIOS
-- =============================================

DELIMITER //

-- 1. PROCEDURE PARA REGISTRAR GANADOR
DROP PROCEDURE IF EXISTS register_ganador //
CREATE PROCEDURE register_ganador (
    IN p_sede_id INT,
    IN p_rifa_id INT,
    IN p_rifa_premio_id INT,
    IN p_premio_id INT,
    IN p_persona_id INT,
    IN p_ticket_id INT,
    IN p_numero_id INT,
    IN p_direccion_envio VARCHAR(500),
    IN p_ciudad_envio VARCHAR(100),
    IN p_pais_envio VARCHAR(100),
    IN p_publicar_web TINYINT,
    IN p_intento_ganador INT,
    IN p_jugado_por VARCHAR(50),
    IN p_creado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_nombre_completo VARCHAR(200);
    DECLARE v_documento_completo VARCHAR(50);
    DECLARE v_email VARCHAR(100);
    DECLARE v_telefono VARCHAR(15);
    DECLARE v_ya_ganador INT DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al registrar el ganador';
    END;

    START TRANSACTION;

    -- Verificar que no sea ganador de otro premio de la misma rifa
    SELECT COUNT(*) INTO v_ya_ganador
    FROM ganadores
    WHERE rifa_id = p_rifa_id
      AND persona_id = p_persona_id
      AND id <> IFNULL((SELECT id FROM ganadores WHERE rifa_premio_id = p_rifa_premio_id LIMIT 1), 0);

    IF v_ya_ganador > 0 THEN
        SET p_mensaje = 'Esta persona ya es ganadora de otro premio en esta rifa';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Obtener datos de la persona
    SELECT 
        CONCAT(nombres, ' ', apellidos),
        CONCAT(tipo_documento, ': ', numero_documento),
        email,
        telefono
    INTO v_nombre_completo, v_documento_completo, v_email, v_telefono
    FROM personas
    WHERE id = p_persona_id;

    IF v_nombre_completo IS NULL THEN
        SET p_mensaje = 'La persona no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Obtener ticket_id si no se proporcionó
    IF p_ticket_id IS NULL THEN
        SELECT id INTO p_ticket_id
        FROM tickets
        WHERE rifa_id = p_rifa_id
          AND persona_id = p_persona_id
          AND estado IN ('APROBADO', 'PARTICIPANDO')
        LIMIT 1;
    END IF;

    -- Insertar ganador
    INSERT INTO ganadores (
        sede_id,
        rifa_id,
        rifa_premio_id,
        premio_id,
        persona_id,
        ticket_id,
        numero_id,
        nombre_completo,
        documento_completo,
        email,
        telefono,
        direccion_envio,
        ciudad_envio,
        pais_envio,
        publicar_web,
        intento_ganador,
        fecha_ganador,
        jugado_por,
        creado_por,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        p_rifa_id,
        p_rifa_premio_id,
        p_premio_id,
        p_persona_id,
        p_ticket_id,
        p_numero_id,
        v_nombre_completo,
        v_documento_completo,
        v_email,
        v_telefono,
        p_direccion_envio,
        p_ciudad_envio,
        p_pais_envio,
        IFNULL(p_publicar_web, 0),
        p_intento_ganador,
        NOW(),
        p_jugado_por,
        p_creado_por,
        NOW(),
        NOW()
    );

    -- Actualizar TODOS los tickets de esta persona a GANADOR para este premio
    UPDATE tickets
    SET estado = 'GANADOR',
        fecha_modificacion = NOW(),
        modificado_por = p_creado_por
    WHERE rifa_id = p_rifa_id
      AND persona_id = p_persona_id
      AND estado IN ('APROBADO', 'PARTICIPANDO');

    -- Actualizar dirección de la persona si se proporciona
    IF p_direccion_envio IS NOT NULL AND p_direccion_envio <> '' THEN
        UPDATE personas
        SET direccion = p_direccion_envio,
            ciudad = IFNULL(p_ciudad_envio, ciudad),
            pais = IFNULL(p_pais_envio, pais),
            fecha_modificacion = NOW(),
            modificado_por = p_creado_por
        WHERE id = p_persona_id;
    END IF;

    COMMIT;
    SET p_mensaje = 'Ganador registrado correctamente';
END //

-- 2. PROCEDURE PARA LISTAR GANADORES DE UNA RIFA (CON NÚMEROS GANADORES)
DROP PROCEDURE IF EXISTS list_ganadores_rifa //
CREATE PROCEDURE list_ganadores_rifa (
    IN p_rifa_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        g.id,
        g.rifa_id,
        g.rifa_premio_id,
        g.premio_id,
        g.persona_id,
        g.ticket_id,
        g.nombre_completo,
        g.documento_completo,
        g.email,
        g.telefono,
        g.direccion_envio,
        g.ciudad_envio,
        g.pais_envio,
        g.publicar_web,
        g.intento_ganador,
        g.fecha_ganador,
        g.jugado_por,
        pr.nombre AS premio_nombre,
        pr.codigo AS premio_codigo,
        rp.orden AS premio_orden,
        rp.titulo AS premio_titulo,
        -- Número ganador específico
        nr_ganador.numero_formateado AS numero_ganador,
        nr_ganador.numero_entero AS numero_ganador_entero,
        nr_ganador.id AS numero_id,
        -- Códigos de tickets ganadores
        (SELECT GROUP_CONCAT(DISTINCT t.codigo_ticket ORDER BY t.id ASC SEPARATOR ', ')
         FROM tickets t
         WHERE t.persona_id = g.persona_id
           AND t.rifa_id = g.rifa_id
           AND t.estado = 'GANADOR') AS tickets_ganadores
    FROM ganadores g
    INNER JOIN premios pr ON g.premio_id = pr.id
    INNER JOIN rifas_premios rp ON g.rifa_premio_id = rp.id
    WHERE g.rifa_id = p_rifa_id
      AND g.sede_id = p_sede_id
    ORDER BY rp.orden ASC, g.fecha_ganador ASC;
END //

-- 4. PROCEDURE PARA OBTENER NÚMERO GANADOR DE UN GANADOR ESPECÍFICO
DROP PROCEDURE IF EXISTS get_numeros_ganador //
CREATE PROCEDURE get_numeros_ganador (
    IN p_ganador_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        nr.id AS numero_id,
        nr.numero_entero,
        nr.numero_formateado,
        t.id AS ticket_id,
        t.codigo_ticket,
        t.numero_boleto,
        t.numero_boleto_entero
    FROM ganadores g
    INNER JOIN numeros_rifa nr ON nr.id = g.numero_id
    INNER JOIN tickets t ON t.id = nr.ticket_id
    WHERE g.id = p_ganador_id
      AND g.sede_id = p_sede_id;
END //

-- 3. PROCEDURE PARA VERIFICAR SI UN PREMIO YA TIENE GANADOR
DROP PROCEDURE IF EXISTS check_premio_ganador //
CREATE PROCEDURE check_premio_ganador (
    IN p_rifa_premio_id INT,
    OUT p_tiene_ganador TINYINT,
    OUT p_ganador_id INT
)
BEGIN
    DECLARE v_count INT DEFAULT 0;
    
    SELECT COUNT(*), COALESCE(MAX(id), 0) INTO v_count, p_ganador_id
    FROM ganadores
    WHERE rifa_premio_id = p_rifa_premio_id;
    
    SET p_tiene_ganador = IF(v_count > 0, 1, 0);
END //

DELIMITER ;
