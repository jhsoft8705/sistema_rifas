-- =====================================================
-- PROCEDIMIENTOS DE AUTENTICACIÓN - SISTEMA CAFED
-- =====================================================

-- =====================================================
-- 1. PROCEDIMIENTO DE LOGIN
-- =====================================================
DROP PROCEDURE IF EXISTS sp_Login;
GO
CREATE PROCEDURE sp_Login
    @username VARCHAR(50),
    @password VARCHAR(255),
    @ip_address VARCHAR(45),
    @user_agent VARCHAR(500),
    @sede_id INT = NULL
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @usuario_id INT = NULL;
    DECLARE @password_hash VARCHAR(255);
    DECLARE @usuario_sede_id INT;
    DECLARE @estado_usuario INT;
    DECLARE @cuenta_bloqueada BIT;
    DECLARE @intentos_fallidos INT;
    DECLARE @fecha_expiracion_password DATETIME;
    DECLARE @token_sesion VARCHAR(255);
    DECLARE @fecha_expiracion DATETIME;
    DECLARE @resultado BIT = 0;
    DECLARE @mensaje VARCHAR(255) = '';
    DECLARE @empleado_id INT;
    DECLARE @nombre_completo VARCHAR(200) = '';
    DECLARE @sede_nombre VARCHAR(200) = '';
    DECLARE @rol_id INT = NULL;
    DECLARE @rol_nombre VARCHAR(50) = '';
    
    BEGIN TRY
        -- Buscar usuario por username
        SELECT 
            @usuario_id = id,
            @password_hash = password_hash,
            @usuario_sede_id = sede_id,
            @estado_usuario = estado,
            @cuenta_bloqueada = cuenta_bloqueada,
            @intentos_fallidos = intentos_fallidos,
            @fecha_expiracion_password = fecha_expiracion_password,
            @empleado_id = empleado_id,
            @nombre_completo = COALESCE(primer_nombre + ' ' + apellido_paterno, '')
        FROM usuarios 
        WHERE username = @username;
        
        -- Verificar si el usuario existe
        IF @usuario_id IS NULL
        BEGIN
            -- Registrar intento fallido
            INSERT INTO intentos_acceso (username, ip_address, user_agent, exito, motivo_fallo)
            VALUES (@username, @ip_address, @user_agent, 0, 'Usuario no encontrado');
            
            SET @mensaje = 'Usuario no encontrado';
        END
        ELSE
        BEGIN
            -- Verificar si la cuenta está bloqueada
            IF @cuenta_bloqueada = 1
            BEGIN
                INSERT INTO intentos_acceso (username, ip_address, user_agent, exito, motivo_fallo)
                VALUES (@username, @ip_address, @user_agent, 0, 'Cuenta bloqueada');
                
                SET @mensaje = 'Cuenta bloqueada';
            END
            -- Verificar si el usuario está activo
            ELSE IF @estado_usuario != 1
            BEGIN
                INSERT INTO intentos_acceso (username, ip_address, user_agent, exito, motivo_fallo)
                VALUES (@username, @ip_address, @user_agent, 0, 'Usuario inactivo');
                
                SET @mensaje = 'Usuario inactivo';
            END
            -- Verificar sede (si se especifica)
            ELSE IF @sede_id IS NOT NULL AND @usuario_sede_id != @sede_id
            BEGIN
                INSERT INTO intentos_acceso (username, ip_address, user_agent, exito, motivo_fallo)
                VALUES (@username, @ip_address, @user_agent, 0, 'Usuario no pertenece a esta sede');
                
                SET @mensaje = 'Usuario no pertenece a esta sede';
            END
            -- Verificar contraseña
            ELSE IF @password_hash != @password
            BEGIN
                -- Incrementar intentos fallidos
                UPDATE usuarios 
                SET intentos_fallidos = intentos_fallidos + 1,
                    fecha_modificacion = GETDATE()
                WHERE id = @usuario_id;
                
                -- Bloquear cuenta si supera 5 intentos
                IF (@intentos_fallidos + 1) >= 5
                BEGIN
                    UPDATE usuarios 
                    SET cuenta_bloqueada = 1, 
                        fecha_bloqueo = GETDATE(),
                        fecha_modificacion = GETDATE()
                    WHERE id = @usuario_id;
                    
                    SET @mensaje = 'Contraseña incorrecta. Cuenta bloqueada por múltiples intentos fallidos';
                END
                ELSE
                BEGIN
                    SET @mensaje = 'Contraseña incorrecta';
                END
                
                INSERT INTO intentos_acceso (username, ip_address, user_agent, exito, motivo_fallo)
                VALUES (@username, @ip_address, @user_agent, 0, 'Contraseña incorrecta');
            END
            ELSE
            BEGIN
                -- Login exitoso
                SET @resultado = 1;
                SET @mensaje = 'Login exitoso';
                
                -- *** IMPORTANTE: INVALIDAR TODAS LAS SESIONES ANTERIORES DEL USUARIO ***
                -- (CORREGIDO: tabla sesiones no tiene columna fecha_modificacion)
                UPDATE sesiones 
                SET activa = 0
                WHERE usuario_id = @usuario_id 
                  AND activa = 1;
                
                -- Resetear intentos fallidos
                UPDATE usuarios 
                SET intentos_fallidos = 0,
                    cuenta_bloqueada = 0,
                    fecha_bloqueo = NULL,
                    ultimo_acceso = GETDATE(),
                    fecha_modificacion = GETDATE()
                WHERE id = @usuario_id;
                
                -- Obtener información de la sede
                SELECT @sede_nombre = nombre
                FROM sedes
                WHERE id = @usuario_sede_id;
                
                -- Obtener rol del usuario (el primer rol activo)
                SELECT TOP 1
                    @rol_id = r.id,
                    @rol_nombre = r.nombre
                FROM usuario_roles ur
                INNER JOIN roles r ON ur.rol_id = r.id
                WHERE ur.usuario_id = @usuario_id 
                  AND ur.sede_id = @usuario_sede_id
                  AND ur.estado = 1
                ORDER BY r.nivel_acceso DESC; -- Obtener el rol con mayor nivel de acceso
                
                -- Generar token de sesión
                SET @token_sesion = CONVERT(VARCHAR(255), NEWID());
                SET @fecha_expiracion = DATEADD(HOUR, 8, GETDATE()); -- Sesión válida por 8 horas
                
                -- Crear NUEVA sesión
                INSERT INTO sesiones (sede_id, usuario_id, token_sesion, ip_address, user_agent, fecha_expiracion)
                VALUES (@usuario_sede_id, @usuario_id, @token_sesion, @ip_address, @user_agent, @fecha_expiracion);
                
                -- Registrar intento exitoso
                INSERT INTO intentos_acceso (username, ip_address, user_agent, exito, motivo_fallo)
                VALUES (@username, @ip_address, @user_agent, 1, 'Login exitoso');
            END
        END
        
        -- Retornar resultado CON INFORMACIÓN ADICIONAL
        SELECT 
            @resultado AS resultado,
            @mensaje AS mensaje,
            @usuario_id AS usuario_id,
            @token_sesion AS token_sesion,
            @fecha_expiracion AS fecha_expiracion,
            @usuario_sede_id AS sede_id,
            @sede_nombre AS sede_nombre,
            @empleado_id AS empleado_id,
            @nombre_completo AS nombre_completo,
            @rol_id AS rol_id,
            @rol_nombre AS rol_nombre,
            @fecha_expiracion_password AS debe_cambiar_password;
            
    END TRY
    BEGIN CATCH
        SET @resultado = 0;
        SET @mensaje = 'Error en el proceso de login: ' + ERROR_MESSAGE();
        
        -- Registrar intento fallido por error
        INSERT INTO intentos_acceso (username, ip_address, user_agent, exito, motivo_fallo)
        VALUES (@username, @ip_address, @user_agent, 0, 'Error: ' + ERROR_MESSAGE());
        
        SELECT 
            @resultado AS resultado,
            @mensaje AS mensaje,
            NULL AS usuario_id,
            NULL AS token_sesion,
            NULL AS fecha_expiracion,
            NULL AS sede_id,
            NULL AS sede_nombre,
            NULL AS empleado_id,
            NULL AS nombre_completo,
            NULL AS rol_id,
            NULL AS rol_nombre,
            NULL AS debe_cambiar_password;
    END CATCH
END
GO


-- =====================================================
-- 2. PROCEDIMIENTO DE LOGOUT
-- =====================================================
DROP PROCEDURE IF EXISTS sp_Logout;
GO
CREATE PROCEDURE sp_Logout
    @token_sesion VARCHAR(255),
    @ip_address VARCHAR(45) = NULL
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @usuario_id INT;
    DECLARE @sede_id INT;
    DECLARE @resultado BIT = 0;
    DECLARE @mensaje VARCHAR(255) = '';
    
    BEGIN TRY
        -- Buscar sesión activa
        SELECT @usuario_id = usuario_id, @sede_id = sede_id
        FROM sesiones 
        WHERE token_sesion = @token_sesion AND activa = 1;
        
        IF @usuario_id IS NOT NULL
        BEGIN
            -- Marcar sesión como inactiva
            UPDATE sesiones 
            SET activa = 0,
                fecha_ultima_actividad = GETDATE()
            WHERE token_sesion = @token_sesion AND activa = 1;
            
            SET @resultado = 1;
            SET @mensaje = 'Logout exitoso';
            
            -- Registrar en auditoría
            DECLARE @username_logout VARCHAR(50);
            SELECT @username_logout = username FROM usuarios WHERE id = @usuario_id;
            INSERT INTO audit_logs (tabla_afectada, registro_id, operacion, usuario_id, ip_address)
            VALUES ('sesiones', @usuario_id, 'LOGOUT', @username_logout, @ip_address);
        END
        ELSE
        BEGIN
            SET @mensaje = 'Sesión no encontrada o ya inactiva';
        END
        
        SELECT @resultado AS resultado, @mensaje AS mensaje;
        
    END TRY
    BEGIN CATCH
        SET @resultado = 0;
        SET @mensaje = 'Error en logout: ' + ERROR_MESSAGE();
        SELECT @resultado AS resultado, @mensaje AS mensaje;
    END CATCH
END
GO

-- =====================================================
-- 3. PROCEDIMIENTO PARA VALIDAR SESIÓN
-- =====================================================
DROP PROCEDURE IF EXISTS sp_ValidarSesion;
GO
CREATE PROCEDURE sp_ValidarSesion
    @token_sesion VARCHAR(255),
    @ip_address VARCHAR(45) = NULL
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @usuario_id INT;
    DECLARE @sede_id INT;
    DECLARE @fecha_expiracion DATETIME;
    DECLARE @activa BIT;
    DECLARE @username VARCHAR(50);
    DECLARE @resultado BIT = 0;
    DECLARE @mensaje VARCHAR(255) = '';
    
    BEGIN TRY
        -- Buscar sesión activa
        SELECT 
            @usuario_id = s.usuario_id, 
            @sede_id = s.sede_id,
            @fecha_expiracion = s.fecha_expiracion,
            @activa = s.activa,
            @username = u.username
        FROM sesiones s
        INNER JOIN usuarios u ON s.usuario_id = u.id
        WHERE s.token_sesion = @token_sesion;
        
        IF @usuario_id IS NULL
        BEGIN
            SET @mensaje = 'Token de sesión no encontrado';
        END
        ELSE IF @activa = 0
        BEGIN
            SET @mensaje = 'Sesión inactiva';
        END
        ELSE IF @fecha_expiracion < GETDATE()
        BEGIN
            -- Marcar sesión como expirada
            UPDATE sesiones 
            SET activa = 0,
                fecha_ultima_actividad = GETDATE()
            WHERE token_sesion = @token_sesion;
            
            SET @mensaje = 'Sesión expirada';
        END
        ELSE
        BEGIN
            -- Sesión válida, actualizar última actividad
            UPDATE sesiones 
            SET fecha_ultima_actividad = GETDATE()
            WHERE token_sesion = @token_sesion;
            
            SET @resultado = 1;
            SET @mensaje = 'Sesión válida';
        END
        
        SELECT 
            @resultado AS resultado,
            @mensaje AS mensaje,
            @usuario_id AS usuario_id,
            @sede_id AS sede_id,
            @username AS username,
            @fecha_expiracion AS fecha_expiracion;
            
    END TRY
    BEGIN CATCH
        SET @resultado = 0;
        SET @mensaje = 'Error al validar sesión: ' + ERROR_MESSAGE();
        
        SELECT 
            @resultado AS resultado,
            @mensaje AS mensaje,
            NULL AS usuario_id,
            NULL AS sede_id,
            NULL AS username,
            NULL AS fecha_expiracion;
    END CATCH
END
GO

-- =====================================================
-- 4. PROCEDIMIENTO PARA OBTENER PERMISOS DE USUARIO
-- =====================================================
CREATE PROCEDURE sp_ObtenerPermisosUsuario
    @usuario_id INT
AS
BEGIN
    SET NOCOUNT ON;
    
    BEGIN TRY
        -- Obtener permisos directos del usuario
        SELECT DISTINCT
            p.id,
            p.nombre,
            p.descripcion,
            p.modulo,
            p.accion,
            'DIRECTO' AS tipo_permiso
        FROM permisos p
        INNER JOIN usuario_permisos up ON p.id = up.permiso_id
        WHERE up.usuario_id = @usuario_id 
        AND up.estado = 1
        AND (up.fecha_vencimiento IS NULL OR up.fecha_vencimiento > GETDATE())
        AND p.estado = 1
        
        UNION
        
        -- Obtener permisos a través de roles
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
        WHERE ur.usuario_id = @usuario_id
        AND ur.estado = 1
        AND (ur.fecha_vencimiento IS NULL OR ur.fecha_vencimiento > GETDATE())
        AND rp.estado = 1
        AND r.estado = 1
        AND p.estado = 1
        
        ORDER BY modulo, accion;
        
    END TRY
    BEGIN CATCH
        SELECT 
            ERROR_MESSAGE() AS error,
            ERROR_NUMBER() AS error_number;
    END CATCH
END
GO

-- =====================================================
-- 5. PROCEDIMIENTO PARA CAMBIAR CONTRASEÑA
-- =====================================================
CREATE PROCEDURE sp_CambiarPassword
    @usuario_id INT,
    @password_actual VARCHAR(255),
    @password_nuevo VARCHAR(255),
    @ip_address VARCHAR(45) = NULL
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @password_hash_actual VARCHAR(255);
    DECLARE @resultado BIT = 0;
    DECLARE @mensaje VARCHAR(255) = '';
    
    BEGIN TRY
        -- Obtener contraseña actual
        SELECT @password_hash_actual = password_hash
        FROM usuarios 
        WHERE id = @usuario_id AND estado = 1;
        
        IF @password_hash_actual IS NULL
        BEGIN
            SET @mensaje = 'Usuario no encontrado o inactivo';
        END
        ELSE IF @password_hash_actual != @password_actual
        BEGIN
            SET @mensaje = 'Contraseña actual incorrecta';
        END
        ELSE
        BEGIN
            -- Actualizar contraseña
            UPDATE usuarios 
            SET password_hash = @password_nuevo,
                debe_cambiar_password = 0,
                fecha_expiracion_password = DATEADD(MONTH, 3, GETDATE()),
                fecha_modificacion = GETDATE()
            WHERE id = @usuario_id;
            
            SET @resultado = 1;
            SET @mensaje = 'Contraseña actualizada exitosamente';
            
            -- Registrar en auditoría
            DECLARE @username_password VARCHAR(50);
            SELECT @username_password = username FROM usuarios WHERE id = @usuario_id;
            INSERT INTO audit_logs (tabla_afectada, registro_id, operacion, usuario_id, ip_address)
            VALUES ('usuarios', @usuario_id, 'CAMBIO_PASSWORD', @username_password, @ip_address);
        END
        
        SELECT @resultado AS resultado, @mensaje AS mensaje;
        
    END TRY
    BEGIN CATCH
        SET @resultado = 0;
        SET @mensaje = 'Error al cambiar contraseña: ' + ERROR_MESSAGE();
        SELECT @resultado AS resultado, @mensaje AS mensaje;
    END CATCH
END
GO

-- =====================================================
-- 6. PROCEDIMIENTO PARA RENOVAR SESIÓN
-- =====================================================
CREATE PROCEDURE sp_RenovarSesion
    @token_sesion VARCHAR(255),
    @ip_address VARCHAR(45) = NULL
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @usuario_id INT;
    DECLARE @fecha_expiracion DATETIME;
    DECLARE @resultado BIT = 0;
    DECLARE @mensaje VARCHAR(255) = '';
    
    BEGIN TRY
        -- Verificar sesión activa
        SELECT @usuario_id = usuario_id, @fecha_expiracion = fecha_expiracion
        FROM sesiones 
        WHERE token_sesion = @token_sesion AND activa = 1;
        
        IF @usuario_id IS NULL
        BEGIN
            SET @mensaje = 'Sesión no encontrada';
        END
        ELSE IF @fecha_expiracion < GETDATE()
        BEGIN
            SET @mensaje = 'Sesión expirada';
        END
        ELSE
        BEGIN
            -- Renovar sesión por 8 horas más
            UPDATE sesiones 
            SET fecha_expiracion = DATEADD(HOUR, 8, GETDATE()),
                fecha_ultima_actividad = GETDATE()
            WHERE token_sesion = @token_sesion;
            
            SET @resultado = 1;
            SET @mensaje = 'Sesión renovada exitosamente';
        END
        
        SELECT 
            @resultado AS resultado,
            @mensaje AS mensaje,
            DATEADD(HOUR, 8, GETDATE()) AS nueva_fecha_expiracion;
            
    END TRY
    BEGIN CATCH
        SET @resultado = 0;
        SET @mensaje = 'Error al renovar sesión: ' + ERROR_MESSAGE();
        SELECT @resultado AS resultado, @mensaje AS mensaje, NULL AS nueva_fecha_expiracion;
    END CATCH
END
GO

-- =====================================================
-- 7. PROCEDIMIENTO PARA LIMPIAR SESIONES EXPIRADAS
-- =====================================================
CREATE PROCEDURE sp_LimpiarSesionesExpiradas
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @sesiones_limpiadas INT = 0;
    
    BEGIN TRY
        -- Marcar sesiones expiradas como inactivas
        UPDATE sesiones 
        SET activa = 0,
            fecha_ultima_actividad = GETDATE()
        WHERE activa = 1 
        AND fecha_expiracion < GETDATE();
        
        SET @sesiones_limpiadas = @@ROWCOUNT;
        
        SELECT 
            1 AS resultado,
            'Limpieza completada' AS mensaje,
            @sesiones_limpiadas AS sesiones_limpiadas;
            
    END TRY
    BEGIN CATCH
        SELECT 
            0 AS resultado,
            'Error en limpieza: ' + ERROR_MESSAGE() AS mensaje,
            0 AS sesiones_limpiadas;
    END CATCH
END
GO

-- =====================================================
-- 8. PROCEDIMIENTO PARA DESBLOQUEAR CUENTA
-- =====================================================
CREATE PROCEDURE sp_DesbloquearCuenta
    @usuario_id INT,
    @usuario_admin INT,
    @ip_address VARCHAR(45) = NULL
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @resultado BIT = 0;
    DECLARE @mensaje VARCHAR(255) = '';
    
    BEGIN TRY
        -- Verificar que el usuario existe
        IF NOT EXISTS (SELECT 1 FROM usuarios WHERE id = @usuario_id)
        BEGIN
            SET @mensaje = 'Usuario no encontrado';
        END
        ELSE
        BEGIN
            -- Desbloquear cuenta
            UPDATE usuarios 
            SET cuenta_bloqueada = 0,
                fecha_bloqueo = NULL,
                intentos_fallidos = 0,
                fecha_modificacion = GETDATE(),
                modificado_por = @usuario_admin
            WHERE id = @usuario_id;
            
            SET @resultado = 1;
            SET @mensaje = 'Cuenta desbloqueada exitosamente';
            
            -- Registrar en auditoría
            DECLARE @username_admin VARCHAR(50);
            SELECT @username_admin = username FROM usuarios WHERE id = @usuario_admin;
            INSERT INTO audit_logs (tabla_afectada, registro_id, operacion, usuario_id, ip_address)
            VALUES ('usuarios', @usuario_id, 'DESBLOQUEAR', @username_admin, @ip_address);
        END
        
        SELECT @resultado AS resultado, @mensaje AS mensaje;
        
    END TRY
    BEGIN CATCH
        SET @resultado = 0;
        SET @mensaje = 'Error al desbloquear cuenta: ' + ERROR_MESSAGE();
        SELECT @resultado AS resultado, @mensaje AS mensaje;
    END CATCH
END
GO

-- =====================================================
-- 9. PROCEDIMIENTO PARA OBTENER INFORMACIÓN DE USUARIO
-- =====================================================
CREATE PROCEDURE sp_ObtenerInfoUsuario
    @usuario_id INT
AS
BEGIN
    SET NOCOUNT ON;
    
    BEGIN TRY
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
            u.empleado_id,
            e.nombre + ' ' + e.apellido_paterno AS empleado_nombre
        FROM usuarios u
        LEFT JOIN sedes s ON u.sede_id = s.id
        LEFT JOIN empleados e ON u.empleado_id = e.id
        WHERE u.id = @usuario_id;
        
    END TRY
    BEGIN CATCH
        SELECT 
            ERROR_MESSAGE() AS error,
            ERROR_NUMBER() AS error_number;
    END CATCH
END
GO

-- =====================================================
-- 10. PROCEDIMIENTO PARA REGISTRAR ACTIVIDAD
-- =====================================================
CREATE PROCEDURE sp_RegistrarActividad
    @usuario_id INT,
    @accion VARCHAR(100),
    @detalle VARCHAR(500) = NULL,
    @ip_address VARCHAR(45) = NULL,
    @user_agent VARCHAR(500) = NULL
AS
BEGIN
    SET NOCOUNT ON;
    
    BEGIN TRY
        INSERT INTO audit_logs (
            tabla_afectada, 
            registro_id, 
            operacion, 
            usuario_id, 
            ip_address, 
            user_agent,
            datos_nuevos
        )
        VALUES (
            'usuarios', 
            @usuario_id, 
            @accion, 
            @usuario_id, 
            @ip_address, 
            @user_agent,
            @detalle
        );
        
        SELECT 1 AS resultado, 'Actividad registrada' AS mensaje;
        
    END TRY
    BEGIN CATCH
        SELECT 0 AS resultado, 'Error al registrar actividad: ' + ERROR_MESSAGE() AS mensaje;
    END CATCH
END
GO

-- =====================================================
-- 11. PROCEDIMIENTO PARA VALIDAR PERMISO ESPECÍFICO
-- =====================================================
CREATE PROCEDURE sp_ValidarPermiso
    @usuario_id INT,
    @modulo VARCHAR(50),
    @accion VARCHAR(50)
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @tiene_permiso BIT = 0;
    
    BEGIN TRY
        -- Verificar si el usuario tiene el permiso específico
        IF EXISTS (
            -- Permisos directos
            SELECT 1 FROM permisos p
            INNER JOIN usuario_permisos up ON p.id = up.permiso_id
            WHERE up.usuario_id = @usuario_id 
            AND up.estado = 1
            AND (up.fecha_vencimiento IS NULL OR up.fecha_vencimiento > GETDATE())
            AND p.modulo = @modulo 
            AND p.accion = @accion
            AND p.estado = 1
            
            UNION
            
            -- Permisos por rol
            SELECT 1 FROM permisos p
            INNER JOIN rol_permisos rp ON p.id = rp.permiso_id
            INNER JOIN roles r ON rp.rol_id = r.id
            INNER JOIN usuario_roles ur ON r.id = ur.rol_id
            WHERE ur.usuario_id = @usuario_id
            AND ur.estado = 1
            AND (ur.fecha_vencimiento IS NULL OR ur.fecha_vencimiento > GETDATE())
            AND rp.estado = 1
            AND r.estado = 1
            AND p.modulo = @modulo 
            AND p.accion = @accion
            AND p.estado = 1
        )
        BEGIN
            SET @tiene_permiso = 1;
        END
        
        SELECT @tiene_permiso AS tiene_permiso;
        
    END TRY
    BEGIN CATCH
        SELECT 0 AS tiene_permiso;
    END CATCH
END
GO
