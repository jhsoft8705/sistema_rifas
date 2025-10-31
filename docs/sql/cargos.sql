-- =============================================
-- STORED PROCEDURES PARA MANTENIMIENTO DE CARGOS (MySQL)
-- Sistema Multi-Sede
-- =============================================

DELIMITER $$

-- 1. PROCEDURE PARA LISTAR CARGOS
DROP PROCEDURE IF EXISTS list_cargo $$
CREATE PROCEDURE list_cargo (IN p_sede_id INT)
BEGIN
    SELECT
        c.id,
        c.sede_id,
        s.nombre AS sede_nombre,
        c.nombre_cargo,
        c.descripcion,
        c.salario_base,
        c.estado,
        c.creado_por,
        c.fecha_creacion,
        c.modificado_por,
        c.fecha_modificacion
    FROM cargos c
    INNER JOIN sedes s ON c.sede_id = s.id
    WHERE c.estado = 1
      AND c.sede_id = p_sede_id
    ORDER BY c.fecha_creacion DESC;
END $$

-- 2. PROCEDURE PARA OBTENER CARGO POR ID
DROP PROCEDURE IF EXISTS list_cargos_by_id $$
CREATE PROCEDURE list_cargos_by_id (
    IN p_id INT,
    IN p_sede_id INT
)
BEGIN
    SELECT
        c.id,
        c.sede_id,
        s.nombre AS sede_nombre,
        c.nombre_cargo,
        c.descripcion,
        c.salario_base,
        c.estado,
        c.creado_por,
        c.fecha_creacion,
        c.modificado_por,
        c.fecha_modificacion
    FROM cargos c
    INNER JOIN sedes s ON c.sede_id = s.id
    WHERE c.estado = 1
      AND c.id = p_id
      AND c.sede_id = p_sede_id;
END $$

-- 3. PROCEDURE PARA REGISTRAR CARGO
DROP PROCEDURE IF EXISTS register_cargo $$
CREATE PROCEDURE register_cargo (
    IN p_sede_id INT,
    IN p_nombre_cargo VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_salario_base DECIMAL(10,2),
    IN p_creado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al registrar el cargo';
    END;

    START TRANSACTION;

    IF NOT EXISTS (SELECT 1 FROM sedes WHERE id = p_sede_id) THEN
        SET p_mensaje = 'La sede no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM cargos
        WHERE nombre_cargo = p_nombre_cargo
          AND sede_id = p_sede_id
          AND estado = 1
    ) THEN
        SET p_mensaje = 'El cargo ya existe en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    INSERT INTO cargos (
        sede_id,
        nombre_cargo,
        descripcion,
        salario_base,
        estado,
        creado_por,
        fecha_creacion,
        fecha_modificacion
    ) VALUES (
        p_sede_id,
        p_nombre_cargo,
        p_descripcion,
        p_salario_base,
        1,
        p_creado_por,
        NOW(),
        NOW()
    );

    COMMIT;
    SET p_mensaje = 'Cargo registrado correctamente';
END $$

-- 4. PROCEDURE PARA ACTUALIZAR CARGO
DROP PROCEDURE IF EXISTS update_cargo $$
CREATE PROCEDURE update_cargo (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_nombre_cargo VARCHAR(100),
    IN p_descripcion VARCHAR(255),
    IN p_salario_base DECIMAL(10,2),
    IN p_estado INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al actualizar el cargo';
    END;

    START TRANSACTION;

    IF NOT EXISTS (SELECT 1 FROM sedes WHERE id = p_sede_id) THEN
        SET p_mensaje = 'La sede no existe';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF NOT EXISTS (
        SELECT 1
        FROM cargos
        WHERE id = p_id
          AND sede_id = p_sede_id
    ) THEN
        SET p_mensaje = 'El cargo no existe en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    IF EXISTS (
        SELECT 1
        FROM cargos
        WHERE nombre_cargo = p_nombre_cargo
          AND sede_id = p_sede_id
          AND id <> p_id
          AND estado = 1
    ) THEN
        SET p_mensaje = 'El nombre del cargo ya está en uso en esta sede';
        ROLLBACK;
        LEAVE proc;
    END IF;

    UPDATE cargos
    SET
        nombre_cargo = p_nombre_cargo,
        descripcion = p_descripcion,
        salario_base = p_salario_base,
        estado = p_estado,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    COMMIT;
    SET p_mensaje = 'Cargo actualizado correctamente';
END $$

-- 5. PROCEDURE PARA ELIMINAR CARGO LÓGICAMENTE
DROP PROCEDURE IF EXISTS delete_cargo $$
CREATE PROCEDURE delete_cargo (
    IN p_id INT,
    IN p_sede_id INT,
    IN p_modificado_por VARCHAR(50),
    OUT p_mensaje VARCHAR(255)
)
proc: BEGIN
    DECLARE v_empleados_tabla INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        ROLLBACK;
        SET p_mensaje = 'Error al eliminar el cargo';
    END;

    START TRANSACTION;

    IF NOT EXISTS (
        SELECT 1
        FROM cargos
        WHERE id = p_id
          AND sede_id = p_sede_id
          AND estado = 1
    ) THEN
        SET p_mensaje = 'El cargo no existe en esta sede o ya está inactivo.';
        ROLLBACK;
        LEAVE proc;
    END IF;

    SELECT COUNT(*) INTO v_empleados_tabla
    FROM information_schema.tables
    WHERE table_schema = DATABASE()
      AND table_name = 'empleados';

    IF v_empleados_tabla > 0 THEN
        IF EXISTS (
            SELECT 1
            FROM empleados
            WHERE cargo_id = p_id
              AND sede_id = p_sede_id
              AND estado = 1
        ) THEN
            SET p_mensaje = 'No se puede eliminar el cargo porque tiene empleados asignados.';
            ROLLBACK;
            LEAVE proc;
        END IF;
    END IF;

    UPDATE cargos
    SET
        estado = 0,
        modificado_por = p_modificado_por,
        fecha_modificacion = NOW()
    WHERE id = p_id
      AND sede_id = p_sede_id;

    COMMIT;
    SET p_mensaje = 'Cargo eliminado correctamente.';
END $$

DELIMITER ;

