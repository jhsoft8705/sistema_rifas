-- =====================================================
-- PROCEDIMIENTOS DE AUTENTICACIÓN - SISTEMA RIFAS (MySQL)
-- =====================================================

DELIMITER //

-- =====================================================
-- 1. PROCEDIMIENTO DE LOGIN
-- =====================================================
DROP PROCEDURE IF EXISTS sp_Login //
CREATE PROCEDURE sp_Login (
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(255),
    IN p_ip_address VARCHAR(45),
    IN p_user_agent VARCHAR(500),
    IN p_sede_id INT
)
BEGIN
    DECLARE v_usuario_id INT DEFAULT NULL;
    DECLARE v_password_hash VARCHAR(255) DEFAULT NULL;
    DECLARE v_usuario_sede_id INT DEFAULT NULL;
    DECLARE v_estado_usuario INT DEFAULT NULL;
    DECLARE v_cuenta_bloqueada TINYINT DEFAULT 0;
    DECLARE v_intentos_fallidos INT DEFAULT 0;
    DECLARE v_fecha_expiracion_password DATETIME DEFAULT NULL;
    DECLARE v_token_sesion VARCHAR(255) DEFAULT NULL;
    DECLARE v_fecha_expiracion DATETIME DEFAULT NULL;
    DECLARE v_resultado TINYINT DEFAULT 0;
    DECLARE v_mensaje VARCHAR(255) DEFAULT '';
    DECLARE v_nombre_completo VARCHAR(200) DEFAULT '';
    DECLARE v_sede_nombre VARCHAR(200) DEFAULT '';
    DECLARE v_rol_id INT DEFAULT NULL;
    DECLARE v_rol_nombre VARCHAR(50) DEFAULT '';
    DECLARE v_debe_cambiar_password TINYINT DEFAULT 0;
    DECLARE v_not_found INT DEFAULT 0;
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE CONTINUE HANDLER FOR NOT FOUND
        SET v_not_found = 1;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SET v_resultado = 0;
        SET v_mensaje = CONCAT('Error en el proceso de login: ', COALESCE(v_sqlmsg, 'Error desconocido'));
        INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
        VALUES (NULL, p_username, p_ip_address, p_user_agent, 0, v_mensaje);
    END;

    main_block: BEGIN
        SET v_not_found = 0;
        SELECT 
            u.id,
            u.password_hash,
            u.sede_id,
            u.estado,
            u.cuenta_bloqueada,
            u.intentos_fallidos,
            u.fecha_expiracion_password,
            u.debe_cambiar_password,
            TRIM(CONCAT(COALESCE(u.primer_nombre, ''), ' ', COALESCE(u.apellido_paterno, ''))) AS nombre
        INTO 
            v_usuario_id,
            v_password_hash,
            v_usuario_sede_id,
            v_estado_usuario,
            v_cuenta_bloqueada,
            v_intentos_fallidos,
            v_fecha_expiracion_password,
            v_debe_cambiar_password,
            v_nombre_completo
        FROM usuarios u
        WHERE u.username = p_username
        LIMIT 1;

        IF v_not_found = 1 OR v_usuario_id IS NULL THEN
            SET v_mensaje = 'Usuario no encontrado';
            INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
            VALUES (NULL, p_username, p_ip_address, p_user_agent, 0, v_mensaje);
            LEAVE main_block;
        END IF;

        IF v_cuenta_bloqueada = 1 THEN
            SET v_mensaje = 'Cuenta bloqueada';
            INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
            VALUES (v_usuario_sede_id, p_username, p_ip_address, p_user_agent, 0, v_mensaje);
            LEAVE main_block;
        END IF;

        IF v_estado_usuario <> 1 THEN
            SET v_mensaje = 'Usuario inactivo';
            INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
            VALUES (v_usuario_sede_id, p_username, p_ip_address, p_user_agent, 0, v_mensaje);
            LEAVE main_block;
        END IF;

        IF p_sede_id IS NOT NULL AND v_usuario_sede_id <> p_sede_id THEN
            SET v_mensaje = 'Usuario no pertenece a esta sede';
            INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
            VALUES (v_usuario_sede_id, p_username, p_ip_address, p_user_agent, 0, v_mensaje);
            LEAVE main_block;
        END IF;

        IF v_password_hash <> p_password THEN
            UPDATE usuarios
            SET intentos_fallidos = intentos_fallidos + 1,
                fecha_modificacion = NOW(),
                modificado_por = p_username
            WHERE id = v_usuario_id;

            SET v_intentos_fallidos = v_intentos_fallidos + 1;

            IF v_intentos_fallidos >= 5 THEN
                UPDATE usuarios
                SET cuenta_bloqueada = 1,
                    fecha_bloqueo = NOW(),
                    fecha_modificacion = NOW(),
                    modificado_por = p_username
                WHERE id = v_usuario_id;
                SET v_mensaje = 'Contraseña incorrecta. Cuenta bloqueada por múltiples intentos fallidos';
            ELSE
                SET v_mensaje = 'Contraseña incorrecta';
            END IF;

            INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
            VALUES (v_usuario_sede_id, p_username, p_ip_address, p_user_agent, 0, v_mensaje);
            LEAVE main_block;
        END IF;

        SET v_resultado = 1;
        SET v_mensaje = 'Login exitoso';

        UPDATE sesiones
        SET activa = 0
        WHERE usuario_id = v_usuario_id
          AND activa = 1;

        UPDATE usuarios
        SET intentos_fallidos = 0,
            cuenta_bloqueada = 0,
            fecha_bloqueo = NULL,
            ultimo_acceso = NOW(),
            fecha_modificacion = NOW(),
            modificado_por = p_username
        WHERE id = v_usuario_id;

        SET v_not_found = 0;
        SELECT s.nombre
        INTO v_sede_nombre
        FROM sedes s
        WHERE s.id = v_usuario_sede_id
        LIMIT 1;

        SET v_not_found = 0;
        SELECT r.id, r.nombre
        INTO v_rol_id, v_rol_nombre
        FROM usuario_roles ur
        INNER JOIN roles r ON ur.rol_id = r.id
        WHERE ur.usuario_id = v_usuario_id
          AND ur.sede_id = v_usuario_sede_id
          AND ur.estado = 1
        ORDER BY r.nivel_acceso DESC
        LIMIT 1;

        SET v_token_sesion = UUID();
        SET v_fecha_expiracion = DATE_ADD(NOW(), INTERVAL 8 HOUR);

        INSERT INTO sesiones (
            sede_id,
            usuario_id,
            token_sesion,
            ip_address,
            user_agent,
            fecha_expiracion
        ) VALUES (
            v_usuario_sede_id,
            v_usuario_id,
            v_token_sesion,
            p_ip_address,
            p_user_agent,
            v_fecha_expiracion
        );

        INSERT INTO intentos_acceso (sede_id, username, ip_address, user_agent, exito, motivo_fallo)
        VALUES (v_usuario_sede_id, p_username, p_ip_address, p_user_agent, 1, 'Login exitoso');
    END main_block;

    SELECT 
        v_resultado AS resultado,
        v_mensaje AS mensaje,
        v_usuario_id AS usuario_id,
        v_token_sesion AS token_sesion,
        v_fecha_expiracion AS fecha_expiracion,
        v_usuario_sede_id AS sede_id,
        v_sede_nombre AS sede_nombre,
        NULL AS empleado_id,
        v_nombre_completo AS nombre_completo,
        v_rol_id AS rol_id,
        v_rol_nombre AS rol_nombre,
        v_debe_cambiar_password AS debe_cambiar_password,
        v_fecha_expiracion_password AS fecha_expiracion_password;
END //

-- =====================================================
-- 2. PROCEDIMIENTO DE LOGOUT
-- =====================================================
DROP PROCEDURE IF EXISTS sp_Logout //
CREATE PROCEDURE sp_Logout (
    IN p_token_sesion VARCHAR(255),
    IN p_ip_address VARCHAR(45)
)
BEGIN
    DECLARE v_usuario_id INT DEFAULT NULL;
    DECLARE v_sede_id INT DEFAULT NULL;
    DECLARE v_username VARCHAR(50) DEFAULT NULL;
    DECLARE v_resultado TINYINT DEFAULT 0;
    DECLARE v_mensaje VARCHAR(255) DEFAULT '';
    DECLARE v_not_found INT DEFAULT 0;
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE CONTINUE HANDLER FOR NOT FOUND
        SET v_not_found = 1;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SET v_resultado = 0;
        SET v_mensaje = CONCAT('Error en logout: ', COALESCE(v_sqlmsg, 'Error desconocido'));
    END;

    SET v_not_found = 0;
    SELECT usuario_id, sede_id
    INTO v_usuario_id, v_sede_id
    FROM sesiones
    WHERE token_sesion = p_token_sesion
      AND activa = 1
    LIMIT 1;

    IF v_not_found = 1 OR v_usuario_id IS NULL THEN
        SET v_mensaje = 'Sesión no encontrada o ya inactiva';
    ELSE
        UPDATE sesiones
        SET activa = 0,
            fecha_ultima_actividad = NOW()
        WHERE token_sesion = p_token_sesion
          AND activa = 1;

        SET v_resultado = 1;
        SET v_mensaje = 'Logout exitoso';

        SET v_not_found = 0;
        SELECT username
        INTO v_username
        FROM usuarios
        WHERE id = v_usuario_id
        LIMIT 1;

        INSERT INTO audit_logs (
            sede_id,
            tabla_afectada,
            registro_id,
            operacion,
            usuario_id,
            ip_address
        ) VALUES (
            v_sede_id,
            'sesiones',
            v_usuario_id,
            'LOGOUT',
            v_username,
            p_ip_address
        );
    END IF;

    SELECT v_resultado AS resultado, v_mensaje AS mensaje;
END //

-- =====================================================
-- 3. PROCEDIMIENTO PARA VALIDAR SESIÓN
-- =====================================================
DROP PROCEDURE IF EXISTS sp_ValidarSesion //
CREATE PROCEDURE sp_ValidarSesion (
    IN p_token_sesion VARCHAR(255),
    IN p_ip_address VARCHAR(45)
)
BEGIN
    DECLARE v_usuario_id INT DEFAULT NULL;
    DECLARE v_sede_id INT DEFAULT NULL;
    DECLARE v_fecha_expiracion DATETIME DEFAULT NULL;
    DECLARE v_activa TINYINT DEFAULT 0;
    DECLARE v_username VARCHAR(50) DEFAULT NULL;
    DECLARE v_nombre_completo VARCHAR(200) DEFAULT NULL;
    DECLARE v_resultado TINYINT DEFAULT 0;
    DECLARE v_mensaje VARCHAR(255) DEFAULT '';
    DECLARE v_not_found INT DEFAULT 0;
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE CONTINUE HANDLER FOR NOT FOUND
        SET v_not_found = 1;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SET v_resultado = 0;
        SET v_mensaje = CONCAT('Error al validar sesión: ', COALESCE(v_sqlmsg, 'Error desconocido'));
    END;

    SET v_not_found = 0;
    SELECT 
        s.usuario_id,
        s.sede_id,
        s.fecha_expiracion,
        s.activa,
        u.username,
        TRIM(CONCAT(COALESCE(u.primer_nombre, ''), ' ', COALESCE(u.apellido_paterno, ''))) AS nombre
    INTO 
        v_usuario_id,
        v_sede_id,
        v_fecha_expiracion,
        v_activa,
        v_username,
        v_nombre_completo
    FROM sesiones s
    INNER JOIN usuarios u ON s.usuario_id = u.id
    WHERE s.token_sesion = p_token_sesion
    LIMIT 1;

    IF v_not_found = 1 OR v_usuario_id IS NULL THEN
        SET v_mensaje = 'Token de sesión no encontrado';
    ELSEIF v_activa = 0 THEN
        SET v_mensaje = 'Sesión inactiva';
    ELSEIF v_fecha_expiracion < NOW() THEN
        UPDATE sesiones
        SET activa = 0,
            fecha_ultima_actividad = NOW()
        WHERE token_sesion = p_token_sesion;
        SET v_mensaje = 'Sesión expirada';
    ELSE
        UPDATE sesiones
        SET fecha_ultima_actividad = NOW()
        WHERE token_sesion = p_token_sesion;
        SET v_resultado = 1;
        SET v_mensaje = 'Sesión válida';
    END IF;

    SELECT 
        v_resultado AS resultado,
        v_mensaje AS mensaje,
        v_usuario_id AS usuario_id,
        v_sede_id AS sede_id,
        v_username AS username,
        v_nombre_completo AS nombre_completo,
        v_fecha_expiracion AS fecha_expiracion;
END //

-- =====================================================
-- 4. PROCEDIMIENTO PARA OBTENER PERMISOS DE USUARIO
-- =====================================================
DROP PROCEDURE IF EXISTS sp_ObtenerPermisosUsuario //
CREATE PROCEDURE sp_ObtenerPermisosUsuario (
    IN p_usuario_id INT
)
BEGIN
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SELECT 
            NULL AS id,
            CONCAT('Error al obtener permisos: ', COALESCE(v_sqlmsg, 'Error desconocido')) AS nombre,
            NULL AS descripcion,
            NULL AS modulo,
            NULL AS accion,
            NULL AS tipo_permiso;
    END;

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
      AND up.estado = 1
      AND (up.fecha_vencimiento IS NULL OR up.fecha_vencimiento > NOW())
      AND p.estado = 1

    UNION

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
      AND ur.estado = 1
      AND (ur.fecha_vencimiento IS NULL OR ur.fecha_vencimiento > NOW())
      AND rp.estado = 1
      AND r.estado = 1
      AND p.estado = 1

    ORDER BY modulo, accion;
END //

-- =====================================================
-- 5. PROCEDIMIENTO PARA CAMBIAR CONTRASEÑA
-- =====================================================
DROP PROCEDURE IF EXISTS sp_CambiarPassword //
CREATE PROCEDURE sp_CambiarPassword (
    IN p_usuario_id INT,
    IN p_password_actual VARCHAR(255),
    IN p_password_nuevo VARCHAR(255),
    IN p_ip_address VARCHAR(45)
)
BEGIN
    DECLARE v_password_hash_actual VARCHAR(255) DEFAULT NULL;
    DECLARE v_resultado TINYINT DEFAULT 0;
    DECLARE v_mensaje VARCHAR(255) DEFAULT '';
    DECLARE v_username VARCHAR(50) DEFAULT NULL;
    DECLARE v_not_found INT DEFAULT 0;
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE CONTINUE HANDLER FOR NOT FOUND
        SET v_not_found = 1;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SET v_resultado = 0;
        SET v_mensaje = CONCAT('Error al cambiar contraseña: ', COALESCE(v_sqlmsg, 'Error desconocido'));
    END;

    SET v_not_found = 0;
    SELECT password_hash
    INTO v_password_hash_actual
    FROM usuarios
    WHERE id = p_usuario_id
      AND estado = 1
    LIMIT 1;

    IF v_not_found = 1 OR v_password_hash_actual IS NULL THEN
        SET v_mensaje = 'Usuario no encontrado o inactivo';
    ELSEIF v_password_hash_actual <> p_password_actual THEN
        SET v_mensaje = 'Contraseña actual incorrecta';
    ELSE
        UPDATE usuarios
        SET password_hash = p_password_nuevo,
            debe_cambiar_password = 0,
            fecha_expiracion_password = DATE_ADD(NOW(), INTERVAL 3 MONTH),
            fecha_modificacion = NOW(),
            modificado_por = NULL
        WHERE id = p_usuario_id;

        SET v_resultado = 1;
        SET v_mensaje = 'Contraseña actualizada exitosamente';

        SET v_not_found = 0;
        SELECT username
        INTO v_username
        FROM usuarios
        WHERE id = p_usuario_id
        LIMIT 1;

        INSERT INTO audit_logs (
            tabla_afectada,
            registro_id,
            operacion,
            usuario_id,
            ip_address
        ) VALUES (
            'usuarios',
            p_usuario_id,
            'CAMBIO_PASSWORD',
            v_username,
            p_ip_address
        );
    END IF;

    SELECT v_resultado AS resultado, v_mensaje AS mensaje;
END //

-- =====================================================
-- 6. PROCEDIMIENTO PARA RENOVAR SESIÓN
-- =====================================================
DROP PROCEDURE IF EXISTS sp_RenovarSesion //
CREATE PROCEDURE sp_RenovarSesion (
    IN p_token_sesion VARCHAR(255),
    IN p_ip_address VARCHAR(45)
)
BEGIN
    DECLARE v_usuario_id INT DEFAULT NULL;
    DECLARE v_fecha_expiracion DATETIME DEFAULT NULL;
    DECLARE v_resultado TINYINT DEFAULT 0;
    DECLARE v_mensaje VARCHAR(255) DEFAULT '';
    DECLARE v_not_found INT DEFAULT 0;
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE CONTINUE HANDLER FOR NOT FOUND
        SET v_not_found = 1;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SET v_resultado = 0;
        SET v_mensaje = CONCAT('Error al renovar sesión: ', COALESCE(v_sqlmsg, 'Error desconocido'));
    END;

    SET v_not_found = 0;
    SELECT usuario_id, fecha_expiracion
    INTO v_usuario_id, v_fecha_expiracion
    FROM sesiones
    WHERE token_sesion = p_token_sesion
      AND activa = 1
    LIMIT 1;

    IF v_not_found = 1 OR v_usuario_id IS NULL THEN
        SET v_mensaje = 'Sesión no encontrada';
    ELSEIF v_fecha_expiracion < NOW() THEN
        SET v_mensaje = 'Sesión expirada';
    ELSE
        UPDATE sesiones
        SET fecha_expiracion = DATE_ADD(NOW(), INTERVAL 8 HOUR),
            fecha_ultima_actividad = NOW()
        WHERE token_sesion = p_token_sesion
          AND activa = 1;

        SET v_resultado = 1;
        SET v_mensaje = 'Sesión renovada exitosamente';
        SET v_fecha_expiracion = DATE_ADD(NOW(), INTERVAL 8 HOUR);
    END IF;

    SELECT 
        v_resultado AS resultado,
        v_mensaje AS mensaje,
        v_fecha_expiracion AS nueva_fecha_expiracion;
END //

-- =====================================================
-- 7. PROCEDIMIENTO PARA LIMPIAR SESIONES EXPIRADAS
-- =====================================================
DROP PROCEDURE IF EXISTS sp_LimpiarSesionesExpiradas //
CREATE PROCEDURE sp_LimpiarSesionesExpiradas ()
BEGIN
    DECLARE v_sesiones_limpiadas INT DEFAULT 0;
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SELECT 
            0 AS resultado,
            CONCAT('Error en limpieza: ', COALESCE(v_sqlmsg, 'Error desconocido')) AS mensaje,
            0 AS sesiones_limpiadas;
    END;

    UPDATE sesiones
    SET activa = 0,
        fecha_ultima_actividad = NOW()
    WHERE activa = 1
      AND fecha_expiracion < NOW();

    SET v_sesiones_limpiadas = ROW_COUNT();

    SELECT 
        1 AS resultado,
        'Limpieza completada' AS mensaje,
        v_sesiones_limpiadas AS sesiones_limpiadas;
END //

-- =====================================================
-- 8. PROCEDIMIENTO PARA DESBLOQUEAR CUENTA
-- =====================================================
DROP PROCEDURE IF EXISTS sp_DesbloquearCuenta //
CREATE PROCEDURE sp_DesbloquearCuenta (
    IN p_usuario_id INT,
    IN p_usuario_admin INT,
    IN p_ip_address VARCHAR(45)
)
BEGIN
    DECLARE v_resultado TINYINT DEFAULT 0;
    DECLARE v_mensaje VARCHAR(255) DEFAULT '';
    DECLARE v_username_admin VARCHAR(50) DEFAULT NULL;
    DECLARE v_not_found INT DEFAULT 0;
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE CONTINUE HANDLER FOR NOT FOUND
        SET v_not_found = 1;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SET v_resultado = 0;
        SET v_mensaje = CONCAT('Error al desbloquear cuenta: ', COALESCE(v_sqlmsg, 'Error desconocido'));
    END;

    SET v_not_found = 0;
    SELECT 1
    FROM usuarios
    WHERE id = p_usuario_id
    LIMIT 1;

    IF v_not_found = 1 THEN
        SET v_mensaje = 'Usuario no encontrado';
    ELSE
        UPDATE usuarios
        SET cuenta_bloqueada = 0,
            fecha_bloqueo = NULL,
            intentos_fallidos = 0,
            fecha_modificacion = NOW(),
            modificado_por = CAST(p_usuario_admin AS CHAR)
        WHERE id = p_usuario_id;

        SET v_resultado = 1;
        SET v_mensaje = 'Cuenta desbloqueada exitosamente';

        SET v_not_found = 0;
        SELECT username
        INTO v_username_admin
        FROM usuarios
        WHERE id = p_usuario_admin
        LIMIT 1;

        INSERT INTO audit_logs (
            tabla_afectada,
            registro_id,
            operacion,
            usuario_id,
            ip_address
        ) VALUES (
            'usuarios',
            p_usuario_id,
            'DESBLOQUEAR',
            v_username_admin,
            p_ip_address
        );
    END IF;

    SELECT v_resultado AS resultado, v_mensaje AS mensaje;
END //

-- =====================================================
-- 9. PROCEDIMIENTO PARA OBTENER INFORMACIÓN DE USUARIO
-- =====================================================
DROP PROCEDURE IF EXISTS sp_ObtenerInfoUsuario //
CREATE PROCEDURE sp_ObtenerInfoUsuario (
    IN p_usuario_id INT
)
BEGIN
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SELECT 
            NULL AS id,
            CONCAT('Error al obtener información del usuario: ', COALESCE(v_sqlmsg, 'Error desconocido')) AS username,
            NULL AS email,
            NULL AS primer_nombre,
            NULL AS apellido_paterno,
            NULL AS apellido_materno,
            NULL AS telefono,
            NULL AS ultimo_acceso,
            NULL AS estado,
            NULL AS cuenta_bloqueada,
            NULL AS debe_cambiar_password,
            NULL AS fecha_expiracion_password,
            NULL AS sede_id,
            NULL AS sede_nombre,
            NULL AS empleado_id,
            NULL AS empleado_nombre;
    END;

    SELECT 
        u.id,
        u.username,
        u.email,
        u.primer_nombre,
        u.apellido_paterno,
        u.apellido_materno,
        u.telefono,
        u.ultimo_acceso,
        u.estado,
        u.cuenta_bloqueada,
        u.debe_cambiar_password,
        u.fecha_expiracion_password,
        u.sede_id,
        s.nombre AS sede_nombre,
        NULL AS empleado_id,
        NULL AS empleado_nombre
    FROM usuarios u
    LEFT JOIN sedes s ON u.sede_id = s.id
    WHERE u.id = p_usuario_id;
END //

-- =====================================================
-- 10. PROCEDIMIENTO PARA REGISTRAR ACTIVIDAD
-- =====================================================
DROP PROCEDURE IF EXISTS sp_RegistrarActividad //
CREATE PROCEDURE sp_RegistrarActividad (
    IN p_usuario_id INT,
    IN p_accion VARCHAR(100),
    IN p_detalle VARCHAR(500),
    IN p_ip_address VARCHAR(45),
    IN p_user_agent VARCHAR(500)
)
BEGIN
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SELECT 0 AS resultado, CONCAT('Error al registrar actividad: ', COALESCE(v_sqlmsg, 'Error desconocido')) AS mensaje;
    END;

    INSERT INTO audit_logs (
        tabla_afectada,
        registro_id,
        operacion,
        usuario_id,
        ip_address,
        user_agent,
        datos_nuevos
    ) VALUES (
        'usuarios',
        p_usuario_id,
        p_accion,
        CAST(p_usuario_id AS CHAR),
        p_ip_address,
        p_user_agent,
        p_detalle
    );

    SELECT 1 AS resultado, 'Actividad registrada' AS mensaje;
END //

-- =====================================================
-- 11. PROCEDIMIENTO PARA VALIDAR PERMISO ESPECÍFICO
-- =====================================================
DROP PROCEDURE IF EXISTS sp_ValidarPermiso //
CREATE PROCEDURE sp_ValidarPermiso (
    IN p_usuario_id INT,
    IN p_modulo VARCHAR(50),
    IN p_accion VARCHAR(50)
)
BEGIN
    DECLARE v_tiene_permiso TINYINT DEFAULT 0;
    DECLARE v_sqlstate CHAR(5);
    DECLARE v_sqlmsg TEXT;

    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        GET DIAGNOSTICS CONDITION 1 v_sqlstate = RETURNED_SQLSTATE, v_sqlmsg = MESSAGE_TEXT;
        SET v_tiene_permiso = 0;
    END;

    IF EXISTS (
        SELECT 1
        FROM permisos p
        INNER JOIN usuario_permisos up ON p.id = up.permiso_id
        WHERE up.usuario_id = p_usuario_id
          AND up.estado = 1
          AND (up.fecha_vencimiento IS NULL OR up.fecha_vencimiento > NOW())
          AND p.modulo = p_modulo
          AND p.accion = p_accion
          AND p.estado = 1

        UNION

        SELECT 1
        FROM permisos p
        INNER JOIN rol_permisos rp ON p.id = rp.permiso_id
        INNER JOIN roles r ON rp.rol_id = r.id
        INNER JOIN usuario_roles ur ON r.id = ur.rol_id
        WHERE ur.usuario_id = p_usuario_id
          AND ur.estado = 1
          AND (ur.fecha_vencimiento IS NULL OR ur.fecha_vencimiento > NOW())
          AND rp.estado = 1
          AND r.estado = 1
          AND p.modulo = p_modulo
          AND p.accion = p_accion
          AND p.estado = 1
    ) THEN
        SET v_tiene_permiso = 1;
    END IF;

    SELECT v_tiene_permiso AS tiene_permiso;
END //

DELIMITER ;

