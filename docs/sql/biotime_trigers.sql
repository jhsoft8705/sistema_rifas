-- =====================================================
-- TRIGGER SIMPLIFICADO - SINCRONIZACIÓN DIRECTA
-- Base de datos origen: biotime_testing
-- Base de datos destino: db_control_asistencia_testing
-- =====================================================
-- Este trigger escribe DIRECTAMENTE en la tabla marcaciones
-- SIN necesidad de procesador intermedio ni logs
-- =====================================================
/* USE biotime_testing;

GO */
-- =====================================================
-- 1. (DEBUGGING)
-- BD: biotime_testing
-- =====================================================
/* CREATE TABLE errores_trigger (
        id INT IDENTITY (1, 1) PRIMARY KEY,
        trigger_name VARCHAR(200),
        error_message NVARCHAR (4000),
        error_number INT,
        error_severity INT,
        error_state INT,
        error_line INT,
        emp_code VARCHAR(100),
        punch_time DATETIME,
        punch_state INT,
        fecha_error DATETIME DEFAULT GETDATE ()
    );
 */
-- =====================================================
-- 2. CREAR O RECREAR EL TRIGGER
/*    MAPEO DE ESTADOS BIOMÉTRICOS A CAMPOS

El trigger mapea los punch_state del biométrico a campos específicos:

NOTA: 
A partir de esas referencias, y de experiencia con dispositivos ZKTeco / soluciones similares, estos son los estados más comunes que maneja punch_state:
Valor numérico	Estado simbólico / nombre	Descripción esperada
0	Entrada / Check-In	El empleado marca al comenzar su jornada o al regresar después del descanso.
1	Salida / Check-Out	El empleado marca al terminar su jornada.
2	Break Out / Inicio de descanso	Inicia un periodo de descanso (almuerzo, descanso corto).
3	Break In / Fin de descanso	Vuelve del descanso para continuar la jornada.
4	Overtime In / Inicio de horas extras	Marca el comienzo de tiempo extra.
5	Overtime Out / Fin de horas extras	Marca el fin del periodo de horas extras.
8, 9 u otros	Otros estados / personalizados	Algunos dispositivos o configuraciones permiten estados adicionales (por ejemplo, “otro tipo de marcación”)


ESTADO GENERAL DEL DÍA (tipo_marcacion):
El campo 'tipo_marcacion' se calcula automáticamente según las marcaciones registradas:

| Estado             | Condición                                          |
|--------------------|----------------------------------------------------|
| INCOMPLETO         | Solo tiene entrada, o faltan marcaciones           |
| DIA_NORMAL         | Tiene entrada Y salida completas                   |
| DIA_CON_REFRIGERIO | Tiene entrada, salida Y refrigerio                 |
| DIA_CON_HE         | Tiene entrada, salida Y horas extras               |
| DIA_COMPLETO_HE    | Tiene TODO: entrada, salida, refrigerio Y HE       |

El estado se actualiza dinámicamente con cada nueva marcación del empleado en el día.
*/

 
---
-- =====================================================
DROP TRIGGER IF EXISTS trg_sync_marcacion_directa;
GO 
CREATE TRIGGER trg_sync_marcacion_directa ON iclock_transaction AFTER INSERT AS BEGIN
-- CRÍTICO: SET XACT_ABORT OFF para que errores no reviertan la transacción principal
SET
    XACT_ABORT OFF;

SET
    NOCOUNT ON;
-- Todo dentro de TRY-CATCH para NUNCA bloquear las inserciones en biotime
BEGIN TRY
-- Verificar que hay datos para procesar
IF NOT EXISTS (
    SELECT 1 FROM inserted
) RETURN;
-- Sincronizar marcaciones solo de empleados mapeados
MERGE db_control_asistencia_testing.cafedasistencia2025.marcaciones AS target USING (
    SELECT
        eb.empleado_id,
        eb.sede_id,
        CAST(i.punch_time AS DATE) AS fecha_marcacion,
        CAST(i.punch_time AS TIME) AS hora_marcacion,
        i.punch_state,
        i.emp_code
    FROM
        inserted i
        INNER JOIN db_control_asistencia_testing.cafedasistencia2025.empleado_biometrico eb ON CAST(eb.biometric_user_id AS VARCHAR(100)) = CAST(i.emp_code AS VARCHAR(100))
        AND eb.activo = 1
) AS source ON (
    target.empleado_id = source.empleado_id
    AND target.fecha_marcacion = source.fecha_marcacion
    AND target.sede_id = source.sede_id
)
-- ACTUALIZAR marcación existente
WHEN MATCHED THEN
UPDATE
SET
    hora_entrada = CASE
        -- LÓGICA HÍBRIDA: Si punch_state = 0 y NO hay hora_entrada, es ENTRADA
        WHEN source.punch_state = 0 AND target.hora_entrada IS NULL THEN source.hora_marcacion
        -- Si punch_state = 0 y YA hay hora_entrada, mantener la primera (más temprana)
        WHEN source.punch_state = 0 AND target.hora_entrada IS NOT NULL THEN target.hora_entrada
        ELSE target.hora_entrada
    END,
    hora_salida = CASE
        -- LÓGICA HÍBRIDA: Si punch_state = 0 y YA hay hora_entrada, es SALIDA
        WHEN source.punch_state = 0 AND target.hora_entrada IS NOT NULL THEN source.hora_marcacion
        -- Si punch_state = 1, es SALIDA explícita
        WHEN source.punch_state = 1 THEN source.hora_marcacion
        ELSE target.hora_salida
    END,
    hora_salida_refrigerio = CASE
        WHEN source.punch_state = 2 THEN source.hora_marcacion
        ELSE target.hora_salida_refrigerio
    END,
    hora_entrada_refrigerio = CASE
        WHEN source.punch_state = 3 THEN source.hora_marcacion
        ELSE target.hora_entrada_refrigerio
    END,
    hora_entrada_he = CASE
        WHEN source.punch_state = 4 THEN source.hora_marcacion
        ELSE target.hora_entrada_he
    END,
    hora_salida_he = CASE
        WHEN source.punch_state = 5 THEN source.hora_marcacion
        ELSE target.hora_salida_he
    END,
    tipo_marcacion = CASE
        -- DIA_COMPLETO_HE: tiene entrada, salida, refrigerio y HE completo
        WHEN (CASE WHEN source.punch_state = 0 AND target.hora_entrada IS NULL THEN source.hora_marcacion ELSE target.hora_entrada END) IS NOT NULL
         AND (CASE WHEN source.punch_state = 0 AND target.hora_entrada IS NOT NULL THEN source.hora_marcacion WHEN source.punch_state = 1 THEN source.hora_marcacion ELSE target.hora_salida END) IS NOT NULL
         AND (CASE WHEN source.punch_state = 3 THEN source.hora_marcacion ELSE target.hora_entrada_refrigerio END) IS NOT NULL
         AND (CASE WHEN source.punch_state = 2 THEN source.hora_marcacion ELSE target.hora_salida_refrigerio END) IS NOT NULL
         AND (CASE WHEN source.punch_state = 4 THEN source.hora_marcacion ELSE target.hora_entrada_he END) IS NOT NULL
         AND (CASE WHEN source.punch_state = 5 THEN source.hora_marcacion ELSE target.hora_salida_he END) IS NOT NULL
        THEN 'DIA_COMPLETO_HE'
        
        -- DIA_CON_HE: tiene entrada, salida y al menos una HE
        WHEN (CASE WHEN source.punch_state = 0 AND target.hora_entrada IS NULL THEN source.hora_marcacion ELSE target.hora_entrada END) IS NOT NULL
         AND (CASE WHEN source.punch_state = 0 AND target.hora_entrada IS NOT NULL THEN source.hora_marcacion WHEN source.punch_state = 1 THEN source.hora_marcacion ELSE target.hora_salida END) IS NOT NULL
         AND ((CASE WHEN source.punch_state = 4 THEN source.hora_marcacion ELSE target.hora_entrada_he END) IS NOT NULL
          OR (CASE WHEN source.punch_state = 5 THEN source.hora_marcacion ELSE target.hora_salida_he END) IS NOT NULL)
        THEN 'DIA_CON_HE'
        
        -- DIA_CON_REFRIGERIO: tiene entrada, salida y refrigerio
        WHEN (CASE WHEN source.punch_state = 0 AND target.hora_entrada IS NULL THEN source.hora_marcacion ELSE target.hora_entrada END) IS NOT NULL
         AND (CASE WHEN source.punch_state = 0 AND target.hora_entrada IS NOT NULL THEN source.hora_marcacion WHEN source.punch_state = 1 THEN source.hora_marcacion ELSE target.hora_salida END) IS NOT NULL
         AND ((CASE WHEN source.punch_state = 3 THEN source.hora_marcacion ELSE target.hora_entrada_refrigerio END) IS NOT NULL
          OR (CASE WHEN source.punch_state = 2 THEN source.hora_marcacion ELSE target.hora_salida_refrigerio END) IS NOT NULL)
        THEN 'DIA_CON_REFRIGERIO'
        
        -- DIA_NORMAL: tiene entrada y salida
        WHEN (CASE WHEN source.punch_state = 0 AND target.hora_entrada IS NULL THEN source.hora_marcacion ELSE target.hora_entrada END) IS NOT NULL
         AND (CASE WHEN source.punch_state = 0 AND target.hora_entrada IS NOT NULL THEN source.hora_marcacion WHEN source.punch_state = 1 THEN source.hora_marcacion ELSE target.hora_salida END) IS NOT NULL
        THEN 'DIA_NORMAL'
        
        -- INCOMPLETO: cualquier otro caso
        ELSE 'INCOMPLETO'
    END,
    fecha_modificacion = GETDATE (),
    modificado_por = 'BIOMETRICO_AUTO'
    -- INSERTAR nueva marcación
    WHEN NOT MATCHED BY TARGET THEN INSERT (
        sede_id,
        empleado_id,
        fecha_marcacion,
        tipo_marcacion,
        hora_entrada,
        hora_salida,
        hora_entrada_refrigerio,
        hora_salida_refrigerio,
        hora_entrada_he,
        hora_salida_he,
        estado_asistencia,
        minutos_tardanza,
        minutos_anticipo_salida,
        minutos_refrigerio_extendido,
        minutos_tolerancia_entrada,
        minutos_tolerancia_salida,
        fuente,
        reconciliado,
        creado_por
    )
VALUES
    (
        source.sede_id,
        source.empleado_id,
        source.fecha_marcacion,
        -- Al insertar siempre será INCOMPLETO porque es la primera marcación del día
        'INCOMPLETO',
        CASE
            WHEN source.punch_state = 0 THEN source.hora_marcacion
            ELSE NULL
        END,
        CASE
            WHEN source.punch_state = 1 THEN source.hora_marcacion
            ELSE NULL
        END,
        CASE
            WHEN source.punch_state = 3 THEN source.hora_marcacion
            ELSE NULL
        END,
        CASE
            WHEN source.punch_state = 2 THEN source.hora_marcacion
            ELSE NULL
        END,
        CASE
            WHEN source.punch_state = 4 THEN source.hora_marcacion
            ELSE NULL
        END,
        CASE
            WHEN source.punch_state = 5 THEN source.hora_marcacion
            ELSE NULL
        END,
        'Presente',
        0,
        0,
        0,
        0,
        0,
        'BIOMETRICO',
        0,
        'BIOMETRICO_AUTO'
    );

END TRY BEGIN CATCH
-- SI HAY ERROR: Registrar en tabla de errores pero NO bloquear biotime
-- Usamos otro TRY-CATCH para que incluso si falla el registro, continúe
BEGIN TRY
INSERT INTO
    db_control_asistencia_testing.cafedasistencia2025.errores_trigger (
        trigger_name,
        error_message,
        error_number,
        error_severity,
        error_state,
        error_line,
        emp_code,
        punch_time,
        punch_state
    )
SELECT
    'trg_sync_marcacion_directa',
    ERROR_MESSAGE (),
    ERROR_NUMBER (),
    ERROR_SEVERITY (),
    ERROR_STATE (),
    ERROR_LINE (),
    emp_code,
    punch_time,
    punch_state
FROM
    inserted;

END TRY BEGIN CATCH
-- Si incluso el registro de error falla, no hacer nada
-- Lo importante es que Biotime siga funcionando
END CATCH END CATCH END  
-- =====================================================


 