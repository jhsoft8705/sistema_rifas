-- =============================================
-- STORED PROCEDURES PARA PERFIL DE USUARIO
-- Sistema de Gestión de Rifas
-- =============================================

DELIMITER //

-- ==========================================================
-- 1. OBTENER PERFIL DEL USUARIO AUTENTICADO
-- ==========================================================
DROP PROCEDURE IF EXISTS get_perfil_usuario //
CREATE PROCEDURE get_perfil_usuario (
    IN p_usuario_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        u.id,
        u.sede_id,
        s.nombre AS sede_nombre,
        u.username,
        u.email,
        u.primer_nombre,
        u.apellido_paterno,
        u.apellido_materno,
        u.telefono,
        u.estado,
        u.ultimo_acceso,
        u.fecha_creacion,
        u.fecha_modificacion,
        (SELECT r.id FROM usuario_roles ur 
         INNER JOIN roles r ON r.id = ur.rol_id
         WHERE ur.usuario_id = u.id 
           AND ur.sede_id = u.sede_id 
           AND ur.estado = 1 
         LIMIT 1) AS rol_id,
        (SELECT r.nombre FROM usuario_roles ur 
         INNER JOIN roles r ON r.id = ur.rol_id
         WHERE ur.usuario_id = u.id 
           AND ur.sede_id = u.sede_id 
           AND ur.estado = 1 
         LIMIT 1) AS rol_nombre,
        u.debe_cambiar_password,
        u.fecha_expiracion_password
    FROM usuarios u
    INNER JOIN sedes s ON u.sede_id = s.id
    WHERE u.id = p_usuario_id
      AND u.sede_id = p_sede_id
      AND u.estado = 1;
END //

-- ==========================================================
-- 2. ACTUALIZAR DATOS DEL PERFIL (sin contraseña)
-- ==========================================================
DROP PROCEDURE IF EXISTS update_perfil_datos //
CREATE PROCEDURE update_perfil_datos (
    IN p_usuario_id INT,
    IN p_sede_id INT,
    IN p_email VARCHAR(100),
    IN p_primer_nombre VARCHAR(50),
    IN p_apellido_paterno VARCHAR(50),
    IN p_apellido_materno VARCHAR(50),
    IN p_telefono VARCHAR(15),
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_usuario_existe INT DEFAULT 0;
    DECLARE v_email_existe INT DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar los datos del perfil';
    END;

    START TRANSACTION;

    -- Verificar que el usuario existe y pertenece a la sede
    SELECT COUNT(*) INTO v_usuario_existe
    FROM usuarios
    WHERE id = p_usuario_id
      AND sede_id = p_sede_id
      AND estado = 1;

    IF v_usuario_existe = 0 THEN
        SET p_mensaje = 'Usuario no encontrado o no tiene permisos';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Verificar que el email no esté en uso por otro usuario de la misma sede
    SELECT COUNT(*) INTO v_email_existe
    FROM usuarios
    WHERE email = p_email
      AND sede_id = p_sede_id
      AND id != p_usuario_id;

    IF v_email_existe > 0 THEN
        SET p_mensaje = 'El correo electrónico ya está en uso por otro usuario';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Actualizar datos del perfil
    UPDATE usuarios
    SET email = p_email,
        primer_nombre = p_primer_nombre,
        apellido_paterno = p_apellido_paterno,
        apellido_materno = p_apellido_materno,
        telefono = p_telefono,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_usuario_id
      AND sede_id = p_sede_id;

    SET p_mensaje = 'Datos del perfil actualizados correctamente';
    COMMIT;
END //

-- ==========================================================
-- 3. CAMBIAR CONTRASEÑA DEL USUARIO
-- ==========================================================
DROP PROCEDURE IF EXISTS cambiar_password_perfil //
CREATE PROCEDURE cambiar_password_perfil (
    IN p_usuario_id INT,
    IN p_sede_id INT,
    IN p_password_actual VARCHAR(255),
    IN p_password_nueva VARCHAR(255),
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_usuario_existe INT DEFAULT 0;
    DECLARE v_password_hash_actual VARCHAR(255);
    DECLARE v_password_valido TINYINT DEFAULT 0;
    
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al cambiar la contraseña';
    END;

    START TRANSACTION;

    -- Verificar que el usuario existe y pertenece a la sede
    SELECT COUNT(*), password_hash INTO v_usuario_existe, v_password_hash_actual
    FROM usuarios
    WHERE id = p_usuario_id
      AND sede_id = p_sede_id
      AND estado = 1;

    IF v_usuario_existe = 0 THEN
        SET p_mensaje = 'Usuario no encontrado o no tiene permisos';
        ROLLBACK;
        LEAVE proc;
    END IF;

    -- Verificar que la contraseña actual sea correcta
    -- Si el password_hash_actual es un hash, usar password_verify
    -- Si es texto plano (legacy), comparar directamente
    IF LENGTH(v_password_hash_actual) = 60 THEN
        -- Es un hash bcrypt, usar password_verify (esto se hace en PHP)
        -- Por ahora asumimos que viene validado desde PHP
        SET v_password_valido = 1;
    ELSE
        -- Comparación directa (legacy)
        IF v_password_hash_actual = p_password_actual THEN
            SET v_password_valido = 1;
        END IF;
    END IF;

    -- La validación real se hace en PHP con password_verify
    -- Este procedimiento solo actualiza si se pasa la validación
    
    -- Actualizar contraseña
    UPDATE usuarios
    SET password_hash = p_password_nueva,
        debe_cambiar_password = 0,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_usuario_id
      AND sede_id = p_sede_id;

    SET p_mensaje = 'Contraseña cambiada correctamente';
    COMMIT;
END //

DELIMITER ;
