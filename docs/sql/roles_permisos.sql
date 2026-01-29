-- =============================================
-- STORED PROCEDURES PARA GESTIÓN DE ROLES Y PERMISOS
-- Sistema de Gestión de Rifas
-- =============================================

DELIMITER //

-- ==========================================================
-- 1. LISTAR ROLES CON PERMISOS (Para gestión)
-- ==========================================================
DROP PROCEDURE IF EXISTS list_roles_completo //
CREATE PROCEDURE list_roles_completo (
    IN p_sede_id INT
)
BEGIN
    SELECT DISTINCT
        r.id,
        r.sede_id,
        r.nombre,
        r.descripcion,
        r.nivel_acceso,
        r.estado,
        r.fecha_creacion,
        r.fecha_modificacion,
        COALESCE((SELECT COUNT(DISTINCT ur.usuario_id) FROM usuario_roles ur WHERE ur.rol_id = r.id AND ur.sede_id = r.sede_id AND ur.estado = 1), 0) AS usuarios_asignados,
        COALESCE((SELECT COUNT(DISTINCT rp.permiso_id) FROM rol_permisos rp WHERE rp.rol_id = r.id AND rp.sede_id = r.sede_id AND rp.estado = 1), 0) AS permisos_asignados
    FROM roles r
    WHERE r.sede_id = p_sede_id
    ORDER BY r.nivel_acceso DESC, r.nombre ASC;
END //

-- ==========================================================
-- 2. OBTENER ROL POR ID CON PERMISOS
-- ==========================================================
DROP PROCEDURE IF EXISTS get_rol_by_id //
CREATE PROCEDURE get_rol_by_id (
    IN p_rol_id INT,
    IN p_sede_id INT
)
BEGIN
    -- Datos del rol
    SELECT
        r.id,
        r.sede_id,
        r.nombre,
        r.descripcion,
        r.nivel_acceso,
        r.estado,
        r.fecha_creacion,
        r.fecha_modificacion
    FROM roles r
    WHERE r.id = p_rol_id
      AND r.sede_id = p_sede_id
    LIMIT 1;
END //

-- ==========================================================
-- 3. REGISTRAR NUEVO ROL
-- ==========================================================
DROP PROCEDURE IF EXISTS register_rol //
CREATE PROCEDURE register_rol (
    IN p_sede_id INT,
    IN p_nombre VARCHAR(50),
    IN p_descripcion VARCHAR(255),
    IN p_nivel_acceso INT,
    IN p_creado_por VARCHAR(50),
    OUT p_rol_id INT,
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_rol_id = NULL;
        SET p_mensaje = 'Error al registrar el rol';
    END;

    START TRANSACTION;

    -- Verificar que el nombre no exista en la sede
    IF EXISTS (SELECT 1 FROM roles WHERE sede_id = p_sede_id AND nombre = p_nombre) THEN
        SET p_mensaje = 'El nombre del rol ya existe en esta sede';
        SET p_rol_id = NULL;
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Insertar rol
    INSERT INTO roles (
        sede_id,
        nombre,
        descripcion,
        nivel_acceso,
        estado,
        creado_por,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        TRIM(p_nombre),
        NULLIF(TRIM(p_descripcion), ''),
        p_nivel_acceso,
        1,
        p_creado_por,
        NOW(),
        NOW()
    );

    SET p_rol_id = LAST_INSERT_ID();
    SET p_mensaje = 'Rol registrado correctamente';
    COMMIT;
END //

-- ==========================================================
-- 4. ACTUALIZAR ROL
-- ==========================================================
DROP PROCEDURE IF EXISTS update_rol //
CREATE PROCEDURE update_rol (
    IN p_rol_id INT,
    IN p_sede_id INT,
    IN p_nombre VARCHAR(50),
    IN p_descripcion VARCHAR(255),
    IN p_nivel_acceso INT,
    IN p_estado INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar el rol';
    END;

    START TRANSACTION;

    -- Verificar que el rol existe
    IF NOT EXISTS (SELECT 1 FROM roles WHERE id = p_rol_id AND sede_id = p_sede_id) THEN
        SET p_mensaje = 'Rol no encontrado';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Verificar que el nombre no esté en uso por otro rol
    IF EXISTS (SELECT 1 FROM roles WHERE sede_id = p_sede_id AND nombre = p_nombre AND id != p_rol_id) THEN
        SET p_mensaje = 'El nombre del rol ya está en uso por otro rol';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Actualizar rol
    UPDATE roles
    SET nombre = TRIM(p_nombre),
        descripcion = NULLIF(TRIM(p_descripcion), ''),
        nivel_acceso = p_nivel_acceso,
        estado = p_estado,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_rol_id
      AND sede_id = p_sede_id;

    SET p_mensaje = 'Rol actualizado correctamente';
    COMMIT;
END //

-- ==========================================================
-- 5. LISTAR PERMISOS POR SEDE
-- ==========================================================
DROP PROCEDURE IF EXISTS list_permisos //
CREATE PROCEDURE list_permisos (
    IN p_sede_id INT
)
BEGIN
    SELECT
        p.id,
        p.sede_id,
        p.nombre,
        p.descripcion,
        p.modulo,
        p.accion,
        p.estado,
        p.fecha_creacion,
        p.fecha_modificacion
    FROM permisos p
    WHERE p.sede_id = p_sede_id
    ORDER BY p.modulo ASC, p.accion ASC, p.nombre ASC;
END //

-- ==========================================================
-- 6. OBTENER PERMISOS DE UN ROL
-- ==========================================================
DROP PROCEDURE IF EXISTS get_permisos_rol //
CREATE PROCEDURE get_permisos_rol (
    IN p_rol_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        p.id,
        p.nombre,
        p.descripcion,
        p.modulo,
        p.accion,
        rp.estado AS asignado,
        rp.fecha_asignacion
    FROM permisos p
    LEFT JOIN rol_permisos rp ON p.id = rp.permiso_id 
        AND rp.rol_id = p_rol_id 
        AND rp.sede_id = p_sede_id
    WHERE p.sede_id = p_sede_id
      AND p.estado = 1
    ORDER BY p.modulo ASC, p.accion ASC;
END //

-- ==========================================================
-- 7. ASIGNAR PERMISOS A UN ROL
-- ==========================================================
DROP PROCEDURE IF EXISTS asignar_permisos_rol //
CREATE PROCEDURE asignar_permisos_rol (
    IN p_rol_id INT,
    IN p_sede_id INT,
    IN p_permisos_ids TEXT,
    IN p_asignado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_permiso_id INT;
    DECLARE v_done INT DEFAULT 0;
    DECLARE v_pos INT DEFAULT 1;
    DECLARE v_permiso_str VARCHAR(20);
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al asignar permisos al rol';
    END;

    START TRANSACTION;

    -- Verificar que el rol existe
    IF NOT EXISTS (SELECT 1 FROM roles WHERE id = p_rol_id AND sede_id = p_sede_id) THEN
        SET p_mensaje = 'Rol no encontrado';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Desactivar todos los permisos actuales del rol
    UPDATE rol_permisos
    SET estado = 0,
        fecha_modificacion = NOW()
    WHERE rol_id = p_rol_id
      AND sede_id = p_sede_id;

    -- Si se proporcionaron permisos, asignarlos
    IF p_permisos_ids IS NOT NULL AND p_permisos_ids != '' THEN
        -- Procesar lista de IDs separados por comas
        WHILE v_pos <= LENGTH(p_permisos_ids) DO
            SET v_permiso_str = SUBSTRING_INDEX(SUBSTRING_INDEX(p_permisos_ids, ',', v_pos), ',', -1);
            SET v_permiso_id = CAST(TRIM(v_permiso_str) AS UNSIGNED);
            
            IF v_permiso_id > 0 THEN
                -- Verificar que el permiso existe y pertenece a la sede
                IF EXISTS (SELECT 1 FROM permisos WHERE id = v_permiso_id AND sede_id = p_sede_id) THEN
                    -- Insertar o actualizar asignación
                    INSERT INTO rol_permisos (
                        sede_id,
                        rol_id,
                        permiso_id,
                        estado,
                        asignado_por,
                        fecha_asignacion,
                        fecha_creacion,
                        fecha_modificacion
                    ) VALUES (
                        p_sede_id,
                        p_rol_id,
                        v_permiso_id,
                        1,
                        p_asignado_por,
                        NOW(),
                        NOW(),
                        NOW()
                    )
                    ON DUPLICATE KEY UPDATE
                        estado = 1,
                        asignado_por = p_asignado_por,
                        fecha_asignacion = NOW(),
                        fecha_modificacion = NOW();
                END IF;
            END IF;
            
            SET v_pos = v_pos + 1;
        END WHILE;
    END IF;

    SET p_mensaje = 'Permisos asignados correctamente al rol';
    COMMIT;
END //

-- ==========================================================
-- 8. OBTENER PERMISOS DEL USUARIO (Mejorado)
-- ==========================================================
DROP PROCEDURE IF EXISTS get_permisos_usuario //
CREATE PROCEDURE get_permisos_usuario (
    IN p_usuario_id INT,
    IN p_sede_id INT
)
BEGIN
    -- Permisos directos del usuario
    SELECT DISTINCT
        p.id,
        p.nombre,
        p.descripcion,
        p.modulo,
        p.accion,
        'DIRECTO' AS tipo_permiso
    FROM permisos p
    INNER JOIN usuario_permisos up ON p.id = up.permiso_id
    WHERE up.usuario_id = p_usuario_id
      AND up.sede_id = p_sede_id
      AND up.estado = 1
      AND (up.fecha_vencimiento IS NULL OR up.fecha_vencimiento > NOW())
      AND p.estado = 1

    UNION

    -- Permisos del rol del usuario
    SELECT DISTINCT
        p.id,
        p.nombre,
        p.descripcion,
        p.modulo,
        p.accion,
        'ROL' AS tipo_permiso
    FROM permisos p
    INNER JOIN rol_permisos rp ON p.id = rp.permiso_id
    INNER JOIN roles r ON rp.rol_id = r.id
    INNER JOIN usuario_roles ur ON r.id = ur.rol_id
    WHERE ur.usuario_id = p_usuario_id
      AND ur.sede_id = p_sede_id
      AND ur.estado = 1
      AND (ur.fecha_vencimiento IS NULL OR ur.fecha_vencimiento > NOW())
      AND rp.estado = 1
      AND r.estado = 1
      AND p.estado = 1

    ORDER BY modulo, accion;
END //

-- ==========================================================
-- 9. VERIFICAR SI USUARIO TIENE PERMISO
-- ==========================================================
DROP PROCEDURE IF EXISTS verificar_permiso_usuario //
CREATE PROCEDURE verificar_permiso_usuario (
    IN p_usuario_id INT,
    IN p_sede_id INT,
    IN p_permiso_nombre VARCHAR(100),
    OUT p_tiene_permiso TINYINT
)
BEGIN
    SET p_tiene_permiso = 0;

    -- Verificar permiso directo
    IF EXISTS (
        SELECT 1
        FROM permisos p
        INNER JOIN usuario_permisos up ON p.id = up.permiso_id
        WHERE up.usuario_id = p_usuario_id
          AND up.sede_id = p_sede_id
          AND p.nombre = p_permiso_nombre
          AND up.estado = 1
          AND (up.fecha_vencimiento IS NULL OR up.fecha_vencimiento > NOW())
          AND p.estado = 1
    ) THEN
        SET p_tiene_permiso = 1;
    -- Verificar permiso por rol
    ELSEIF EXISTS (
        SELECT 1
        FROM permisos p
        INNER JOIN rol_permisos rp ON p.id = rp.permiso_id
        INNER JOIN roles r ON rp.rol_id = r.id
        INNER JOIN usuario_roles ur ON r.id = ur.rol_id
        WHERE ur.usuario_id = p_usuario_id
          AND ur.sede_id = p_sede_id
          AND p.nombre = p_permiso_nombre
          AND ur.estado = 1
          AND (ur.fecha_vencimiento IS NULL OR ur.fecha_vencimiento > NOW())
          AND rp.estado = 1
          AND r.estado = 1
          AND p.estado = 1
    ) THEN
        SET p_tiene_permiso = 1;
    END IF;
END //

-- ==========================================================
-- 10. REGISTRAR NUEVO PERMISO
-- ==========================================================
DROP PROCEDURE IF EXISTS register_permiso //
CREATE PROCEDURE register_permiso (
    IN p_sede_id INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_modulo VARCHAR(50),
    IN p_accion VARCHAR(50),
    IN p_creado_por VARCHAR(50),
    OUT p_permiso_id INT,
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_permiso_id = NULL;
        SET p_mensaje = 'Error al registrar el permiso';
    END;

    START TRANSACTION;

    -- Verificar que el nombre no exista en la sede
    IF EXISTS (SELECT 1 FROM permisos WHERE sede_id = p_sede_id AND nombre = p_nombre) THEN
        SET p_mensaje = 'El nombre del permiso ya existe en esta sede';
        SET p_permiso_id = NULL;
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Insertar permiso
    INSERT INTO permisos (
        sede_id,
        nombre,
        descripcion,
        modulo,
        accion,
        estado,
        creado_por,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        TRIM(p_nombre),
        NULLIF(TRIM(p_descripcion), ''),
        TRIM(p_modulo),
        TRIM(p_accion),
        1,
        p_creado_por,
        NOW(),
        NOW()
    );

    SET p_permiso_id = LAST_INSERT_ID();
    SET p_mensaje = 'Permiso registrado correctamente';
    COMMIT;
END //

-- ==========================================================
-- 11. ACTUALIZAR PERMISO
-- ==========================================================
DROP PROCEDURE IF EXISTS update_permiso //
CREATE PROCEDURE update_permiso (
    IN p_permiso_id INT,
    IN p_sede_id INT,
    IN p_nombre VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_modulo VARCHAR(50),
    IN p_accion VARCHAR(50),
    IN p_estado INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar el permiso';
    END;

    START TRANSACTION;

    -- Verificar que el permiso existe
    IF NOT EXISTS (SELECT 1 FROM permisos WHERE id = p_permiso_id AND sede_id = p_sede_id) THEN
        SET p_mensaje = 'Permiso no encontrado';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Verificar que el nombre no esté en uso por otro permiso
    IF EXISTS (SELECT 1 FROM permisos WHERE sede_id = p_sede_id AND nombre = p_nombre AND id != p_permiso_id) THEN
        SET p_mensaje = 'El nombre del permiso ya está en uso por otro permiso';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Actualizar permiso
    UPDATE permisos
    SET nombre = TRIM(p_nombre),
        descripcion = NULLIF(TRIM(p_descripcion), ''),
        modulo = TRIM(p_modulo),
        accion = TRIM(p_accion),
        estado = p_estado,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_permiso_id
      AND sede_id = p_sede_id;

    SET p_mensaje = 'Permiso actualizado correctamente';
    COMMIT;
END //

DELIMITER ;
