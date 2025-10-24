-- =====================================================
-- SCRIPT DE LIMPIEZA - TABLAS ANTIGUAS
-- =====================================================
-- ADVERTENCIA: Este script ELIMINARÁ todas las tablas
-- del antiguo sistema de asistencia y planilla.
-- 
-- Ejecutar SOLO cuando estés seguro de que no necesitas
-- los datos del sistema anterior.
--
-- RESPALDO: Haz backup antes de ejecutar este script
-- =====================================================

USE sistema_rifas;

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- ELIMINAR TABLA TEMPORAL DE REFERENCIA
-- =====================================================

-- Tabla cargos (era solo referencia temporal)
DROP TABLE IF EXISTS cargos;

COMMIT;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================

-- Listar todas las tablas restantes
SELECT 
    TABLE_NAME AS 'Tablas del Sistema de Rifas',
    TABLE_ROWS AS 'Registros'
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'sistema_rifas'
ORDER BY TABLE_NAME;

-- Verificar que solo existan las tablas del sistema de rifas
SELECT COUNT(*) AS 'Total de Tablas' 
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = 'sistema_rifas';

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- OPTIMIZAR TABLAS
-- =====================================================

-- Optimizar todas las tablas después de la limpieza
OPTIMIZE TABLE sedes;
OPTIMIZE TABLE usuarios;
OPTIMIZE TABLE roles;
OPTIMIZE TABLE permisos;
OPTIMIZE TABLE usuario_roles;
OPTIMIZE TABLE usuario_permisos;
OPTIMIZE TABLE rol_permisos;
OPTIMIZE TABLE sesiones;
OPTIMIZE TABLE intentos_acceso;
OPTIMIZE TABLE configuracion_sede;
OPTIMIZE TABLE ubicaciones_rifa;
OPTIMIZE TABLE estados_ticket;
OPTIMIZE TABLE metodos_pago;
OPTIMIZE TABLE categorias_premios;
OPTIMIZE TABLE premios;
OPTIMIZE TABLE rifas;
OPTIMIZE TABLE tickets;
OPTIMIZE TABLE comprobantes_pago;
OPTIMIZE TABLE participantes;
OPTIMIZE TABLE intentos_sorteo;
OPTIMIZE TABLE ganadores;
OPTIMIZE TABLE audit_logs;

-- =====================================================
-- RESULTADO
-- =====================================================

SELECT '✅ Limpieza completada exitosamente' AS Resultado;
SELECT '✅ Tablas antiguas eliminadas' AS Resultado;
SELECT '✅ Tablas optimizadas' AS Resultado;
SELECT '⚠️ Recuerda actualizar tu código si hacía referencia a la tabla cargos' AS Advertencia;

