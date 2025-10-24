/**
 * Auth.js - Helper Global de Autenticación
 * Maneja autenticación, tokens y peticiones a la API
 * 
 * IMPORTANTE: Requiere que window.BASE_URL y window.API_BASE_URL estén definidas
 * Estas variables se definen en views/components/js.php usando Enrutamiento::dominio()
 */

// Configuración de la API (se obtiene del PHP vía window.API_BASE_URL)
const API_CONFIG = {
    BASE_URL: window.API_BASE_URL || (window.location.origin + '/CONTROL_ASISTENCIA_CAFED/api'),
    TIMEOUT: 30000
};

/**
 * Auth - Manejo de autenticación
 */
const Auth = {
    /**
     * Hacer login
     */
    async login(username, password, sedeId = 1) {
        try {
            const response = await fetch(`${API_CONFIG.BASE_URL}/auth/login`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    username: username,
                    password: password,
                    sede_id: sedeId
                })
            });

            const data = await response.json();

            if (data.ok) {
                // Guardar en localStorage
                localStorage.setItem('token', data.data.token);
                localStorage.setItem('usuario', JSON.stringify(data.data));
                
                console.log('Login exitoso:', data.data.nombre_completo);
                return data;
            } else {
                console.error('Error en login:', data.msj);
                return data;
            }
        } catch (error) {
            console.error('Error de conexión:', error);
            return { ok: false, msj: 'Error de conexión con el servidor' };
        }
    },

    /**
     * Hacer logout
     */
    async logout() {
        const token = this.getToken();
        
        if (token) {
            try {
                await fetch(`${API_CONFIG.BASE_URL}/auth/logout`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': `Bearer ${token}`
                    },
                    body: JSON.stringify({ token: token })
                });
            } catch (error) {
                console.error('Error en logout:', error);
            }
        }

        // Limpiar datos locales
        localStorage.clear();
        sessionStorage.clear();
        
        // Redirigir al login usando la ruta base dinámica
        window.location.href = window.BASE_URL || '/CONTROL_ASISTENCIA_CAFED/';
    },

    /**
     * Obtener token
     */
    getToken() {
        return localStorage.getItem('token');
    },

    /**
     * Obtener datos del usuario
     */
    getUsuario() {
        const usuarioStr = localStorage.getItem('usuario');
        return usuarioStr ? JSON.parse(usuarioStr) : null;
    },

    /**
     * Verificar si está autenticado
     */
    isAuthenticated() {
        return this.getToken() !== null;
    },

    /**
     * Requerir autenticación (redirige si no está autenticado)
     */
    requireAuth() {
        if (!this.isAuthenticated()) {
            window.location.href = window.BASE_URL || '/CONTROL_ASISTENCIA_CAFED/';
            return false;
        }
        return true;
    },

    /**
     * Obtener info del usuario
     */
    getUserInfo() {
        const usuario = this.getUsuario();
        if (!usuario) return null;

        return {
            usuario_id: usuario.usuario_id,
            nombre_completo: usuario.nombre_completo,
            sede_id: usuario.sede_id,
            sede_nombre: usuario.sede_nombre,
            rol_id: usuario.rol_id,
            rol_nombre: usuario.rol_nombre,
            empleado_id: usuario.empleado_id
        };
    }
};

/**
 * API - Peticiones autenticadas
 */
const API = {
    /**
     * GET con autenticación
     */
    async get(endpoint, params = {}) {
        const token = Auth.getToken();
        
        if (!token) {
            console.error('No hay token');
            Auth.logout();
            return null;
        }

        try {
            const url = new URL(`${API_CONFIG.BASE_URL}/${endpoint}`);
            Object.keys(params).forEach(key => url.searchParams.append(key, params[key]));

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await response.json();

            if (response.status === 401) {
                console.error('Token inválido');
                Auth.logout();
                return null;
            }

            return data;
        } catch (error) {
            console.error('Error en GET:', error);
            return { ok: false, msj: 'Error de conexión' };
        }
    },

    /**
     * POST con autenticación
     */
    async post(endpoint, body = {}) {
        const token = Auth.getToken();
        
        if (!token) {
            console.error('No hay token');
            Auth.logout();
            return null;
        }

        try {
            const response = await fetch(`${API_CONFIG.BASE_URL}/${endpoint}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify(body)
            });

            const data = await response.json();

            if (response.status === 401) {
                console.error('Token inválido');
                Auth.logout();
                return null;
            }

            return data;
        } catch (error) {
            console.error('Error en POST:', error);
            return { ok: false, msj: 'Error de conexión' };
        }
    }
};

/**
 * Utils - Utilidades globales
 */
const Utils = {
    formatearFecha(fecha) {
        if (!fecha) return '-';
        return new Date(fecha).toLocaleDateString('es-PE');
    },

    formatearMoneda(valor) {
        if (!valor) return 'S/. 0.00';
        return `S/. ${parseFloat(valor).toLocaleString('es-PE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        })}`;
    },

    showAlert(message, type = 'info') {
        const titles = {
            'success': 'Éxito',
            'error': 'Error',
            'warning': 'Advertencia',
            'info': 'Información'
        };

        Swal.fire({
            title: titles[type],
            text: message,
            icon: type,
            confirmButtonText: 'Aceptar'
        });
    },

    showToast(message, type = 'error') {
        const config = {
            'success': { 
                heading: 'Éxito', 
                icon: 'success', 
                loaderBg: '#46c35f' 
            },
            'error': { 
                heading: 'Error', 
                icon: 'error', 
                loaderBg: '#bf441d' 
            },
            'warning': { 
                heading: 'Advertencia', 
                icon: 'warning', 
                loaderBg: '#f8b739' 
            },
            'info': { 
                heading: 'Información', 
                icon: 'info', 
                loaderBg: '#3b82f6' 
            }
        };

        const toastConfig = config[type] || config['info'];

        $.toast({
            heading: toastConfig.heading,
            text: message,
            icon: toastConfig.icon,
            position: 'top-right',
            loader: true,
            loaderBg: toastConfig.loaderBg,
            hideAfter: 4000
        });
    },

    showLoading(message = 'Cargando...') {
        Swal.fire({
            title: message,
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },

    closeLoading() {
        Swal.close();
    }
};

// Exponer globalmente
window.Auth = Auth;
window.API = API;
window.Utils = Utils;

/* console.log(' Auth.js cargado correctamente'); */

