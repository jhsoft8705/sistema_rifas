<?php
/**
 * PermisosHelper
 * Helper para verificar permisos de usuarios
 */
require_once(__DIR__ . '/AuthMiddleware.php');
require_once(__DIR__ . '/../models/Permiso.php');

class PermisosHelper
{
    /**
     * Verificar si el usuario autenticado tiene un permiso específico
     * @param string $permiso_nombre Nombre del permiso (ej: 'RIFAS_CREAR')
     * @return bool True si tiene el permiso, False si no
     */
    public static function tienePermiso(string $permiso_nombre): bool
    {
        try {
            $authData = AuthMiddleware::verificarAutenticacion(true);
            if (!$authData['ok']) {
                return false;
            }

            $usuario = $authData['data'];
            $usuario_id = (int) $usuario['usuario_id'];
            $sede_id = (int) $usuario['sede_id'];

            $permiso = new Permiso();
            return $permiso->verificar_permiso($usuario_id, $sede_id, $permiso_nombre);
        } catch (Exception $e) {
            error_log("Error en PermisosHelper::tienePermiso: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario tiene alguno de los permisos especificados
     * @param array $permisos Array de nombres de permisos
     * @return bool True si tiene al menos uno de los permisos
     */
    public static function tieneAlgunPermiso(array $permisos): bool
    {
        foreach ($permisos as $permiso) {
            if (self::tienePermiso($permiso)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Verificar si el usuario tiene todos los permisos especificados
     * @param array $permisos Array de nombres de permisos
     * @return bool True si tiene todos los permisos
     */
    public static function tieneTodosPermisos(array $permisos): bool
    {
        foreach ($permisos as $permiso) {
            if (!self::tienePermiso($permiso)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Obtener todos los permisos del usuario autenticado
     * @return array Array de permisos
     */
    public static function obtenerPermisosUsuario(): array
    {
        try {
            $authData = AuthMiddleware::verificarAutenticacion(true);
            if (!$authData['ok']) {
                return [];
            }

            $usuario = $authData['data'];
            $usuario_id = (int) $usuario['usuario_id'];
            $sede_id = (int) $usuario['sede_id'];

            $permiso = new Permiso();
            $resultado = $permiso->get_permisos_usuario($usuario_id, $sede_id);
            
            return $resultado['ok'] ? $resultado['data'] : [];
        } catch (Exception $e) {
            error_log("Error en PermisosHelper::obtenerPermisosUsuario: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verificar si el usuario tiene un rol específico
     * @param string $rol_nombre Nombre del rol (ej: 'SUPERADMIN', 'ADMIN')
     * @return bool True si tiene el rol
     */
    public static function tieneRol(string $rol_nombre): bool
    {
        try {
            $authData = AuthMiddleware::verificarAutenticacion(true);
            if (!$authData['ok']) {
                return false;
            }

            $usuario = $authData['data'];
            $rol_usuario = strtoupper($usuario['rol_nombre'] ?? '');
            $rol_buscado = strtoupper($rol_nombre);

            return $rol_usuario === $rol_buscado;
        } catch (Exception $e) {
            error_log("Error en PermisosHelper::tieneRol: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el usuario es SUPERADMIN
     * @return bool True si es SUPERADMIN
     */
    public static function esSuperAdmin(): bool
    {
        return self::tieneRol('SUPERADMIN');
    }
}
