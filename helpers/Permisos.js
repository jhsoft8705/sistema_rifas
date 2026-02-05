/**
 * Permisos.js - Helper Global de Permisos
 * Maneja verificación de permisos en el frontend
 */

const Permisos = {
    /**
     * Obtener permisos del usuario desde localStorage
     */
    getPermisos() {
        const usuario = Auth.getUsuario();
        if (!usuario || !usuario.permisos) {
            return [];
        }
        return usuario.permisos;
    },

    /**
     * Obtener nombres de permisos como array
     */
    getNombresPermisos() {
        const permisos = this.getPermisos();
        if (!Array.isArray(permisos)) {
            return [];
        }
        return permisos.map(p => {
            // Si es un objeto con propiedad nombre, usar nombre
            if (typeof p === 'object' && p !== null && p.nombre) {
                return p.nombre;
            }
            // Si es un string, usarlo directamente
            if (typeof p === 'string') {
                return p;
            }
            return null;
        }).filter(p => p !== null);
    },

    /**
     * Verificar si el usuario tiene un permiso específico
     * @param {string} permiso_nombre Nombre del permiso (ej: 'RIFAS_CREAR')
     * @returns {boolean} True si tiene el permiso
     */
    tienePermiso(permiso_nombre) {
        if (this.esSuperAdmin()) return true;
        return this.getNombresPermisos().includes(permiso_nombre);
    },

    /**
     * Verificar si el usuario tiene alguno de los permisos especificados
     * @param {string[]} permisos Array de nombres de permisos
     * @returns {boolean} True si tiene al menos uno
     */
    tieneAlgunPermiso(permisos) {
        return permisos.some(permiso => this.tienePermiso(permiso));
    },

    /**
     * Verificar si el usuario tiene todos los permisos especificados
     * @param {string[]} permisos Array de nombres de permisos
     * @returns {boolean} True si tiene todos
     */
    tieneTodosPermisos(permisos) {
        return permisos.every(permiso => this.tienePermiso(permiso));
    },

    /**
     * Verificar si el usuario tiene un rol específico
     * @param {string} rol_nombre Nombre del rol (ej: 'SUPERADMIN')
     * @returns {boolean} True si tiene el rol
     */
    tieneRol(rol_nombre) {
        const userInfo = Auth.getUserInfo();
        if (!userInfo) return false;
        
        const rolUsuario = (userInfo.rol_nombre || '').toUpperCase();
        const rolBuscado = rol_nombre.toUpperCase();
        
        return rolUsuario === rolBuscado;
    },

    /**
     * Verificar si el usuario es SUPERADMIN
     * @returns {boolean} True si es SUPERADMIN
     */
    esSuperAdmin() {
        if (typeof Auth === 'undefined') return false;
        const userInfo = Auth.getUserInfo();
        if (!userInfo) return false;
        return (userInfo.rol_nombre || '').toUpperCase().trim() === 'SUPERADMIN';
    },

    /**
     * Verificar si el usuario es ADMIN
     * @returns {boolean} True si es ADMIN
     */
    esAdmin() {
        return this.tieneRol('ADMIN') || this.esSuperAdmin();
    },

    /**
     * Verificar permiso de forma asíncrona (llamada al servidor)
     * @param {string} permiso_nombre Nombre del permiso
     * @returns {Promise<boolean>} Promise que resuelve a true si tiene el permiso
     */
    async verificarPermisoAsync(permiso_nombre) {
        try {
            const response = await API.get(`permisos/verificarPermiso?permiso=${encodeURIComponent(permiso_nombre)}`);
            return response.ok && response.tiene_permiso === true;
        } catch (error) {
            console.error('Error al verificar permiso:', error);
            return false;
        }
    },

    /**
     * Cargar permisos del usuario desde el servidor
     * @returns {Promise<array>} Promise con los permisos
     */
    async cargarPermisos() {
        try {
            const response = await API.get('permisos/getPermisosUsuario');
            if (response.ok && response.data) {
                // Actualizar permisos en localStorage
                const usuario = Auth.getUsuario();
                if (usuario) {
                    usuario.permisos = response.data;
                    localStorage.setItem('usuario', JSON.stringify(usuario));
                }
                return response.data;
            }
            return [];
        } catch (error) {
            console.error('Error al cargar permisos:', error);
            return [];
        }
    }
};

// Exponer globalmente
window.Permisos = Permisos;
