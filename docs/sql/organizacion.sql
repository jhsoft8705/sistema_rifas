-- =============================================
-- STORED PROCEDURES PARA MANTENIMIENTO DE ORGANIZACIÓN (SEDE)
-- La organización es la sede principal; solo listado y actualización
-- =============================================

DELIMITER //

-- 1. OBTENER SEDE (ORGANIZACIÓN) POR ID
DROP PROCEDURE IF EXISTS get_sede_by_id //
CREATE PROCEDURE get_sede_by_id (
    IN p_id INT
)
BEGIN
    SELECT
        id,
        codigo,
        nombre,
        pais,
        descripcion,
        direccion,
        telefono,
        email,
        es_principal,
        url_logo,
        url_favicon,
        url_landing,
        moneda,
        simbolo_moneda,
        codigo_moneda,
        zona_horaria,
        requiere_aprobacion_manual,
        dias_validez_ticket,
        estado,
        fecha_creacion,
        fecha_modificacion,
        creado_por,
        modificado_por
    FROM sedes
    WHERE id = p_id
    LIMIT 1;
END //

-- 2. LISTAR SEDES (para combos; la organización suele ser una)
DROP PROCEDURE IF EXISTS list_sedes //
CREATE PROCEDURE list_sedes (
    IN p_estado INT
)
BEGIN
    SELECT
        id,
        codigo,
        nombre,
        pais,
        descripcion,
        direccion,
        telefono,
        email,
        es_principal,
        estado,
        fecha_creacion
    FROM sedes
    WHERE (p_estado IS NULL OR estado = p_estado)
    ORDER BY es_principal DESC, nombre ASC;
END //

-- 3. ACTUALIZAR SEDE (ORGANIZACIÓN)
DROP PROCEDURE IF EXISTS update_sede //
CREATE PROCEDURE update_sede (
    IN p_id INT,
    IN p_codigo VARCHAR(20),
    IN p_nombre VARCHAR(200),
    IN p_pais VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_direccion VARCHAR(500),
    IN p_telefono VARCHAR(15),
    IN p_email VARCHAR(100),
    IN p_es_principal TINYINT,
    IN p_url_logo VARCHAR(255),
    IN p_url_favicon VARCHAR(255),
    IN p_url_landing VARCHAR(255),
    IN p_moneda VARCHAR(50),
    IN p_simbolo_moneda VARCHAR(10),
    IN p_codigo_moneda VARCHAR(3),
    IN p_zona_horaria VARCHAR(50),
    IN p_requiere_aprobacion_manual TINYINT,
    IN p_dias_validez_ticket INT,
    IN p_estado INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar la organización';
    END;

    IF NOT EXISTS (SELECT 1 FROM sedes WHERE id = p_id) THEN
        SET p_mensaje = 'La organización no existe';
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1 FROM sedes
        WHERE codigo = p_codigo AND id <> p_id
    ) THEN
        SET p_mensaje = 'El código ya está en uso por otra sede';
        LEAVE proc;
    END IF;

    UPDATE sedes
    SET
        codigo = p_codigo,
        nombre = p_nombre,
        pais = p_pais,
        descripcion = p_descripcion,
        direccion = p_direccion,
        telefono = p_telefono,
        email = p_email,
        es_principal = IFNULL(p_es_principal, es_principal),
        url_logo = p_url_logo,
        url_favicon = p_url_favicon,
        url_landing = p_url_landing,
        moneda = IFNULL(p_moneda, moneda),
        simbolo_moneda = IFNULL(p_simbolo_moneda, simbolo_moneda),
        codigo_moneda = IFNULL(p_codigo_moneda, codigo_moneda),
        zona_horaria = IFNULL(p_zona_horaria, zona_horaria),
        requiere_aprobacion_manual = IFNULL(p_requiere_aprobacion_manual, requiere_aprobacion_manual),
        dias_validez_ticket = IFNULL(p_dias_validez_ticket, dias_validez_ticket),
        estado = IFNULL(p_estado, estado),
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id;

    SET p_mensaje = 'Organización actualizada correctamente';
END //

DELIMITER ;
