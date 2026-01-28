-- =============================================
-- STORED PROCEDURES PARA MENSAJES DE CONTACTO (Landing Contáctanos)
-- =============================================

DELIMITER //

-- 1. REGISTRAR MENSAJE DE CONTACTO
DROP PROCEDURE IF EXISTS register_contacto //
CREATE PROCEDURE register_contacto (
    IN p_sede_id INT,
    IN p_nombre VARCHAR(200),
    IN p_email VARCHAR(150),
    IN p_telefono VARCHAR(20),
    IN p_asunto VARCHAR(255),
    IN p_mensaje TEXT,
    IN p_ip_origen VARCHAR(45),
    OUT p_mensaje_salida VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje_salida = 'Error al registrar el mensaje de contacto';
    END;

    IF TRIM(IFNULL(p_nombre, '')) = '' THEN
        SET p_mensaje_salida = 'El nombre es obligatorio';
        LEAVE proc;
    END IF;

    IF TRIM(IFNULL(p_email, '')) = '' THEN
        SET p_mensaje_salida = 'El correo electrónico es obligatorio';
        LEAVE proc;
    END IF;

    IF TRIM(IFNULL(p_asunto, '')) = '' THEN
        SET p_mensaje_salida = 'El asunto es obligatorio';
        LEAVE proc;
    END IF;

    IF TRIM(IFNULL(p_mensaje, '')) = '' THEN
        SET p_mensaje_salida = 'El mensaje es obligatorio';
        LEAVE proc;
    END IF;

    INSERT INTO contactos (
        sede_id,
        nombre,
        email,
        telefono,
        asunto,
        mensaje,
        ip_origen,
        estado,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        TRIM(p_nombre),
        TRIM(p_email),
        NULLIF(TRIM(IFNULL(p_telefono, '')), ''),
        TRIM(p_asunto),
        TRIM(p_mensaje),
        NULLIF(TRIM(IFNULL(p_ip_origen, '')), ''),
        1,
        NOW(),
        NOW()
    );

    SET p_mensaje_salida = 'Mensaje enviado correctamente. Te responderemos a la brevedad.';
END //

DELIMITER ;
