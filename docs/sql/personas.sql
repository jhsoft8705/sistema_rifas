-- =============================================
-- STORED PROCEDURES PARA GESTIÓN DE PERSONAS/CLIENTES
-- Tabla centralizada para evitar duplicación de datos de participantes
-- =============================================

DELIMITER //

-- ==========================================================
-- 1. BUSCAR O CREAR PERSONA POR NÚMERO DE DOCUMENTO
-- ==========================================================
DROP PROCEDURE IF EXISTS get_or_create_persona //
CREATE PROCEDURE get_or_create_persona (
    IN p_sede_id INT,
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_tipo_documento VARCHAR(20),
    IN p_numero_documento VARCHAR(20),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_direccion VARCHAR(500),
    IN p_ciudad VARCHAR(100),
    IN p_pais VARCHAR(100),
    OUT p_persona_id INT,
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_persona_existente INT;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_persona_id = NULL;
        SET p_mensaje = 'Error al buscar o crear la persona';
    END;

    START TRANSACTION;

    -- Buscar si existe una persona con ese número de documento en esta sede
    SELECT id INTO v_persona_existente
    FROM personas
    WHERE sede_id = p_sede_id
      AND tipo_documento = p_tipo_documento
      AND numero_documento = p_numero_documento
    LIMIT 1;

    IF v_persona_existente IS NOT NULL THEN
        -- Persona existe: actualizar datos con la información más reciente
        UPDATE personas
        SET nombres = p_nombres,
            apellidos = p_apellidos,
            email = COALESCE(NULLIF(p_email, ''), email),
            telefono = COALESCE(NULLIF(p_telefono, ''), telefono),
            direccion = COALESCE(NULLIF(p_direccion, ''), direccion),
            ciudad = COALESCE(NULLIF(p_ciudad, ''), ciudad),
            pais = COALESCE(NULLIF(p_pais, ''), pais),
            fecha_modificacion = NOW(),
            modificado_por = 'SISTEMA'
        WHERE id = v_persona_existente;
        
        SET p_persona_id = v_persona_existente;
        SET p_mensaje = 'Persona encontrada y actualizada';
    ELSE
        -- Persona no existe: crear nueva
        INSERT INTO personas (
            sede_id,
            nombres,
            apellidos,
            tipo_documento,
            numero_documento,
            email,
            telefono,
            direccion,
            ciudad,
            pais,
            fecha_creacion,
            fecha_modificacion,
            creado_por
        ) VALUES (
            p_sede_id,
            p_nombres,
            p_apellidos,
            p_tipo_documento,
            p_numero_documento,
            p_email,
            p_telefono,
            p_direccion,
            p_ciudad,
            p_pais,
            NOW(),
            NOW(),
            'SISTEMA'
        );
        
        SET p_persona_id = LAST_INSERT_ID();
        SET p_mensaje = 'Persona creada correctamente';
    END IF;

    COMMIT;
END proc //

-- ==========================================================
-- 2. LISTAR PERSONAS POR SEDE
-- ==========================================================
DROP PROCEDURE IF EXISTS list_personas //
CREATE PROCEDURE list_personas (
    IN p_sede_id INT,
    IN p_busqueda VARCHAR(255)
)
BEGIN
    SELECT
        p.*,
        (SELECT COUNT(*) FROM tickets t WHERE t.persona_id = p.id) AS total_tickets,
        (SELECT COUNT(*) FROM tickets t WHERE t.persona_id = p.id AND t.estado = 'APROBADO') AS tickets_aprobados,
        (SELECT MAX(t.fecha_creacion) FROM tickets t WHERE t.persona_id = p.id) AS ultima_compra
    FROM personas p
    WHERE p.sede_id = p_sede_id
      AND (
          p_busqueda IS NULL OR p_busqueda = '' OR
          p.nombres LIKE CONCAT('%', p_busqueda, '%') OR
          p.apellidos LIKE CONCAT('%', p_busqueda, '%') OR
          p.numero_documento LIKE CONCAT('%', p_busqueda, '%') OR
          p.email LIKE CONCAT('%', p_busqueda, '%')
      )
    ORDER BY p.fecha_creacion DESC;
END //

DELIMITER ;


-- =============================================
-- STORED PROCEDURES PARA MANTENIMIENTO DE PERSONAS (MySQL)
-- Basado en la estructura definida en docs/sql/bd_rifas_mysql.sql
-- =============================================

DELIMITER //

-- 1. PROCEDURE PARA LISTAR PERSONAS POR SEDE
DROP PROCEDURE IF EXISTS list_personas //
CREATE PROCEDURE list_personas (
    IN p_sede_id INT
)
BEGIN
    SELECT
        p.id,
        p.sede_id,
        s.nombre AS sede_nombre,
        p.nombres,
        p.apellidos,
        p.tipo_documento,
        p.numero_documento,
        p.email,
        p.telefono,
        p.direccion,
        p.ciudad,
        p.pais,
        CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo,
        CONCAT(p.tipo_documento, ': ', p.numero_documento) AS documento_completo,
        p.fecha_creacion,
        p.fecha_modificacion,
        p.creado_por,
        p.modificado_por,
        -- Estadísticas de participación
        (SELECT COUNT(*) FROM tickets t WHERE t.persona_id = p.id) AS total_tickets,
        (SELECT COUNT(DISTINCT t.rifa_id) FROM tickets t WHERE t.persona_id = p.id) AS total_rifas_participadas
    FROM personas p
    INNER JOIN sedes s ON p.sede_id = s.id
    WHERE p.sede_id = p_sede_id
    ORDER BY p.fecha_creacion DESC;
END //

-- 2. PROCEDURE PARA OBTENER PERSONA POR ID
DROP PROCEDURE IF EXISTS list_persona_by_id //
CREATE PROCEDURE list_persona_by_id (
    IN p_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        p.*,
        s.nombre AS sede_nombre,
        CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo,
        CONCAT(p.tipo_documento, ': ', p.numero_documento) AS documento_completo,
        (SELECT COUNT(*) FROM tickets t WHERE t.persona_id = p.id) AS total_tickets,
        (SELECT COUNT(DISTINCT t.rifa_id) FROM tickets t WHERE t.persona_id = p.id) AS total_rifas_participadas
    FROM personas p
    INNER JOIN sedes s ON p.sede_id = s.id
    WHERE p.id = p_id
      AND p.sede_id = p_sede_id;
END //

-- 3. PROCEDURE PARA REGISTRAR PERSONA
DROP PROCEDURE IF EXISTS register_persona //
CREATE PROCEDURE register_persona (
    IN p_sede_id INT,
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_tipo_documento VARCHAR(20),
    IN p_numero_documento VARCHAR(20),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_direccion VARCHAR(500),
    IN p_ciudad VARCHAR(100),
    IN p_pais VARCHAR(100),
    IN p_creado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al registrar la persona';
    END;

    START TRANSACTION;

    IF NOT EXISTS (SELECT 1 FROM sedes WHERE id = p_sede_id) THEN
        SET p_mensaje = 'La sede no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Validar que el documento no exista en esta sede
    IF EXISTS (
        SELECT 1
        FROM personas
        WHERE sede_id = p_sede_id
          AND tipo_documento = p_tipo_documento
          AND numero_documento = p_numero_documento
    ) THEN
        SET p_mensaje = 'Ya existe una persona con este documento en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    INSERT INTO personas (
        sede_id,
        nombres,
        apellidos,
        tipo_documento,
        numero_documento,
        email,
        telefono,
        direccion,
        ciudad,
        pais,
        creado_por,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        p_nombres,
        p_apellidos,
        p_tipo_documento,
        p_numero_documento,
        p_email,
        p_telefono,
        p_direccion,
        p_ciudad,
        p_pais,
        p_creado_por,
        NOW(),
        NOW()
    );

    COMMIT;
    SET p_mensaje = 'Persona registrada correctamente';
END //

-- 4. PROCEDURE PARA ACTUALIZAR PERSONA
DROP PROCEDURE IF EXISTS update_persona //
CREATE PROCEDURE update_persona (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_nombres VARCHAR(100),
    IN p_apellidos VARCHAR(100),
    IN p_tipo_documento VARCHAR(20),
    IN p_numero_documento VARCHAR(20),
    IN p_email VARCHAR(100),
    IN p_telefono VARCHAR(15),
    IN p_direccion VARCHAR(500),
    IN p_ciudad VARCHAR(100),
    IN p_pais VARCHAR(100),
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar la persona';
    END;

    START TRANSACTION;

    IF NOT EXISTS (
        SELECT 1 FROM personas
        WHERE id = p_id AND sede_id = p_sede_id
    ) THEN
        SET p_mensaje = 'La persona no existe en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Validar que el documento no esté en uso por otra persona
    IF EXISTS (
        SELECT 1
        FROM personas
        WHERE sede_id = p_sede_id
          AND tipo_documento = p_tipo_documento
          AND numero_documento = p_numero_documento
          AND id <> p_id
    ) THEN
        SET p_mensaje = 'Ya existe otra persona con este documento en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE personas
    SET
        nombres = p_nombres,
        apellidos = p_apellidos,
        tipo_documento = p_tipo_documento,
        numero_documento = p_numero_documento,
        email = p_email,
        telefono = p_telefono,
        direccion = p_direccion,
        ciudad = p_ciudad,
        pais = p_pais,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    COMMIT;
    SET p_mensaje = 'Persona actualizada correctamente';
END //

-- 5. PROCEDURE PARA ELIMINAR PERSONA (VERIFICAR QUE NO TENGA TICKETS)
DROP PROCEDURE IF EXISTS delete_persona //
CREATE PROCEDURE delete_persona (
    IN p_id INT,
    IN p_sede_id INT,
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_tickets_count INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al eliminar la persona';
    END;

    START TRANSACTION;

    IF NOT EXISTS (
        SELECT 1 FROM personas
        WHERE id = p_id AND sede_id = p_sede_id
    ) THEN
        SET p_mensaje = 'La persona no existe en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Verificar si tiene tickets asociados
    SELECT COUNT(*) INTO v_tickets_count
    FROM tickets
    WHERE persona_id = p_id;

    IF v_tickets_count > 0 THEN
        SET p_mensaje = CONCAT('No se puede eliminar la persona porque tiene ', v_tickets_count, ' ticket(s) asociado(s)');
        ROLLBACK;
        LEAVE proc;
    END IF;

    DELETE FROM personas
    WHERE id = p_id
      AND sede_id = p_sede_id;

    COMMIT;
    SET p_mensaje = 'Persona eliminada correctamente';
END //

DELIMITER ;
