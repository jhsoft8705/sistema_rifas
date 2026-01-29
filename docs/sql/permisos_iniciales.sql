-- =============================================
-- PERMISOS INICIALES PARA EL SISTEMA
-- Ejecutar después de crear las sedes
-- =============================================

-- Agregar permisos faltantes para el sistema completo
INSERT INTO permisos (sede_id, nombre, descripcion, modulo, accion, creado_por)
SELECT id, 'DASHBOARD_VER', 'Ver dashboard', 'DASHBOARD', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'ORGANIZACION_VER', 'Ver organización', 'ORGANIZACION', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'USUARIOS_VER', 'Ver usuarios', 'USUARIOS', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'USUARIOS_CREAR', 'Crear usuarios', 'USUARIOS', 'CREAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'USUARIOS_EDITAR', 'Editar usuarios', 'USUARIOS', 'ACTUALIZAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'USUARIOS_ELIMINAR', 'Eliminar usuarios', 'USUARIOS', 'ELIMINAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'ROLES_VER', 'Ver roles', 'ROLES', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'ROLES_CREAR', 'Crear roles', 'ROLES', 'CREAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'ROLES_EDITAR', 'Editar roles', 'ROLES', 'ACTUALIZAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'ROLES_ELIMINAR', 'Eliminar roles', 'ROLES', 'ELIMINAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PERMISOS_VER', 'Ver permisos', 'PERMISOS', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PERMISOS_CREAR', 'Crear permisos', 'PERMISOS', 'CREAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PERMISOS_EDITAR', 'Editar permisos', 'PERMISOS', 'ACTUALIZAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PERMISOS_ELIMINAR', 'Eliminar permisos', 'PERMISOS', 'ELIMINAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'CATEGORIAS_VER', 'Ver categorías', 'CATEGORIAS', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'CATEGORIAS_CREAR', 'Crear categorías', 'CATEGORIAS', 'CREAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'CATEGORIAS_EDITAR', 'Editar categorías', 'CATEGORIAS', 'ACTUALIZAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'CATEGORIAS_ELIMINAR', 'Eliminar categorías', 'CATEGORIAS', 'ELIMINAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PREMIOS_VER', 'Ver premios', 'PREMIOS', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PREMIOS_ELIMINAR', 'Eliminar premios', 'PREMIOS', 'ELIMINAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'RIFAS_VER', 'Ver rifas', 'RIFAS', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'TICKETS_VER', 'Ver tickets', 'TICKETS', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'TICKETS_CREAR', 'Crear tickets', 'TICKETS', 'CREAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'TICKETS_EDITAR', 'Editar tickets', 'TICKETS', 'ACTUALIZAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PERSONAS_VER', 'Ver personas', 'PERSONAS', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PERSONAS_CREAR', 'Crear personas', 'PERSONAS', 'CREAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'PERSONAS_EDITAR', 'Editar personas', 'PERSONAS', 'ACTUALIZAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'VENTAS_VER', 'Ver ventas', 'VENTAS', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'VENTAS_CREAR', 'Crear ventas', 'VENTAS', 'CREAR', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'JUEGOS_VER', 'Ver juegos', 'JUEGOS', 'LEER', 'SYSTEM' FROM sedes
UNION ALL
SELECT id, 'JUEGOS_CREAR', 'Crear juegos', 'JUEGOS', 'CREAR', 'SYSTEM' FROM sedes
ON DUPLICATE KEY UPDATE 
    descripcion = VALUES(descripcion),
    modulo = VALUES(modulo),
    accion = VALUES(accion);

-- Asignar todos los permisos al rol SUPERADMIN
INSERT INTO rol_permisos (sede_id, rol_id, permiso_id, estado, asignado_por, fecha_asignacion, fecha_creacion, fecha_modificacion)
SELECT 
    p.sede_id,
    r.id AS rol_id,
    p.id AS permiso_id,
    1 AS estado,
    'SYSTEM' AS asignado_por,
    NOW() AS fecha_asignacion,
    NOW() AS fecha_creacion,
    NOW() AS fecha_modificacion
FROM permisos p
CROSS JOIN roles r
WHERE r.nombre = 'SUPERADMIN'
  AND p.estado = 1
ON DUPLICATE KEY UPDATE 
    estado = 1,
    fecha_modificacion = NOW();

-- Asignar permisos básicos al rol ADMIN
INSERT INTO rol_permisos (sede_id, rol_id, permiso_id, estado, asignado_por, fecha_asignacion, fecha_creacion, fecha_modificacion)
SELECT 
    p.sede_id,
    r.id AS rol_id,
    p.id AS permiso_id,
    1 AS estado,
    'SYSTEM' AS asignado_por,
    NOW() AS fecha_asignacion,
    NOW() AS fecha_creacion,
    NOW() AS fecha_modificacion
FROM permisos p
CROSS JOIN roles r
WHERE r.nombre = 'ADMIN'
  AND p.estado = 1
  AND p.nombre IN (
    'DASHBOARD_VER',
    'ORGANIZACION_VER',
    'USUARIOS_VER',
    'USUARIOS_CREAR',
    'USUARIOS_EDITAR',
    'ROLES_VER',
    'ROLES_CREAR',
    'ROLES_EDITAR',
    'PERMISOS_VER',
    'CATEGORIAS_VER',
    'CATEGORIAS_CREAR',
    'CATEGORIAS_EDITAR',
    'PREMIOS_VER',
    'PREMIOS_CREAR',
    'PREMIOS_EDITAR',
    'RIFAS_VER',
    'RIFAS_CREAR',
    'RIFAS_EDITAR',
    'TICKETS_VER',
    'TICKETS_CREAR',
    'TICKETS_EDITAR',
    'PERSONAS_VER',
    'PERSONAS_CREAR',
    'PERSONAS_EDITAR',
    'VENTAS_VER',
    'VENTAS_CREAR',
    'REPORTES_VER'
  )
ON DUPLICATE KEY UPDATE 
    estado = 1,
    fecha_modificacion = NOW();
