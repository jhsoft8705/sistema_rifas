-- =============================================
-- STORED PROCEDURES PARA MANTENIMIENTO DE CARGOS
-- Sistema Multi-Sede
-- =============================================

-- 1. PROCEDURE PARA LISTAR CARGOS
DROP PROCEDURE IF EXISTS list_cargo;
GO
CREATE PROCEDURE list_cargo
    @sede_id INT
AS  
BEGIN  
    SET NOCOUNT ON;  
  
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
      AND c.sede_id = @sede_id
    ORDER BY c.fecha_creacion DESC;   
END;  
GO

-- 2. PROCEDURE PARA OBTENER CARGO POR ID
DROP PROCEDURE IF EXISTS list_cargos_by_id;
GO
CREATE PROCEDURE list_cargos_by_id
    @id INT,
    @sede_id INT
AS  
BEGIN  
    SET NOCOUNT ON;  
  
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
      AND c.id = @id
      AND c.sede_id = @sede_id;  
END;
GO

-- 3. PROCEDURE PARA REGISTRAR CARGO
DROP PROCEDURE IF EXISTS register_cargo;
GO
CREATE PROCEDURE register_cargo   
    @sede_id INT,
    @nombre_cargo NVARCHAR(100),    
    @descripcion NVARCHAR(255) NULL,    
    @salario_base DECIMAL(10,2) NULL,    
    @creado_por VARCHAR(50),    
    @mensaje NVARCHAR(255) OUTPUT    
AS    
BEGIN    
    SET NOCOUNT ON;    
    
    -- Validar que la sede exista
    IF NOT EXISTS (SELECT 1 FROM sedes WHERE id = @sede_id)
    BEGIN
        SET @mensaje = 'La sede no existe';
        RETURN;
    END
    
    -- Validar que el cargo no exista en esa sede    
    IF EXISTS (    
        SELECT 1    
        FROM cargos    
        WHERE nombre_cargo = @nombre_cargo AND sede_id = @sede_id AND estado = 1
    )    
    BEGIN    
        SET @mensaje = 'El cargo ya existe en esta sede';    
        RETURN;    
    END    
    
    -- Insertar cargo    
    INSERT INTO cargos (    
        sede_id,
        nombre_cargo,    
        descripcion,    
        salario_base,    
        estado,    
        creado_por,    
        fecha_creacion,
        fecha_modificacion
    )    
    VALUES (    
        @sede_id,
        @nombre_cargo,  
        @descripcion,    
        @salario_base,    
        1,    
        @creado_por,    
        GETDATE(),
        GETDATE()
    )    
    
    SET @mensaje = 'Cargo registrado correctamente';    
END; 
GO

-- 4. PROCEDURE PARA ACTUALIZAR CARGO
DROP PROCEDURE IF EXISTS update_cargo;
GO
CREATE PROCEDURE update_cargo      
    @id INT,
    @sede_id INT,      
    @nombre_cargo NVARCHAR(100),      
    @descripcion NVARCHAR(255) NULL,      
    @salario_base DECIMAL(10,2) NULL,    
    @estado INT,
    @modificado_por VARCHAR(50),      
    @mensaje NVARCHAR(255) OUTPUT      
AS      
BEGIN      
    SET NOCOUNT ON;      
      
    -- Validar que la sede exista
    IF NOT EXISTS (SELECT 1 FROM sedes WHERE id = @sede_id)
    BEGIN
        SET @mensaje = 'La sede no existe';
        RETURN;
    END

    -- Validar que el cargo exista y pertenezca a la sede      
    IF NOT EXISTS (      
        SELECT 1 FROM cargos WHERE id = @id AND sede_id = @sede_id
    )      
    BEGIN      
        SET @mensaje = 'El cargo no existe en esta sede';      
        RETURN;      
    END      
      
    -- Validar nombre duplicado por otro cargo en la misma sede      
    IF EXISTS (      
        SELECT 1 FROM cargos       
        WHERE nombre_cargo = @nombre_cargo 
          AND sede_id = @sede_id 
          AND id <> @id
          AND estado = 1
    )      
    BEGIN      
        SET @mensaje = 'El nombre del cargo ya está en uso en esta sede';      
        RETURN;      
    END      
      
    -- Actualizar cargo      
    UPDATE cargos      
    SET       
        nombre_cargo = @nombre_cargo,      
        descripcion = @descripcion,      
        salario_base = @salario_base,      
        estado = @estado,      
        modificado_por = @modificado_por,      
        fecha_modificacion = GETDATE()      
    WHERE id = @id AND sede_id = @sede_id;    
      
    SET @mensaje = 'Cargo actualizado correctamente';      
END;  
GO

-- 5. PROCEDURE PARA ELIMINAR CARGO LÓGICAMENTE
DROP PROCEDURE IF EXISTS delete_cargo;
GO
CREATE PROCEDURE delete_cargo
    @id INT,
    @sede_id INT,
    @modificado_por VARCHAR(50),
    @mensaje NVARCHAR(255) OUTPUT
AS  
BEGIN  
    SET NOCOUNT ON;  
  
    -- Validar que el cargo exista y pertenezca a la sede
    IF NOT EXISTS (  
        SELECT 1  
        FROM cargos 
        WHERE id = @id AND sede_id = @sede_id AND estado = 1
    )  
    BEGIN  
        SET @mensaje = 'El cargo no existe en esta sede o ya está inactivo.';
        RETURN;  
    END

    -- Validar si existen empleados con este cargo
    IF EXISTS (
        SELECT 1
        FROM empleados
        WHERE cargo_id = @id AND sede_id = @sede_id AND estado = 1
    )
    BEGIN
        SET @mensaje = 'No se puede eliminar el cargo porque tiene empleados asignados.';
        RETURN;
    END
  
    -- Eliminar lógicamente
    UPDATE cargos
    SET  
        estado = 0,  
        modificado_por = @modificado_por,  
        fecha_modificacion = GETDATE()  
    WHERE id = @id AND sede_id = @sede_id;  

    SET @mensaje = 'Cargo eliminado correctamente.';
END;  
GO
