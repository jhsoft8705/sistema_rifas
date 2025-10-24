/**
 * Módulo de Marcaciones Biométricas
 * Manejo de marcaciones desde el frontend
 */

const MarcacionesAPI = {
    baseURL: '/api/routes/routes_marcaciones.php',
    
    /**
     * Procesa logs pendientes del biométrico
     * @param {number} sedeId - ID de la sede
     * @param {number} limite - Cantidad máxima de logs a procesar
     * @returns {Promise}
     */
    procesarLogs: async (sedeId = null, limite = 100) => {
        try {
            const response = await fetch(`${MarcacionesAPI.baseURL}/procesar-logs`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    sede_id: sedeId,
                    limite: limite
                })
            });
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al procesar logs:', error);
            return {
                success: false,
                mensaje: 'Error de conexión: ' + error.message
            };
        }
    },
    
    /**
     * Obtiene marcaciones filtradas
     * @param {number} sedeId - ID de la sede
     * @param {string} fechaInicio - Fecha inicial (YYYY-MM-DD)
     * @param {string} fechaFin - Fecha final (YYYY-MM-DD)
     * @param {number} empleadoId - ID del empleado (opcional)
     * @returns {Promise}
     */
    obtenerMarcaciones: async (sedeId, fechaInicio, fechaFin, empleadoId = null) => {
        try {
            const params = new URLSearchParams({
                sede_id: sedeId,
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            });
            
            if (empleadoId) {
                params.append('empleado_id', empleadoId);
            }
            
            const response = await fetch(`${MarcacionesAPI.baseURL}/obtener?${params}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al obtener marcaciones:', error);
            return {
                success: false,
                mensaje: 'Error de conexión: ' + error.message
            };
        }
    },
    
    /**
     * Obtiene resumen de asistencia
     * @param {number} sedeId - ID de la sede
     * @param {string} fechaInicio - Fecha inicial (YYYY-MM-DD)
     * @param {string} fechaFin - Fecha final (YYYY-MM-DD)
     * @param {number} empleadoId - ID del empleado (opcional)
     * @returns {Promise}
     */
    obtenerResumen: async (sedeId, fechaInicio, fechaFin, empleadoId = null) => {
        try {
            const params = new URLSearchParams({
                sede_id: sedeId,
                fecha_inicio: fechaInicio,
                fecha_fin: fechaFin
            });
            
            if (empleadoId) {
                params.append('empleado_id', empleadoId);
            }
            
            const response = await fetch(`${MarcacionesAPI.baseURL}/resumen?${params}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al obtener resumen:', error);
            return {
                success: false,
                mensaje: 'Error de conexión: ' + error.message
            };
        }
    },
    
    /**
     * Obtiene logs pendientes de procesar
     * @param {number} sedeId - ID de la sede
     * @param {number} limite - Cantidad máxima de logs
     * @returns {Promise}
     */
    obtenerLogsPendientes: async (sedeId, limite = 100) => {
        try {
            const params = new URLSearchParams({
                sede_id: sedeId,
                limite: limite
            });
            
            const response = await fetch(`${MarcacionesAPI.baseURL}/logs-pendientes?${params}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al obtener logs pendientes:', error);
            return {
                success: false,
                mensaje: 'Error de conexión: ' + error.message
            };
        }
    },
    
    /**
     * Obtiene marcaciones pendientes de reconciliación
     * @param {number} sedeId - ID de la sede
     * @returns {Promise}
     */
    obtenerPendientesReconciliacion: async (sedeId) => {
        try {
            const params = new URLSearchParams({
                sede_id: sedeId
            });
            
            const response = await fetch(`${MarcacionesAPI.baseURL}/pendientes-reconciliacion?${params}`);
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Error al obtener pendientes:', error);
            return {
                success: false,
                mensaje: 'Error de conexión: ' + error.message
            };
        }
    }
};

// =====================================================
// FUNCIONES DE UI
// =====================================================

/**
 * Carga marcaciones en una tabla
 */
async function cargarMarcaciones(sedeId, fechaInicio, fechaFin) {
    try {
        // Mostrar loading
        mostrarLoading('Cargando marcaciones...');
        
        const resultado = await MarcacionesAPI.obtenerMarcaciones(sedeId, fechaInicio, fechaFin);
        
        if (resultado.success) {
            renderizarTablaMarcaciones(resultado.data);
            mostrarMensaje('success', `${resultado.total} marcaciones cargadas`);
        } else {
            mostrarMensaje('error', resultado.mensaje);
        }
    } catch (error) {
        mostrarMensaje('error', 'Error al cargar marcaciones');
        console.error(error);
    } finally {
        ocultarLoading();
    }
}

/**
 * Renderiza tabla de marcaciones
 */
function renderizarTablaMarcaciones(marcaciones) {
    const tbody = document.querySelector('#tablaMarcaciones tbody');
    
    if (!tbody) {
        console.error('No se encontró el tbody de la tabla');
        return;
    }
    
    tbody.innerHTML = '';
    
    if (marcaciones.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center">No hay marcaciones registradas</td>
            </tr>
        `;
        return;
    }
    
    marcaciones.forEach(marcacion => {
        const row = document.createElement('tr');
        
        // Calcular horas trabajadas
        const horasTrabajadas = marcacion.minutos_trabajados 
            ? formatearMinutosAHoras(marcacion.minutos_trabajados)
            : '-';
        
        // Badge de estado
        const estadoBadge = obtenerBadgeEstado(marcacion.estado_asistencia);
        
        row.innerHTML = `
            <td>${marcacion.fecha_marcacion}</td>
            <td>${marcacion.nombre_completo}</td>
            <td>${marcacion.numero_documento}</td>
            <td>${marcacion.nombre_cargo || '-'}</td>
            <td>${marcacion.hora_entrada || '-'}</td>
            <td>${marcacion.hora_salida || '-'}</td>
            <td>${horasTrabajadas}</td>
            <td>${estadoBadge}</td>
            <td>${marcacion.minutos_tardanza || 0} min</td>
            <td>
                <button class="btn btn-sm btn-info" onclick="verDetalleMarcacion(${marcacion.marcacion_id})">
                    <i class="ri-eye-line"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
    });
}

/**
 * Procesa logs pendientes manualmente
 */
async function procesarLogsManual() {
    try {
        const sedeId = document.getElementById('sedeSelect')?.value || null;
        
        if (!confirm('¿Desea procesar los logs pendientes del biométrico?')) {
            return;
        }
        
        mostrarLoading('Procesando logs...');
        
        const resultado = await MarcacionesAPI.procesarLogs(sedeId, 100);
        
        if (resultado.success) {
            const mensaje = `
                Logs procesados exitosamente:<br>
                - Total procesados: ${resultado.data.logs_procesados}<br>
                - Empleados afectados: ${resultado.data.empleados_afectados}
            `;
            mostrarMensaje('success', mensaje);
            
            // Recargar marcaciones
            cargarMarcacionesDelDia();
        } else {
            mostrarMensaje('error', resultado.mensaje);
        }
    } catch (error) {
        mostrarMensaje('error', 'Error al procesar logs');
        console.error(error);
    } finally {
        ocultarLoading();
    }
}

/**
 * Carga resumen de asistencia
 */
async function cargarResumenAsistencia(sedeId, fechaInicio, fechaFin) {
    try {
        mostrarLoading('Generando resumen...');
        
        const resultado = await MarcacionesAPI.obtenerResumen(sedeId, fechaInicio, fechaFin);
        
        if (resultado.success) {
            renderizarResumenAsistencia(resultado.data);
        } else {
            mostrarMensaje('error', resultado.mensaje);
        }
    } catch (error) {
        mostrarMensaje('error', 'Error al cargar resumen');
        console.error(error);
    } finally {
        ocultarLoading();
    }
}

/**
 * Renderiza resumen de asistencia
 */
function renderizarResumenAsistencia(resumen) {
    const container = document.getElementById('resumenContainer');
    
    if (!container) {
        console.error('No se encontró el contenedor de resumen');
        return;
    }
    
    container.innerHTML = '';
    
    resumen.forEach(empleado => {
        const card = `
            <div class="col-md-6 col-lg-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">${empleado.nombre_completo}</h5>
                        <p class="text-muted">${empleado.nombre_cargo || 'Sin cargo'}</p>
                        <hr>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Presente</small>
                                <h4 class="text-success">${empleado.dias_presente}</h4>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Tardanzas</small>
                                <h4 class="text-warning">${empleado.dias_tardanza}</h4>
                            </div>
                            <div class="col-6 mt-2">
                                <small class="text-muted">Faltas</small>
                                <h4 class="text-danger">${empleado.dias_falta}</h4>
                            </div>
                            <div class="col-6 mt-2">
                                <small class="text-muted">Justificado</small>
                                <h4 class="text-info">${empleado.dias_justificado}</h4>
                            </div>
                        </div>
                        <hr>
                        <small class="text-muted">
                            Total tardanzas: ${empleado.total_minutos_tardanza || 0} min
                        </small>
                    </div>
                </div>
            </div>
        `;
        
        container.innerHTML += card;
    });
}

/**
 * Ver logs pendientes
 */
async function verLogsPendientes(sedeId) {
    try {
        mostrarLoading('Cargando logs pendientes...');
        
        const resultado = await MarcacionesAPI.obtenerLogsPendientes(sedeId, 50);
        
        if (resultado.success) {
            mostrarModalLogsPendientes(resultado.data);
        } else {
            mostrarMensaje('error', resultado.mensaje);
        }
    } catch (error) {
        mostrarMensaje('error', 'Error al obtener logs pendientes');
        console.error(error);
    } finally {
        ocultarLoading();
    }
}

// =====================================================
// FUNCIONES AUXILIARES
// =====================================================

/**
 * Formatea minutos a formato HH:MM
 */
function formatearMinutosAHoras(minutos) {
    const horas = Math.floor(minutos / 60);
    const mins = minutos % 60;
    return `${horas.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
}

/**
 * Obtiene badge HTML según estado
 */
function obtenerBadgeEstado(estado) {
    const badges = {
        'Presente': '<span class="badge bg-success">Presente</span>',
        'Tardanza': '<span class="badge bg-warning">Tardanza</span>',
        'Falta': '<span class="badge bg-danger">Falta</span>',
        'Justificado': '<span class="badge bg-info">Justificado</span>'
    };
    
    return badges[estado] || `<span class="badge bg-secondary">${estado}</span>`;
}

/**
 * Muestra loading spinner
 */
function mostrarLoading(mensaje = 'Cargando...') {
    // Implementar según tu UI
    console.log('Loading:', mensaje);
}

/**
 * Oculta loading spinner
 */
function ocultarLoading() {
    // Implementar según tu UI
    console.log('Loading completado');
}

/**
 * Muestra mensaje (success, error, warning, info)
 */
function mostrarMensaje(tipo, mensaje) {
    // Implementar según tu sistema de notificaciones
    console.log(`[${tipo.toUpperCase()}] ${mensaje}`);
    
    // Ejemplo con SweetAlert2 (si lo tienes instalado)
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: tipo === 'success' ? 'success' : 'error',
            title: tipo === 'success' ? 'Éxito' : 'Error',
            html: mensaje,
            timer: 3000
        });
    }
}

/**
 * Carga marcaciones del día actual
 */
async function cargarMarcacionesDelDia() {
    const hoy = new Date().toISOString().split('T')[0];
    const sedeId = document.getElementById('sedeSelect')?.value || 1;
    await cargarMarcaciones(sedeId, hoy, hoy);
}

// =====================================================
// INICIALIZACIÓN
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    console.log('Módulo de Marcaciones cargado');
    
    // Auto-procesar logs cada 5 minutos (opcional)
    // setInterval(() => {
    //     MarcacionesAPI.procesarLogs(null, 100);
    // }, 5 * 60 * 1000);
    
    // Event listeners
    const btnProcesarLogs = document.getElementById('btnProcesarLogs');
    if (btnProcesarLogs) {
        btnProcesarLogs.addEventListener('click', procesarLogsManual);
    }
    
    const btnCargarMarcaciones = document.getElementById('btnCargarMarcaciones');
    if (btnCargarMarcaciones) {
        btnCargarMarcaciones.addEventListener('click', cargarMarcacionesDelDia);
    }
});

// Exportar para uso global
window.MarcacionesAPI = MarcacionesAPI;
window.procesarLogsManual = procesarLogsManual;
window.cargarMarcaciones = cargarMarcaciones;
window.cargarResumenAsistencia = cargarResumenAsistencia;
window.verLogsPendientes = verLogsPendientes;


