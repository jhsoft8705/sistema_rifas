-- =============================================
-- STORED PROCEDURES PARA MANTENIMIENTO DE USUARIOS
-- Registrar, actualizar, dar de baja (desactivar)
-- Incluye rol por usuario (usuario_roles)
-- =============================================

DELIMITER //

-- 0. LISTAR ROLES POR SEDE (para combos)
DROP PROCEDURE IF EXISTS list_roles //
CREATE PROCEDURE list_roles (
    IN p_sede_id INT
)
BEGIN
    SELECT id, nombre, descripcion, nivel_acceso, estado
    FROM roles
    WHERE sede_id = p_sede_id
      AND estado = 1
    ORDER BY nivel_acceso DESC, nombre ASC;
END //

-- 1. LISTAR USUARIOS POR SEDE (opcionalmente por estado), con rol
DROP PROCEDURE IF EXISTS list_usuarios //
CREATE PROCEDURE list_usuarios (
    IN p_sede_id INT,
    IN p_estado INT
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
        (SELECT r.id FROM usuario_roles ur INNER JOIN roles r ON r.id = ur.rol_id
         WHERE ur.usuario_id = u.id AND ur.sede_id = u.sede_id AND ur.estado = 1 LIMIT 1) AS rol_id,
        (SELECT r.nombre FROM usuario_roles ur INNER JOIN roles r ON r.id = ur.rol_id
         WHERE ur.usuario_id = u.id AND ur.sede_id = u.sede_id AND ur.estado = 1 LIMIT 1) AS rol_nombre
    FROM usuarios u
    INNER JOIN sedes s ON u.sede_id = s.id
    WHERE u.sede_id = p_sede_id
      AND (p_estado IS NULL OR u.estado = p_estado)
    ORDER BY u.primer_nombre ASC, u.apellido_paterno ASC;
END //

-- 2. OBTENER USUARIO POR ID (incluye rol_id y rol_nombre)
DROP PROCEDURE IF EXISTS get_usuario_by_id //
CREATE PROCEDURE get_usuario_by_id (
    IN p_id INT,
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
        u.debe_cambiar_password,
        u.fecha_creacion,
        u.fecha_modificacion,
        r.id AS rol_id,
        r.nombre AS rol_nombre
    FROM usuarios u
    INNER JOIN sedes s ON u.sede_id = s.id
    LEFT JOIN usuario_roles ur ON ur.usuario_id = u.id AND ur.sede_id = u.sede_id AND ur.estado = 1
    LEFT JOIN roles r ON r.id = ur.rol_id
    WHERE u.id = p_id
      AND u.sede_id = p_sede_id
    LIMIT 1;
END //

-- 3. REGISTRAR USUARIO (password_hash ya viene hasheado desde PHP); asigna rol
DROP PROCEDURE IF EXISTS register_usuario //
CREATE PROCEDURE register_usuario (
    IN p_sede_id INT,
    IN p_username VARCHAR(50),
    IN p_password_hash VARCHAR(255),
    IN p_email VARCHAR(100),
    IN p_primer_nombre VARCHAR(50),
    IN p_apellido_paterno VARCHAR(50),
    IN p_apellido_materno VARCHAR(50),
    IN p_telefono VARCHAR(15),
    IN p_rol_id INT,
    IN p_creado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_usuario_id INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al registrar el usuario';
    END;

    IF NOT EXISTS (SELECT 1 FROM sedes WHERE id = p_sede_id) THEN
        SET p_mensaje = 'La sede no existe';
        LEAVE proc;
    END IF;

    IF p_rol_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM roles WHERE id = p_rol_id AND sede_id = p_sede_id AND estado = 1) THEN
        SET p_mensaje = 'El rol no existe o no pertenece a esta sede';
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1 FROM usuarios
        WHERE sede_id = p_sede_id AND username = p_username
    ) THEN
        SET p_mensaje = 'El nombre de usuario ya existe en esta sede';
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1 FROM usuarios
        WHERE sede_id = p_sede_id AND email = p_email
    ) THEN
        SET p_mensaje = 'El correo ya está registrado en esta sede';
        LEAVE proc;
    END IF;

    INSERT INTO usuarios (
        sede_id,
        username,
        password_hash,
        email,
        primer_nombre,
        apellido_paterno,
        apellido_materno,
        telefono,
        estado,
        debe_cambiar_password,
        fecha_expiracion_password,
        creado_por,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        TRIM(p_username),
        p_password_hash,
        TRIM(p_email),
        TRIM(p_primer_nombre),
        TRIM(p_apellido_paterno),
        NULLIF(TRIM(IFNULL(p_apellido_materno, '')), ''),
        NULLIF(TRIM(IFNULL(p_telefono, '')), ''),
        1,
        1,
        DATE_ADD(NOW(), INTERVAL 3 MONTH),
        p_creado_por,
        NOW(),
        NOW()
    );

    SET v_usuario_id = LAST_INSERT_ID();

    IF p_rol_id IS NOT NULL THEN
        INSERT INTO usuario_roles (sede_id, usuario_id, rol_id, asignado_por, estado)
        VALUES (p_sede_id, v_usuario_id, p_rol_id, p_creado_por, 1);
    END IF;

    SET p_mensaje = 'Usuario registrado correctamente';
END //

-- 4. ACTUALIZAR USUARIO (sin cambiar contraseña; incluye rol)
DROP PROCEDURE IF EXISTS update_usuario //
CREATE PROCEDURE update_usuario (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_username VARCHAR(50),
    IN p_email VARCHAR(100),
    IN p_primer_nombre VARCHAR(50),
    IN p_apellido_paterno VARCHAR(50),
    IN p_apellido_materno VARCHAR(50),
    IN p_telefono VARCHAR(15),
    IN p_estado INT,
    IN p_rol_id INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar el usuario';
    END;

    IF NOT EXISTS (SELECT 1 FROM usuarios WHERE id = p_id AND sede_id = p_sede_id) THEN
        SET p_mensaje = 'El usuario no existe en esta sede';
        LEAVE proc;
    END IF;

    IF p_rol_id IS NOT NULL AND NOT EXISTS (SELECT 1 FROM roles WHERE id = p_rol_id AND sede_id = p_sede_id AND estado = 1) THEN
        SET p_mensaje = 'El rol no existe o no pertenece a esta sede';
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1 FROM usuarios
        WHERE sede_id = p_sede_id AND username = p_username AND id <> p_id
    ) THEN
        SET p_mensaje = 'El nombre de usuario ya está en uso';
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1 FROM usuarios
        WHERE sede_id = p_sede_id AND email = p_email AND id <> p_id
    ) THEN
        SET p_mensaje = 'El correo ya está en uso por otro usuario';
        LEAVE proc;
    END IF;

    UPDATE usuarios
    SET
        username = TRIM(p_username),
        email = TRIM(p_email),
        primer_nombre = TRIM(p_primer_nombre),
        apellido_paterno = TRIM(p_apellido_paterno),
        apellido_materno = NULLIF(TRIM(IFNULL(p_apellido_materno, '')), ''),
        telefono = NULLIF(TRIM(IFNULL(p_telefono, '')), ''),
        estado = IFNULL(p_estado, estado),
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    -- Actualizar o insertar rol del usuario (un rol por usuario en esta sede)
    IF p_rol_id IS NOT NULL THEN
        IF EXISTS (SELECT 1 FROM usuario_roles WHERE usuario_id = p_id AND sede_id = p_sede_id) THEN
            UPDATE usuario_roles
            SET rol_id = p_rol_id
            WHERE usuario_id = p_id AND sede_id = p_sede_id;
        ELSE
            INSERT INTO usuario_roles (sede_id, usuario_id, rol_id, asignado_por, estado)
            VALUES (p_sede_id, p_id, p_rol_id, p_modificado_por, 1);
        END IF;
    END IF;

    SET p_mensaje = 'Usuario actualizado correctamente';
END //

-- 5. DAR DE BAJA USUARIO (estado = 0)
DROP PROCEDURE IF EXISTS disable_usuario //
CREATE PROCEDURE disable_usuario (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    IF NOT EXISTS (SELECT 1 FROM usuarios WHERE id = p_id AND sede_id = p_sede_id) THEN
        SET p_mensaje = 'El usuario no existe en esta sede';
        LEAVE proc;
    END IF;

    UPDATE usuarios
    SET
        estado = 0,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    SET p_mensaje = 'Usuario dado de baja correctamente';
END //

DELIMITER ;
