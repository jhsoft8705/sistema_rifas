-- =============================================
-- STORED PROCEDURES PARA MANTENIMIENTO DE CATEGORÍAS DE PREMIOS
-- Basado en la estructura definida en docs/sql/bd_rifas_mysql.sql
-- =============================================

DELIMITER //

-- 1. LISTAR CATEGORÍAS POR SEDE (OPCIONALMENTE POR ESTADO)
DROP PROCEDURE IF EXISTS list_categorias_premios //
CREATE PROCEDURE list_categorias_premios (
    IN p_sede_id INT,
    IN p_estado INT
)
BEGIN
    SELECT
        cp.id,
        cp.sede_id,
        s.nombre AS sede_nombre,
        cp.nombre,
        cp.descripcion,
        cp.icono,
        cp.color_hex,
        cp.orden,
        cp.estado,
        cp.fecha_creacion,
        cp.fecha_modificacion,
        cp.creado_por,
        cp.modificado_por
    FROM categorias_premios cp
    INNER JOIN sedes s ON cp.sede_id = s.id
    WHERE cp.sede_id = p_sede_id
      AND (p_estado IS NULL OR cp.estado = p_estado)
    ORDER BY cp.orden ASC, cp.nombre ASC;
END //

-- 2. OBTENER CATEGORÍA POR ID
DROP PROCEDURE IF EXISTS list_categoria_premio_by_id //
CREATE PROCEDURE list_categoria_premio_by_id (
    IN p_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        cp.*,
        s.nombre AS sede_nombre
    FROM categorias_premios cp
    INNER JOIN sedes s ON cp.sede_id = s.id
    WHERE cp.id = p_id
      AND cp.sede_id = p_sede_id;
END //

-- 3. REGISTRAR NUEVA CATEGORÍA
DROP PROCEDURE IF EXISTS register_categoria_premio //
CREATE PROCEDURE register_categoria_premio (
    IN p_sede_id INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_icono VARCHAR(100),
    IN p_color_hex VARCHAR(7),
    IN p_orden INT,
    IN p_creado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al registrar la categoría';
    END;

    START TRANSACTION;

    IF NOT EXISTS (SELECT 1 FROM sedes WHERE id = p_sede_id) THEN
        SET p_mensaje = 'La sede no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM categorias_premios
        WHERE sede_id = p_sede_id
          AND nombre = p_nombre
    ) THEN
        SET p_mensaje = 'El nombre de la categoría ya existe en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    INSERT INTO categorias_premios (
        sede_id,
        nombre,
        descripcion,
        icono,
        color_hex,
        orden,
        estado,
        creado_por,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        p_nombre,
        p_descripcion,
        p_icono,
        p_color_hex,
        IFNULL(p_orden, 0),
        1,
        p_creado_por,
        NOW(),
        NOW()
    );

    COMMIT;
    SET p_mensaje = 'Categoría registrada correctamente';
END //

-- 4. ACTUALIZAR CATEGORÍA
DROP PROCEDURE IF EXISTS update_categoria_premio //
CREATE PROCEDURE update_categoria_premio (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_icono VARCHAR(100),
    IN p_color_hex VARCHAR(7),
    IN p_orden INT,
    IN p_estado INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar la categoría';
    END;

    START TRANSACTION;

    IF NOT EXISTS (
        SELECT 1 FROM categorias_premios
        WHERE id = p_id AND sede_id = p_sede_id
    ) THEN
        SET p_mensaje = 'La categoría no existe en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM categorias_premios
        WHERE sede_id = p_sede_id
          AND nombre = p_nombre
          AND id <> p_id
    ) THEN
        SET p_mensaje = 'El nombre de la categoría ya está en uso en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE categorias_premios
    SET
        nombre = p_nombre,
        descripcion = p_descripcion,
        icono = p_icono,
        color_hex = p_color_hex,
        orden = IFNULL(p_orden, orden),
        estado = IFNULL(p_estado, estado),
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    COMMIT;
    SET p_mensaje = 'Categoría actualizada correctamente';
END //

-- 5. ELIMINAR (DESACTIVAR) CATEGORÍA
DROP PROCEDURE IF EXISTS delete_categoria_premio //
CREATE PROCEDURE delete_categoria_premio (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_premios_activos INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al eliminar la categoría';
    END;

    START TRANSACTION;

    IF NOT EXISTS (
        SELECT 1
        FROM categorias_premios
        WHERE id = p_id
          AND sede_id = p_sede_id
          AND estado = 1
    ) THEN
        SET p_mensaje = 'La categoría no existe en esta sede o ya está inactiva.';
        ROLLBACK;
        LEAVE proc;
    END IF;

    SELECT COUNT(*) INTO v_premios_activos
    FROM premios
    WHERE categoria_id = p_id
      AND sede_id = p_sede_id
      AND estado <> 0;

    IF v_premios_activos > 0 THEN
        SET p_mensaje = 'No se puede eliminar la categoría porque existe al menos un premio asociado.';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE categorias_premios
    SET
        estado = 0,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    COMMIT;
    SET p_mensaje = 'Categoría eliminada correctamente.';
END //

DELIMITER ;


