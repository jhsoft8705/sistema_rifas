/**
 * Utils.js - Helper Global de Utilidades
 * Funciones genéricas y reutilizables para todo el sistema
 * 
 * IMPORTANTE: Requiere que window.BASE_URL esté definida
 * Esta variable se define en views/components/js.php usando Enrutamiento::dominio()
 * 
 * Dependencias:
 * - jQuery
 * - SweetAlert2
 * - jQuery Toast Plugin
 */

const Utils = {
    getDataTableLanguageES() {
        return {
            sProcessing: "Cargando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron resultados",
            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            sUrl: "",
            sInfoThousands: ",",
            sLoadingRecords: "Cargando...",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            },
            oAria: {
                sSortAscending: ": Activar para ordenar la columna de manera ascendente",
                sSortDescending: ": Activar para ordenar la columna de manera descendente"
            }
        };
    },

    /**
     * Mostrar alerta con SweetAlert2
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo: success, error, warning, info, question
     * @param {object} options - Opciones adicionales de SweetAlert2
     * @returns {Promise}
     */
    showAlert(message, type = 'info', options = {}) {
        const defaultOptions = {
            title: this.getTitleByType(type),
            text: message,
            icon: type,
            confirmButtonText: 'Aceptar',
            confirmButtonColor: '#3085d6',
            ...options
        };

        return Swal.fire(defaultOptions);
    },

    /**
     * Mostrar alerta de confirmación
     * @param {string} title - Título de la confirmación
     * @param {string} text - Texto de la confirmación
     * @param {string} confirmText - Texto del botón confirmar
     * @param {string} cancelText - Texto del botón cancelar
     * @returns {Promise}
     */
    showConfirm(title = '¿Está seguro?', text = '', confirmText = 'Sí, confirmar', cancelText = 'Cancelar') {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: confirmText,
            cancelButtonText: cancelText
        });
    },

    /**
     * Mostrar toast notification
     * @param {string} message - Mensaje a mostrar
     * @param {string} type - Tipo: success, error, warning, info
     * @param {object} options - Opciones adicionales
     */
    showToast(message, type = 'info', options = {}) {
        const defaultOptions = {
            text: message,
            heading: this.getTitleByType(type),
            icon: type,
            position: 'top-right',
            hideAfter: 3000,
            stack: 5,
            ...options
        };

        $.toast(defaultOptions);
    },

    /**
     * Mostrar loading con SweetAlert2
     * @param {string} message - Mensaje de carga
     */
    showLoading(message = 'Cargando...') {
        Swal.fire({
            html: `<div style="font-size: 0.95rem;">${message}</div>`,
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
    },

    /**
     * Cerrar loading/modal de SweetAlert2
     */
    closeLoading() {
        Swal.close();
    },

    /**
     * Obtener título según el tipo de mensaje
     * @param {string} type - Tipo de mensaje
     * @returns {string}
     */
    getTitleByType(type) {
        const titles = {
            success: 'Éxito',
            error: 'Error',
            warning: 'Advertencia',
            info: 'Información',
            question: 'Confirmación'
        };
        return titles[type] || 'Notificación';
    },

    /**
     * Validar email
     * @param {string} email - Email a validar
     * @returns {boolean}
     */
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    /**
     * Validar DNI peruano (8 dígitos)
     * @param {string} dni - DNI a validar
     * @returns {boolean}
     */
    isValidDNI(dni) {
        return /^\d{8}$/.test(dni);
    },

    /**
     * Validar RUC peruano (11 dígitos)
     * @param {string} ruc - RUC a validar
     * @returns {boolean}
     */
    isValidRUC(ruc) {
        return /^\d{11}$/.test(ruc);
    },

    /**
     * Validar teléfono peruano (9 dígitos)
     * @param {string} telefono - Teléfono a validar
     * @returns {boolean}
     */
    isValidTelefono(telefono) {
        return /^9\d{8}$/.test(telefono);
    },

    /**
     * Validar campo obligatorio
     * @param {string} fieldId - ID del campo
     * @param {string} errorMessage - Mensaje de error
     * @returns {boolean}
     */
    validarCampo(fieldId, errorMessage) {
        const field = $("#" + fieldId);
        const value = field.val().trim();
        
        if (value === "") {
            field.addClass("is-invalid");
            $("#" + fieldId + "_error").text(errorMessage);
            return false;
        } else {
            field.removeClass("is-invalid");
            $("#" + fieldId + "_error").text("");
            return true;
        }
    },

    /**
     * Limpiar validaciones de un formulario
     * @param {string} formId - ID del formulario
     */
    limpiarValidaciones(formId = null) {
        if (formId) {
            $(`#${formId} .is-invalid`).removeClass("is-invalid");
            $(`#${formId} .invalid-feedback`).text("");
        } else {
            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").text("");
        }
    },

    /**
     * Formatear fecha (YYYY-MM-DD) a formato peruano (DD/MM/YYYY)
     * @param {string} fecha - Fecha en formato YYYY-MM-DD
     * @returns {string}
     */
    formatearFecha(fecha) {
        if (!fecha) return '';
        const [año, mes, dia] = fecha.split('-');
        return `${dia}/${mes}/${año}`;
    },

    /**
     * Formatear fecha y hora
     * @param {string} fechaHora - Fecha y hora en formato ISO
     * @returns {string}
     */
    formatearFechaHora(fechaHora) {
        if (!fechaHora) return '';
        const fecha = new Date(fechaHora);
        return fecha.toLocaleString('es-PE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    },

    /**
     * Formatear número como moneda peruana
     * @param {number} numero - Número a formatear
     * @param {number} decimales - Cantidad de decimales
     * @returns {string}
     */
    formatearMoneda(numero, decimales = 2) {
        return new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: decimales
        }).format(numero);
    },

    /**
     * Formatear número con separadores de miles
     * @param {number} numero - Número a formatear
     * @param {number} decimales - Cantidad de decimales
     * @returns {string}
     */
    formatearNumero(numero, decimales = 2) {
        return new Intl.NumberFormat('es-PE', {
            minimumFractionDigits: decimales,
            maximumFractionDigits: decimales
        }).format(numero);
    },

    /**
     * Capitalizar primera letra de cada palabra
     * @param {string} texto - Texto a capitalizar
     * @returns {string}
     */
    capitalize(texto) {
        if (!texto) return '';
        return texto.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
    },

    /**
     * Obtener la URL base del sistema
     * @returns {string}
     */
    getBaseUrl() {
        return window.BASE_URL || '';
    },

    /**
     * Redirigir a una ruta del sistema
     * @param {string} ruta - Ruta relativa (ej: '/empleados', '/dashboard')
     */
    redirect(ruta) {
        window.location.href = this.getBaseUrl() + ruta;
    },

    /**
     * Recargar página actual
     */
    reload() {
        window.location.reload();
    },

    /**
     * Deshabilitar botón con estado de carga
     * @param {string} btnSelector - Selector del botón
     * @param {string} loadingText - Texto durante la carga
     * @returns {object} - { originalText, originalHtml }
     */
    disableButton(btnSelector, loadingText = 'Procesando...') {
        const $btn = $(btnSelector);
        const originalText = $btn.text();
        const originalHtml = $btn.html();
        
        $btn.prop("disabled", true)
            .html(`<i class="ri-loader-4-line me-1"></i>${loadingText}`);
        
        return { originalText, originalHtml };
    },

    /**
     * Habilitar botón y restaurar texto
     * @param {string} btnSelector - Selector del botón
     * @param {string} originalHtml - HTML original del botón
     */
    enableButton(btnSelector, originalHtml) {
        $(btnSelector).prop("disabled", false).html(originalHtml);
    },

    /**
     * Poblar un select con datos
     * @param {string} selector - Selector del select
     * @param {array} data - Array de datos
     * @param {string} valueField - Nombre del campo para el value
     * @param {string} textField - Nombre del campo para el texto
     * @param {string} placeholderText - Texto del placeholder (opcional)
     */
    poblarSelect(selector, data, valueField, textField, placeholderText = null) {
        const $select = $(selector);
        const placeholder = placeholderText || $select.find('option:first').text();
        $select.empty();
        $select.append(`<option value="">${placeholder}</option>`);
        
        if (data && data.length > 0) {
            data.forEach(item => {
                $select.append(`<option value="${item[valueField]}">${item[textField]}</option>`);
            });
        }
    },

    /**
     * Generar código aleatorio alfanumérico
     * @param {number} length - Longitud del código
     * @returns {string}
     */
    generarCodigo(length = 8) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    },

    /**
     * Debounce function - Retrasar ejecución de una función
     * @param {function} func - Función a ejecutar
     * @param {number} wait - Tiempo de espera en ms
     * @returns {function}
     */
    debounce(func, wait = 300) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    /**
     * Copiar texto al portapapeles
     * @param {string} texto - Texto a copiar
     * @returns {Promise}
     */
    async copiarAlPortapapeles(texto) {
        try {
            await navigator.clipboard.writeText(texto);
            this.showToast('Copiado al portapapeles', 'success');
            return true;
        } catch (err) {
            console.error('Error al copiar:', err);
            this.showToast('Error al copiar al portapapeles', 'error');
            return false;
        }
    },

    /**
     * Descargar archivo desde base64
     * @param {string} base64Data - Datos en base64
     * @param {string} filename - Nombre del archivo
     * @param {string} mimeType - Tipo MIME del archivo
     */
    descargarBase64(base64Data, filename, mimeType = 'application/octet-stream') {
        const byteCharacters = atob(base64Data);
        const byteNumbers = new Array(byteCharacters.length);
        for (let i = 0; i < byteCharacters.length; i++) {
            byteNumbers[i] = byteCharacters.charCodeAt(i);
        }
        const byteArray = new Uint8Array(byteNumbers);
        const blob = new Blob([byteArray], { type: mimeType });
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        window.URL.revokeObjectURL(url);
    },

    /**
     * Obtener parámetro de la URL
     * @param {string} param - Nombre del parámetro
     * @returns {string|null}
     */
    getUrlParameter(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    },

    /**
     * Scroll suave hacia un elemento
     * @param {string} selector - Selector del elemento
     * @param {number} offset - Offset en píxeles (opcional)
     */
    scrollTo(selector, offset = 0) {
        const element = $(selector);
        if (element.length) {
            $('html, body').animate({
                scrollTop: element.offset().top - offset
            }, 500);
        }
    },

    /**
     * Calcular edad a partir de fecha de nacimiento
     * @param {string} fechaNacimiento - Fecha en formato YYYY-MM-DD
     * @returns {number}
     */
    calcularEdad(fechaNacimiento) {
        if (!fechaNacimiento) return 0;
        const hoy = new Date();
        const nacimiento = new Date(fechaNacimiento);
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        const mes = hoy.getMonth() - nacimiento.getMonth();
        if (mes < 0 || (mes === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }
        return edad;
    },

    /**
     * Escapar HTML para prevenir XSS
     * @param {string} texto - Texto a escapar
     * @returns {string}
     */
    escapeHtml(texto) {
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return texto.replace(/[&<>"']/g, m => map[m]);
    },

    /**
     * Generar color hexadecimal aleatorio
     * @returns {string}
     */
    generarColorAleatorio() {
        return '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0');
    },

    /**
     * Inicializar tooltips de Bootstrap
     */
    initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    },

    /**
     * Destruir tooltips de Bootstrap
     */
    destroyTooltips() {
        const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltips.forEach(tooltip => {
            const instance = bootstrap.Tooltip.getInstance(tooltip);
            if (instance) {
                instance.dispose();
            }
        });
    },

    /**
     * Limpiar caché del sistema
     * @param {string} key - Clave específica a limpiar (opcional)
     */
    limpiarCache(key = null) {
        if (key) {
            localStorage.removeItem(key);
            localStorage.removeItem(`${key}_timestamp`);
            console.log(`🗑️ Caché limpiado: ${key}`);
        } else {
            // Limpiar solo cachés del sistema (no auth)
            const keys = Object.keys(localStorage);
            keys.forEach(k => {
                if (k.startsWith('catalogos_') || k.endsWith('_timestamp')) {
                    localStorage.removeItem(k);
                }
            });
            console.log('🗑️ Todos los cachés del sistema limpiados');
        }
        this.showToast('Caché limpiado correctamente', 'success');
    },

    /**
     * Obtener tiempo desde última actualización de caché
     * @param {string} key - Clave del caché
     * @returns {number} - Minutos desde última actualización
     */
    getTiempoCacheMinutos(key) {
        const timestamp = localStorage.getItem(`${key}_timestamp`);
        if (!timestamp) return null;
        
        const now = new Date().getTime();
        const cached = parseInt(timestamp);
        return Math.floor((now - cached) / 60000);
    },

    /**
     * Encriptar un ID numérico para usar en URLs
     * @param {number|string} id - ID a encriptar
     * @returns {string} - ID encriptado en base64
     */
    encryptId(id) {
        if (!id) return '';
        try {
            // Convertir a string y agregar un salt simple
            const salt = 'RIFA_SYS_2025';
            const data = `${id}_${salt}_${Date.now()}`;
            // Usar btoa para base64 (compatible con navegadores)
            return btoa(unescape(encodeURIComponent(data))).replace(/[+/=]/g, (m) => {
                return { '+': '-', '/': '_', '=': '' }[m];
            });
        } catch (error) {
            console.error('Error al encriptar ID:', error);
            return '';
        }
    },

    /**
     * Desencriptar un ID desde URL
     * @param {string} encryptedId - ID encriptado
     * @returns {number|null} - ID desencriptado o null si hay error
     */
    decryptId(encryptedId) {
        if (!encryptedId) return null;
        try {
            // Restaurar caracteres especiales
            const base64 = encryptedId.replace(/[-_]/g, (m) => {
                return { '-': '+', '_': '/' }[m];
            });
            // Decodificar base64
            const decoded = decodeURIComponent(escape(atob(base64)));
            // Extraer el ID (antes del primer _)
            const parts = decoded.split('_');
            const id = parseInt(parts[0], 10);
            return isNaN(id) ? null : id;
        } catch (error) {
            console.error('Error al desencriptar ID:', error);
            return null;
        }
    }
};

// Exportar Utils para uso global
window.Utils = Utils;

