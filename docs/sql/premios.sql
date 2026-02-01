-- =============================================
-- STORED PROCEDURES PARA MANTENIMIENTO DE PREMIOS (MySQL)
-- Basado en la estructura definida en docs/sql/bd_rifas_mysql.sql
-- =============================================

DELIMITER //

-- 1. PROCEDURE PARA LISTAR PREMIOS POR SEDE (OPCIONALMENTE POR ESTADO)
DROP PROCEDURE IF EXISTS list_premios //
CREATE PROCEDURE list_premios (
    IN p_sede_id INT,
    IN p_estado INT
)
BEGIN
    SELECT
        p.id,
        p.sede_id,
        s.nombre AS sede_nombre,
        p.categoria_id,
        cp.nombre AS categoria_nombre,
        p.codigo,
        p.nombre,
        p.descripcion,
        p.valor_estimado,
        p.imagen_principal,
        p.imagen_secundaria,
        p.galeria_imagenes,
        p.video_url,
        p.marca,
        p.modelo,
        p.color,
        p.especificaciones,
        p.terminos_condiciones,
        p.restricciones,
        p.es_destacado,
        p.orden_visualizacion,
        p.estado,
        p.creado_por,
        p.fecha_creacion,
        p.modificado_por,
        p.fecha_modificacion
    FROM premios p
    INNER JOIN sedes s ON p.sede_id = s.id
    LEFT JOIN categorias_premios cp ON p.categoria_id = cp.id
    WHERE p.sede_id = p_sede_id
      AND (p_estado IS NULL OR p.estado = p_estado)
    ORDER BY p.es_destacado DESC, p.orden_visualizacion ASC, p.fecha_creacion DESC;
END //

-- 2. PROCEDURE PARA OBTENER PREMIO POR ID
DROP PROCEDURE IF EXISTS list_premio_by_id //
CREATE PROCEDURE list_premio_by_id (
    IN p_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        p.*,
        s.nombre AS sede_nombre,
        cp.nombre AS categoria_nombre
    FROM premios p
    INNER JOIN sedes s ON p.sede_id = s.id
    LEFT JOIN categorias_premios cp ON p.categoria_id = cp.id
    WHERE p.id = p_id
      AND p.sede_id = p_sede_id;
END //

-- 3. PROCEDURE PARA REGISTRAR PREMIO
DROP PROCEDURE IF EXISTS register_premio //
CREATE PROCEDURE register_premio (
    IN p_sede_id INT,
    IN p_categoria_id INT,
    IN p_codigo VARCHAR(50),
    IN p_nombre VARCHAR(200),
    IN p_descripcion TEXT,
    IN p_valor_estimado DECIMAL(12,2),
    IN p_imagen_principal VARCHAR(255),
    IN p_imagen_secundaria VARCHAR(255),
    IN p_galeria_imagenes TEXT,
    IN p_video_url VARCHAR(255),
    IN p_marca VARCHAR(100),
    IN p_modelo VARCHAR(100),
    IN p_color VARCHAR(50),
    IN p_especificaciones TEXT,
    IN p_terminos_condiciones TEXT,
    IN p_restricciones TEXT,
    IN p_es_destacado TINYINT,
    IN p_orden_visualizacion INT,
    IN p_creado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_codigo_generado VARCHAR(50);
    DECLARE v_ultimo_numero INT DEFAULT 0;
    DECLARE v_nuevo_numero INT DEFAULT 1;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al registrar el premio';
    END;

    START TRANSACTION;

    IF NOT EXISTS (SELECT 1 FROM sedes WHERE id = p_sede_id) THEN
        SET p_mensaje = 'La sede no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Generar código automáticamente si no se proporciona o está vacío
    IF p_codigo IS NULL OR (p_codigo IS NOT NULL AND TRIM(p_codigo) = '') THEN
        -- Obtener el último número de código para esta sede
        SELECT COALESCE(MAX(CAST(SUBSTRING(codigo, 5) AS UNSIGNED)), 0) INTO v_ultimo_numero
        FROM premios
        WHERE sede_id = p_sede_id
          AND codigo LIKE 'PRM-%'
          AND SUBSTRING(codigo, 5) REGEXP '^[0-9]+$';
        
        SET v_nuevo_numero = v_ultimo_numero + 1;
        SET v_codigo_generado = CONCAT('PRM-', LPAD(v_nuevo_numero, 6, '0'));
        
        -- Verificar que el código generado no exista (por si acaso)
        WHILE EXISTS (
            SELECT 1 FROM premios
            WHERE sede_id = p_sede_id AND codigo = v_codigo_generado
        ) DO
            SET v_nuevo_numero = v_nuevo_numero + 1;
            SET v_codigo_generado = CONCAT('PRM-', LPAD(v_nuevo_numero, 6, '0'));
        END WHILE;
        
        SET p_codigo = v_codigo_generado;
    ELSE
        -- Validar que el código proporcionado no exista
        IF EXISTS (
            SELECT 1
            FROM premios
            WHERE sede_id = p_sede_id
              AND codigo = p_codigo
        ) THEN
            SET p_mensaje = 'El código del premio ya existe en esta sede';
            ROLLBACK;
            LEAVE proc;
        END IF;
    END IF;

    INSERT INTO premios (
        sede_id,
        categoria_id,
        codigo,
        nombre,
        descripcion,
        valor_estimado,
        imagen_principal,
        imagen_secundaria,
        galeria_imagenes,
        video_url,
        marca,
        modelo,
        color,
        especificaciones,
        terminos_condiciones,
        restricciones,
        es_destacado,
        orden_visualizacion,
        estado,
        creado_por,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        p_categoria_id,
        p_codigo,
        p_nombre,
        p_descripcion,
        p_valor_estimado,
        p_imagen_principal,
        p_imagen_secundaria,
        p_galeria_imagenes,
        p_video_url,
        p_marca,
        p_modelo,
        p_color,
        p_especificaciones,
        p_terminos_condiciones,
        p_restricciones,
        IFNULL(p_es_destacado, 0),
        IFNULL(p_orden_visualizacion, 0),
        1,
        p_creado_por,
        NOW(),
        NOW()
    );

    COMMIT;
    SET p_mensaje = 'Premio registrado correctamente';
END //

-- 4. PROCEDURE PARA ACTUALIZAR PREMIO
DROP PROCEDURE IF EXISTS update_premio //
CREATE PROCEDURE update_premio (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_categoria_id INT,
    IN p_codigo VARCHAR(50),
    IN p_nombre VARCHAR(200),
    IN p_descripcion TEXT,
    IN p_valor_estimado DECIMAL(12,2),
    IN p_imagen_principal VARCHAR(255),
    IN p_imagen_secundaria VARCHAR(255),
    IN p_galeria_imagenes TEXT,
    IN p_video_url VARCHAR(255),
    IN p_marca VARCHAR(100),
    IN p_modelo VARCHAR(100),
    IN p_color VARCHAR(50),
    IN p_especificaciones TEXT,
    IN p_terminos_condiciones TEXT,
    IN p_restricciones TEXT,
    IN p_es_destacado TINYINT,
    IN p_orden_visualizacion INT,
    IN p_estado INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar el premio';
    END;

    START TRANSACTION;

    IF NOT EXISTS (
        SELECT 1 FROM premios
        WHERE id = p_id AND sede_id = p_sede_id
    ) THEN
        SET p_mensaje = 'El premio no existe en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM premios
        WHERE sede_id = p_sede_id
          AND codigo = p_codigo
          AND id <> p_id
    ) THEN
        SET p_mensaje = 'El código del premio ya está en uso en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE premios
    SET
        categoria_id = p_categoria_id,
        codigo = p_codigo,
        nombre = p_nombre,
        descripcion = p_descripcion,
        valor_estimado = p_valor_estimado,
        imagen_principal = p_imagen_principal,
        imagen_secundaria = p_imagen_secundaria,
        galeria_imagenes = p_galeria_imagenes,
        video_url = p_video_url,
        marca = p_marca,
        modelo = p_modelo,
        color = p_color,
        especificaciones = p_especificaciones,
        terminos_condiciones = p_terminos_condiciones,
        restricciones = p_restricciones,
        es_destacado = IFNULL(p_es_destacado, 0),
        orden_visualizacion = IFNULL(p_orden_visualizacion, 0),
        estado = IFNULL(p_estado, estado),
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    COMMIT;
    SET p_mensaje = 'Premio actualizado correctamente';
END //

-- 5. PROCEDURE PARA DESACTIVAR/ELIMINAR LÓGICAMENTE UN PREMIO
DROP PROCEDURE IF EXISTS delete_premio //
CREATE PROCEDURE delete_premio (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_rifas_activas INT DEFAULT 0;
    DECLARE v_rifas_relacionadas INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al eliminar el premio';
    END;

    START TRANSACTION;

    IF NOT EXISTS (
        SELECT 1
        FROM premios
        WHERE id = p_id
          AND sede_id = p_sede_id
          AND estado = 1
    ) THEN
        SET p_mensaje = 'El premio no existe en esta sede o ya está inactivo.';
        ROLLBACK;
        LEAVE proc;
    END IF;

    SELECT COUNT(*) INTO v_rifas_activas
    FROM rifas
    WHERE premio_id = p_id
      AND sede_id = p_sede_id
      AND estado_activo = 1;

    SELECT COUNT(*) INTO v_rifas_relacionadas
    FROM rifas_premios rp
    INNER JOIN rifas r ON rp.rifa_id = r.id
    WHERE rp.premio_id = p_id
      AND rp.estado = 1
      AND r.sede_id = p_sede_id
      AND r.estado_activo = 1;

    IF v_rifas_activas > 0 OR v_rifas_relacionadas > 0 THEN
        SET p_mensaje = 'No se puede eliminar el premio porque está asociado a rifas activas.';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE premios
    SET
        estado = 0,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    COMMIT;
    SET p_mensaje = 'Premio eliminado correctamente.';
END //

-- 6. PROCEDURE PARA OBTENER PREMIOS DESTACADOS (Para landing page)
DROP PROCEDURE IF EXISTS list_premios_destacados //
CREATE PROCEDURE list_premios_destacados (
    IN p_sede_id INT,
    IN p_limite INT
)
BEGIN
    DECLARE v_limite INT DEFAULT 6;
    
    -- Si p_limite es NULL o menor a 1, usar el valor por defecto
    IF p_limite IS NULL OR p_limite < 1 THEN
        SET v_limite = 6;
    ELSE
        SET v_limite = p_limite;
    END IF;
    
    SELECT
        p.id,
        p.sede_id,
        s.nombre AS sede_nombre,
        p.categoria_id,
        cp.nombre AS categoria_nombre,
        p.codigo,
        p.nombre,
        p.descripcion,
        p.valor_estimado,
        p.imagen_principal,
        p.imagen_secundaria,
        p.galeria_imagenes,
        p.video_url,
        p.marca,
        p.modelo,
        p.color,
        p.especificaciones,
        p.terminos_condiciones,
        p.restricciones,
        p.es_destacado,
        p.orden_visualizacion,
        p.estado,
        -- Verificar si tiene rifas activas asociadas
        CASE 
            WHEN EXISTS (
                SELECT 1 FROM rifas_premios rp
                INNER JOIN rifas r ON rp.rifa_id = r.id
                WHERE rp.premio_id = p.id
                  AND r.sede_id = p.sede_id
                  AND r.estado IN ('PUBLICADA', 'EN_VENTA')
                  AND r.estado_activo = 1
                  AND rp.estado = 1
            ) THEN 1
            ELSE 0
        END AS tiene_rifas_activas
    FROM premios p
    INNER JOIN sedes s ON p.sede_id = s.id
    LEFT JOIN categorias_premios cp ON p.categoria_id = cp.id
    WHERE p.sede_id = p_sede_id
      AND p.es_destacado = 1
      AND p.estado = 1
    ORDER BY p.orden_visualizacion ASC, p.fecha_creacion DESC
    LIMIT v_limite;
END //

DELIMITER ;


