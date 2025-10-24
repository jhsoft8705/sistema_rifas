-- =====================================================
-- STORED PROCEDURES PARA GESTIÓN DE MARCACIONES
-- Base de datos: db_control_asistencia_testing
-- =====================================================

-- =====================================================
-- 1. SP: OBTENER MARCACIONES POR SEDE Y RANGO DE FECHAS
-- =====================================================
-- Descripción: Obtiene las marcaciones de empleados con filtros por sede y fechas
-- Parámetros:
--   @sede_id: ID de la sede (obligatorio)
--   @fecha_inicio: Fecha inicial del rango (opcional)
--   @fecha_fin: Fecha final del rango (opcional)
--   @empleado_id: ID del empleado específico (opcional)
-- =====================================================
 
DROP PROCEDURE IF EXISTS sp_obtener_marcaciones;
GO
CREATE PROCEDURE sp_obtener_marcaciones
    @sede_id INT,
    @fecha_inicio DATE = NULL,
    @fecha_fin DATE = NULL,
    @empleado_id INT = NULL
AS
BEGIN
    SET NOCOUNT ON;
    
    -- Validar parámetro obligatorio
    IF @sede_id IS NULL
    BEGIN
        RAISERROR('El parámetro @sede_id es obligatorio', 16, 1);
        RETURN;
    END
    
    -- Establecer fechas por defecto si no se proporcionan
    IF @fecha_inicio IS NULL
        SET @fecha_inicio = DATEADD(DAY, -30, GETDATE()); -- Últimos 30 días por defecto
    
    IF @fecha_fin IS NULL
        SET @fecha_fin = GETDATE();
    
    -- Consulta principal
    SELECT 
        m.id AS marcacion_id,
        m.sede_id,
        s.nombre as nombre_sede,
        m.empleado_id,
        e.nombre + ' ' + e.apellido_materno + ' ' + e.apellido_paterno AS nombre_completo,
        e.nro_documento,
        e.email,
        c.nombre_cargo,
        m.fecha_marcacion,
        m.hora_entrada,
        m.hora_salida,
        m.hora_entrada_refrigerio,
        m.hora_salida_refrigerio,
        m.minutos_refrigerio_extendido,
        m.minutos_tardanza,
        m.minutos_anticipo_salida,
        m.estado_asistencia,
        m.tipo_marcacion,
        m.observaciones,
        m.fuente,
        m.reconciliado,
        m.reconciliado_por,
        m.reconciliado_en,
        m.fecha_creacion,
        m.fecha_modificacion,
        -- Datos del mapeo biométrico
        eb.biometric_user_id,
        eb.device_id,
        -- Calcular horas trabajadas
        CASE 
            WHEN m.hora_entrada IS NOT NULL AND m.hora_salida IS NOT NULL THEN
                DATEDIFF(MINUTE, m.hora_entrada, m.hora_salida) - ISNULL(m.minutos_refrigerio_extendido, 0)
            ELSE NULL
        END AS minutos_trabajados
    FROM marcaciones m
    INNER JOIN empleados e ON m.empleado_id = e.id
    INNER JOIN sedes s ON m.sede_id = s.id
    LEFT JOIN cargos c ON e.cargo_id = c.id
    LEFT JOIN empleado_biometrico eb ON e.id = eb.empleado_id AND eb.activo = 1
    WHERE 
        m.sede_id = @sede_id
        AND m.fecha_marcacion BETWEEN @fecha_inicio AND @fecha_fin
        AND (@empleado_id IS NULL OR m.empleado_id = @empleado_id)
        AND e.estado = 1
    ORDER BY 
        m.fecha_marcacion DESC,
        e.apellido_paterno ASC,
        e.nombre ASC;
    
END
GO
-- =====================================================
-- 2. SP: OBTENER RESUMEN DE ASISTENCIA POR EMPLEADO
-- =====================================================
-- Descripción: Genera un resumen estadístico de asistencia por empleado
-- =====================================================

DROP PROCEDURE IF EXISTS sp_resumen_asistencia_empleado;
GO
CREATE PROCEDURE sp_resumen_asistencia_empleado
    @sede_id INT,
    @fecha_inicio DATE,
    @fecha_fin DATE,
    @empleado_id INT = NULL
AS
BEGIN
    SET NOCOUNT ON;
    
    SELECT 
        e.id AS empleado_id,
        e.nombres + ' ' + e.apellidos AS nombre_completo,
        e.numero_documento,
        c.nombre_cargo,
        COUNT(m.id) AS total_marcaciones,
        SUM(CASE WHEN m.estado_asistencia = 'Presente' THEN 1 ELSE 0 END) AS dias_presente,
        SUM(CASE WHEN m.estado_asistencia = 'Tardanza' THEN 1 ELSE 0 END) AS dias_tardanza,
        SUM(CASE WHEN m.estado_asistencia = 'Falta' THEN 1 ELSE 0 END) AS dias_falta,
        SUM(CASE WHEN m.estado_asistencia = 'Justificado' THEN 1 ELSE 0 END) AS dias_justificado,
        SUM(ISNULL(m.minutos_tardanza, 0)) AS total_minutos_tardanza,
        AVG(
            CASE 
                WHEN m.hora_entrada IS NOT NULL AND m.hora_salida IS NOT NULL 
                THEN DATEDIFF(MINUTE, m.hora_entrada, m.hora_salida) 
                ELSE NULL 
            END
        ) AS promedio_minutos_trabajados
    FROM empleados e
    LEFT JOIN marcaciones m ON e.id = m.empleado_id 
        AND m.sede_id = @sede_id
        AND m.fecha_marcacion BETWEEN @fecha_inicio AND @fecha_fin
    LEFT JOIN cargos c ON e.cargo_id = c.id
    WHERE 
        e.sede_id = @sede_id
        AND e.activo = 1
        AND (@empleado_id IS NULL OR e.id = @empleado_id)
    GROUP BY 
        e.id,
        e.nombres,
        e.apellidos,
        e.numero_documento,
        c.nombre_cargo
    ORDER BY 
        e.apellido_paterno ASC,
        e.nombre ASC;
END
GO




















-- =====================================================
-- 3. SP: CALCULAR ESTADOS DE ASISTENCIA AUTOMATICAMENTE NO IMPLEMENTADO
-- =====================================================
-- Descripción: 
--   Calcula automáticamente los estados de asistencia (Presente, Tardanza, Falta)
--   y las métricas relacionadas (minutos de tardanza, anticipo salida, etc.)
--   basándose en los horarios configurados para cada empleado.
--
-- Parámetros:
--   @fecha_proceso: Fecha específica a procesar (NULL = HOY)
--   @fecha_inicio: Fecha inicial del rango (NULL = @fecha_proceso)
--   @fecha_fin: Fecha final del rango (NULL = @fecha_proceso)
--   @empleado_id: ID del empleado específico (NULL = TODOS)
--   @sede_id: ID de la sede (NULL = TODAS)
--   @solo_no_reconciliados: Si es 1, solo procesa registros no reconciliados
--
-- Uso:
--   -- Procesar el día de hoy para todos
--   EXEC sp_calcular_estados_asistencia;
--   
--   -- Procesar una fecha específica
--   EXEC sp_calcular_estados_asistencia @fecha_proceso = '2025-10-13';
--   
--   -- Procesar un rango de fechas
--   EXEC sp_calcular_estados_asistencia 
--       @fecha_inicio = '2025-10-01', 
--       @fecha_fin = '2025-10-31';
--   
--   -- Procesar un empleado específico
--   EXEC sp_calcular_estados_asistencia 
--       @fecha_proceso = '2025-10-13',
--       @empleado_id = 5;
--
-- Lógica de cálculo:
--   1. FALTA: No tiene hora_entrada en el día
--   2. TARDANZA: hora_entrada > (hora_inicio_turno + tolerancia_entrada)
--   3. PRESENTE: hora_entrada <= (hora_inicio_turno + tolerancia_entrada)
--   4. JUSTIFICADO: Se mantiene si ya estaba marcado como tal (manual)
--
-- Nota: Este SP NO modifica registros que ya están reconciliados (reconciliado = 1)
--       a menos que se especifique @solo_no_reconciliados = 0
-- =====================================================

DROP PROCEDURE IF EXISTS sp_calcular_estados_asistencia;
GO
CREATE PROCEDURE sp_calcular_estados_asistencia
    @fecha_proceso DATE = NULL,
    @fecha_inicio DATE = NULL,
    @fecha_fin DATE = NULL,
    @empleado_id INT = NULL,
    @sede_id INT = NULL,
    @solo_no_reconciliados BIT = 1
AS
BEGIN
    SET NOCOUNT ON;
    
    -- =====================================================
    -- PASO 1: VALIDAR Y ESTABLECER FECHAS
    -- =====================================================
    
    -- Si no se especifica ninguna fecha, procesar HOY
    IF @fecha_proceso IS NULL AND @fecha_inicio IS NULL AND @fecha_fin IS NULL
    BEGIN
        SET @fecha_proceso = CAST(GETDATE() AS DATE);
    END
    
    -- Si solo se especifica fecha_proceso, usarla como rango de un día
    IF @fecha_proceso IS NOT NULL AND @fecha_inicio IS NULL
    BEGIN
        SET @fecha_inicio = @fecha_proceso;
        SET @fecha_fin = @fecha_proceso;
    END
    
    -- Si solo se especifica fecha_inicio, poner fecha_fin = fecha_inicio
    IF @fecha_inicio IS NOT NULL AND @fecha_fin IS NULL
    BEGIN
        SET @fecha_fin = @fecha_inicio;
    END
    
    -- Validar que fecha_fin >= fecha_inicio
    IF @fecha_fin < @fecha_inicio
    BEGIN
        RAISERROR('La fecha_fin no puede ser menor que fecha_inicio', 16, 1);
        RETURN;
    END
    
    -- =====================================================
    -- PASO 2: MOSTRAR INFORMACIÓN DEL PROCESO
    -- =====================================================
    
    PRINT '╔══════════════════════════════════════════════════════════╗';
    PRINT '║   CALCULANDO ESTADOS DE ASISTENCIA                       ║';
    PRINT '╚══════════════════════════════════════════════════════════╝';
    PRINT '';
    PRINT 'Rango de fechas: ' + CONVERT(VARCHAR(10), @fecha_inicio, 120) + ' al ' + CONVERT(VARCHAR(10), @fecha_fin, 120);
    PRINT 'Empleado: ' + ISNULL(CAST(@empleado_id AS VARCHAR(10)), 'TODOS');
    PRINT 'Sede: ' + ISNULL(CAST(@sede_id AS VARCHAR(10)), 'TODAS');
    PRINT 'Solo no reconciliados: ' + CASE WHEN @solo_no_reconciliados = 1 THEN 'SÍ' ELSE 'NO' END;
    PRINT '';
    
    -- =====================================================
    -- PASO 3: ACTUALIZAR MARCACIONES CON CÁLCULOS
    -- =====================================================
    
    DECLARE @registros_actualizados INT = 0;
    DECLARE @registros_con_error INT = 0;
    
    BEGIN TRY
        
        UPDATE m
        SET 
            -- =====================================================
            -- CALCULAR MINUTOS DE TARDANZA
            -- =====================================================
            -- Si la hora de entrada es mayor que (hora_inicio + tolerancia)
            -- entonces hay tardanza
            minutos_tardanza = CASE
                WHEN m.hora_entrada IS NOT NULL 
                 AND hs.hora_inicio IS NOT NULL
                 AND m.hora_entrada > DATEADD(MINUTE, ISNULL(ce.tolerancia_entrada, 0), hs.hora_inicio)
                THEN DATEDIFF(MINUTE, 
                    DATEADD(MINUTE, ISNULL(ce.tolerancia_entrada, 0), hs.hora_inicio),
                    m.hora_entrada)
                ELSE 0
            END,
            
            -- =====================================================
            -- CALCULAR MINUTOS DE ANTICIPO DE SALIDA
            -- =====================================================
            -- Si la hora de salida es menor que (hora_fin - tolerancia)
            -- entonces hay anticipo de salida
            minutos_anticipo_salida = CASE
                WHEN m.hora_salida IS NOT NULL 
                 AND hs.hora_fin IS NOT NULL
                 AND m.hora_salida < DATEADD(MINUTE, -ISNULL(cs.tolerancia_salida, 0), hs.hora_fin)
                THEN DATEDIFF(MINUTE, 
                    m.hora_salida,
                    DATEADD(MINUTE, -ISNULL(cs.tolerancia_salida, 0), hs.hora_fin))
                ELSE 0
            END,
            
            -- =====================================================
            -- CALCULAR MINUTOS DE REFRIGERIO EXTENDIDO
            -- =====================================================
            -- Si tiene salida y entrada de refrigerio, calcular la diferencia
            -- y comparar con la duración permitida + tolerancia
            minutos_refrigerio_extendido = CASE
                WHEN m.hora_salida_refrigerio IS NOT NULL 
                 AND m.hora_entrada_refrigerio IS NOT NULL
                 AND cr.duracion_refrigerio IS NOT NULL
                THEN CASE
                    WHEN DATEDIFF(MINUTE, m.hora_salida_refrigerio, m.hora_entrada_refrigerio) > 
                         (cr.duracion_refrigerio + ISNULL(cr.tolerancia_retorno, 0))
                    THEN DATEDIFF(MINUTE, m.hora_salida_refrigerio, m.hora_entrada_refrigerio) 
                         - (cr.duracion_refrigerio + ISNULL(cr.tolerancia_retorno, 0))
                    ELSE 0
                END
                ELSE 0
            END,
            
            -- =====================================================
            -- GUARDAR TOLERANCIAS APLICADAS (AUDITORÍA)
            -- =====================================================
            minutos_tolerancia_entrada = ISNULL(ce.tolerancia_entrada, 0),
            minutos_tolerancia_salida = ISNULL(cs.tolerancia_salida, 0),
            
            -- =====================================================
            -- CALCULAR ESTADO DE ASISTENCIA
            -- =====================================================
            estado_asistencia = CASE
                -- Mantener JUSTIFICADO si ya estaba marcado manualmente
                WHEN m.estado_asistencia = 'Justificado' THEN 'Justificado'
                
                -- FALTA: No tiene hora de entrada
                WHEN m.hora_entrada IS NULL THEN 'Falta'
                
                -- TARDANZA: Llegó después de hora_inicio + tolerancia
                WHEN hs.hora_inicio IS NOT NULL
                 AND m.hora_entrada > DATEADD(MINUTE, ISNULL(ce.tolerancia_entrada, 0), hs.hora_inicio)
                THEN 'Tardanza'
                
                -- PRESENTE: Llegó a tiempo
                ELSE 'Presente'
            END,
            
            -- Actualizar fecha de modificación
            fecha_modificacion = GETDATE(),
            modificado_por = 'SP_CALCULO_AUTO'
            
        FROM marcaciones m
        
        -- =====================================================
        -- JOIN CON EMPLEADO Y SU CONFIGURACIÓN
        -- =====================================================
        INNER JOIN empleados e ON m.empleado_id = e.id
        
        -- =====================================================
        -- JOIN CON HORARIO DEL DÍA DE LA SEMANA
        -- =====================================================
        -- Obtener el horario configurado para el día de la semana de la marcación
        LEFT JOIN horario_semanal hs ON e.turno_id = hs.turno_id 
            AND m.sede_id = hs.sede_id
            AND hs.dia_semana = CASE DATEPART(WEEKDAY, m.fecha_marcacion)
                WHEN 1 THEN 'Domingo'
                WHEN 2 THEN 'Lunes'
                WHEN 3 THEN 'Martes'
                WHEN 4 THEN 'Miercoles'
                WHEN 5 THEN 'Jueves'
                WHEN 6 THEN 'Viernes'
                WHEN 7 THEN 'Sabado'
            END
            AND hs.estado = 1
            AND hs.es_laborable = 1
        
        -- =====================================================
        -- JOIN CON CONFIGURACIONES DE TOLERANCIA
        -- =====================================================
        LEFT JOIN configuracion_entrada ce 
            ON hs.horario_id = ce.horario_id 
            AND m.sede_id = ce.sede_id
            AND ce.estado = 1
        
        LEFT JOIN configuracion_salida cs 
            ON hs.horario_id = cs.horario_id 
            AND m.sede_id = cs.sede_id
            AND cs.estado = 1
        
        LEFT JOIN configuracion_refrigerio cr 
            ON hs.horario_id = cr.horario_id 
            AND m.sede_id = cr.sede_id
            AND cr.estado = 1
        
        -- =====================================================
        -- FILTROS WHERE
        -- =====================================================
        WHERE 
            -- Rango de fechas
            m.fecha_marcacion BETWEEN @fecha_inicio AND @fecha_fin
            
            -- Filtro por empleado (opcional)
            AND (@empleado_id IS NULL OR m.empleado_id = @empleado_id)
            
            -- Filtro por sede (opcional)
            AND (@sede_id IS NULL OR m.sede_id = @sede_id)
            
            -- Filtro por reconciliación
            AND (@solo_no_reconciliados = 0 OR m.reconciliado = 0)
            
            -- Solo empleados activos
            AND e.estado = 1;
        
        -- Obtener cantidad de registros actualizados
        SET @registros_actualizados = @@ROWCOUNT;
        
    END TRY
    BEGIN CATCH
        -- Si hay error, registrarlo
        SET @registros_con_error = 1;
        
        PRINT '';
        PRINT '❌ ERROR AL CALCULAR ESTADOS:';
        PRINT 'Mensaje: ' + ERROR_MESSAGE();
        PRINT 'Línea: ' + CAST(ERROR_LINE() AS VARCHAR(10));
        PRINT 'Procedimiento: ' + ISNULL(ERROR_PROCEDURE(), 'N/A');
        
        -- Propagar el error
        THROW;
    END CATCH
    
    -- =====================================================
    -- PASO 4: MOSTRAR RESUMEN DE RESULTADOS
    -- =====================================================
    
    IF @registros_con_error = 0
    BEGIN
        PRINT '';
        PRINT '╔══════════════════════════════════════════════════════════╗';
        PRINT '║   RESULTADO DEL PROCESO                                  ║';
        PRINT '╚══════════════════════════════════════════════════════════╝';
        PRINT '';
        PRINT '✅ Registros actualizados: ' + CAST(@registros_actualizados AS VARCHAR(10));
        PRINT '';
        
        -- Mostrar distribución de estados
        PRINT '📊 Distribución de estados:';
        SELECT 
            estado_asistencia AS Estado,
            COUNT(*) AS Cantidad,
            CAST(COUNT(*) * 100.0 / @registros_actualizados AS DECIMAL(5,2)) AS Porcentaje
        FROM marcaciones
        WHERE fecha_marcacion BETWEEN @fecha_inicio AND @fecha_fin
            AND (@empleado_id IS NULL OR empleado_id = @empleado_id)
            AND (@sede_id IS NULL OR sede_id = @sede_id)
        GROUP BY estado_asistencia
        ORDER BY COUNT(*) DESC;
        
        PRINT '';
        PRINT '✅ PROCESO COMPLETADO EXITOSAMENTE';
        PRINT '';
    END
    
END
GO

-- =====================================================
-- 4. SP: JOB AUTOMÁTICO - CALCULAR ASISTENCIA DEL DÍA NO IMPLEMENTADO
-- =====================================================
-- Descripción: 
--   Procedimiento simplificado para ejecutar desde SQL Server Agent
--   o desde un cron job. Procesa el día actual.
--
-- Uso:
--   -- Crear un Job de SQL Server que ejecute cada 30 minutos:
--   EXEC sp_job_calcular_asistencia_diaria;
-- =====================================================

DROP PROCEDURE IF EXISTS sp_job_calcular_asistencia_diaria;
GO
CREATE PROCEDURE sp_job_calcular_asistencia_diaria
AS
BEGIN
    SET NOCOUNT ON;
    
    DECLARE @fecha_hoy DATE = CAST(GETDATE() AS DATE);
    
    PRINT '🤖 JOB AUTOMÁTICO: Calculando asistencias del día ' + CONVERT(VARCHAR(10), @fecha_hoy, 120);
    
    -- Ejecutar el cálculo solo para el día de hoy
    EXEC sp_calcular_estados_asistencia 
        @fecha_proceso = @fecha_hoy,
        @solo_no_reconciliados = 1;
    
END
GO
 