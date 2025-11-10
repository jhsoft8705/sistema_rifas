/**
 * Sistema de Gestión de Empleados - CAFED
 * Control de Asistencia
 */

// Variables globales
let tableEmpleados;
let empleadosData = [];
let userInfo = null;
let selectedEmpleados = [];
let fechaInicio = '';
let fechaFin = '';
let estadoFiltro = '';
let unidadFiltro = '';

// Inicialización cuando el documento esté listo
$(document).ready(function() {
    if (!Auth.requireAuth()) {
        return;
    }
    
    userInfo = Auth.getUserInfo();
    
    initializeDateRange();
    initializeDataTable();
    initializeEventListeners();
    initializeTooltips();
    cargarGerencias();
    cargarEmpleados();
});

/**
 * Inicializar selector de rango de fechas
 */
function initializeDateRange() {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const formatoFecha = (fecha) => fecha.toISOString().split('T')[0];
    fechaInicio = formatoFecha(new Date(hoy.getFullYear(), hoy.getMonth(), 1));
    fechaFin = formatoFecha(hoy);
    
    // Inicializar flatpickr si está disponible
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#fecha_rango", {
            mode: "range",
            dateFormat: "Y-m-d",
            locale: "es",
            defaultDate: [fechaInicio, fechaFin]
        });
    }
}

/**
 * Cargar gerencias/unidades organizacionales para el filtro
 */
async function cargarGerencias() {
    try {
        const gerencias = await API.get('maestros/gerencias', { sede_id: userInfo.sede_id });
        
        if (gerencias && gerencias.ok && gerencias.data) {
            // Usar Utils.poblarSelect si está disponible, sino hacerlo manualmente
            if (typeof Utils !== 'undefined' && Utils.poblarSelect) {
                Utils.poblarSelect('#unidad_filtro', gerencias.data, 'id', 'nombre', 'Todas las gerencias');
            } else {
                // Fallback manual
                const $select = $('#unidad_filtro');
                $select.empty();
                $select.append('<option value="">Todas las gerencias</option>');
                
                gerencias.data.forEach(function(gerencia) {
                    $select.append(`<option value="${gerencia.id}">${gerencia.nombre}</option>`);
                });
            }
        } else {
            console.error('Error al cargar gerencias:', gerencias?.msj || 'Respuesta inválida');
        }
    } catch (error) {
        console.error('Error al cargar gerencias:', error);
    }
}

/**
 * Cargar empleados desde la API
 */
async function cargarEmpleados() {
    try {
        Utils.showLoading('Cargando empleados...');
        
        // Preparar los datos a enviar, eliminando valores null/undefined/vacíos
        const params = {
            sede_id: userInfo.sede_id
        };
        
        // Solo agregar parámetros si tienen valor
        if (estadoFiltro !== null && estadoFiltro !== undefined && estadoFiltro !== '') {
            params.estado = estadoFiltro;
        }
        if (unidadFiltro !== null && unidadFiltro !== undefined && unidadFiltro !== '') {
            params.gerencia_id = unidadFiltro;
        }
        if (fechaInicio !== null && fechaInicio !== undefined && fechaInicio !== '') {
            params.fecha_inicial = fechaInicio;
        }
        if (fechaFin !== null && fechaFin !== undefined && fechaFin !== '') {
            params.fecha_final = fechaFin;
        }
        
        const resultado = await API.get('empleados/getAllFull', params);
        
        Utils.closeLoading();
        
        if (resultado && resultado.ok) {
            empleadosData = resultado.data;
            tableEmpleados.clear().rows.add(empleadosData).draw();
            setTimeout(() => reinitializeTooltips(), 100);
        } else {
            tableEmpleados.clear().draw();
            Utils.showToast(resultado?.msj || 'Error al cargar empleados', 'error');
        }
    } catch (error) {
        Utils.closeLoading();
        console.error('Error al cargar empleados:', error);
        tableEmpleados.clear().draw();
        Utils.showToast('Error de conexión al cargar empleados', 'error');
    }
}

/**
 * Inicializar DataTable para la tabla de empleados
 */
function initializeDataTable() {
    tableEmpleados = $("#table_empleados").DataTable({
        processing: false,
        serverSide: false,
        data: empleadosData,
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function(data, type, row) {
                    return `
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false" data-bs-toggle="tooltip" data-bs-placement="left" title="Acciones disponibles">
                                <i class="ri-more-fill align-middle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="#" class="dropdown-item" onclick="verEmpleado(${row.id})" data-bs-toggle="tooltip" data-bs-placement="left" title="Ver detalles del empleado">
                                        <i class="ri-eye-fill align-bottom me-2 text-muted"></i>Ver
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item" onclick="editarEmpleado(${row.id})" data-bs-toggle="tooltip" data-bs-placement="left" title="Editar información del empleado">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>Editar
                                    </a>
                                </li>${(!row.tiene_biometrico || row.tiene_biometrico == 0) ? `
                                <li>
                                    <a  class="dropdown-item" onclick="vincularBiometrico(${row.id})" data-bs-toggle="tooltip" data-bs-placement="left" title="Vincular empleado con dispositivo biométrico">
                                        <i class="ri-fingerprint-line align-bottom me-2 text-info"></i>Vincular Biométrico
                                    </a>
                                </li>` : ''}
                                <li>
                                    <a class="dropdown-item text-danger" onclick="eliminarEmpleado(${row.id})" data-bs-toggle="tooltip" data-bs-placement="left" title="Eliminar empleado permanentemente">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>Eliminar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    `;
                }
            },
            { data: "id" },
            { data: "nro_documento" },
            { 
                data: null,
                render: function(data, type, row) {
                    return `<strong>${row.nombre_completo || row.nombre + ' ' + row.apellido_paterno + ' ' + (row.apellido_materno || '')}</strong>`;
                }
            },
            { 
                data: "email",
                defaultContent: "-",
                render: function(data) {
                    return data || "-";
                }
            },
            { 
                data: "nombre_cargo",
                defaultContent: "-",
                render: function(data) {
                    return data || "-";
                }
            },
            { 
                data: "nombre_turno",
                defaultContent: "-",
                render: function(data) {
                    return data || "-";
                }
            },
            { 
                data: "gerencia_nombre",
                defaultContent: "-",
                render: function(data) {
                    return data || "-";
                }
            },
            {
                data: "fecha_ingreso",
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString('es-ES') : "-";
                }
            },
            {
                data: "tiene_biometrico",
                render: function(data, type, row) {
                    if (data == 1 || data === true) {
                        return `<span class="badge badge-soft-success">
                            <i class="ri-check-line me-1"></i>Vinculado
                        </span>`;
                    } else {
                        return `<span class="badge badge-soft-danger">
                            <i class="ri-close-line me-1"></i>No vinculado
                        </span>`;
                    }
                }
            },
            {
                data: "estado",
                render: function(data, type, row) {
                    const badgeClass = data == 1 ? "badge-soft-success" : "badge-soft-danger";
                    const text = data == 1 ? "Activo" : "Inactivo";
                    return `<span class="badge ${badgeClass}">${text}</span>`;
                }
            }
        ],
        dom: "Bfrtip",
        buttons: [
            {
                text: "Imprimir",
                extend: "print",
                title: "Lista de Empleados - CAFED",
                exportOptions: {
                    columns: [1, 2, 3, 4, 5, 6, 7, 8, 9, 10]
                }
            }
        ],
        responsive: true,
        autowidth: false,
        language: {
            sProcessing: "Procesando...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron empleados",
            sEmptyTable: "No hay empleados registrados",
            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            sLoadingRecords: "Cargando...",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            },
            buttons: {
                copyTitle: "Copiado al portapapeles",
                copySuccess: {
                    _: "%d líneas copiadas",
                    1: "1 línea copiada"
                }
            }
        },
        order: [[1, "desc"]],
        pageLength: 10
    });
}

/**
 * Inicializar event listeners
 */
function initializeEventListeners() {
    // Botón nuevo empleado
    $("#btn_nuevo_empleado").on("click", function() {
        window.location.href = getBaseUrl() + '/empleadosregistro';
    });

    // Botón filtrar
    $("#btn_filtrar").on("click", function() {
        filtrarEmpleados();
    });

    // Botón recargar/restablecer
    $("#btn_recargar").on("click", function() {
        recargarTabla();
    });
    
    // Formulario de vinculación biométrica
    $("#form_biometrico").on("submit", function(e) {
        e.preventDefault();
        guardarVinculacionBiometrico();
    });
    
    // Limpiar formulario al cerrar modal
    $("#modal_biometrico").on("hidden.bs.modal", function() {
        limpiarFormularioBiometrico();
    });
    
    // Cargar dispositivos cuando se abre el modal
    $("#modal_biometrico").on("show.bs.modal", function() {
        cargarDispositivosBiometricos();
    });
}

/**
 * Cargar dispositivos biométricos acAtivos para el select
 */
async function cargarDispositivosBiometricos() {
    try {
        const resultado = await API.get('biometrico/listarDispositivos', { sede_id: userInfo.sede_id });
        
        const $select = $('#device_id');
        $select.find('option:not(:first)').remove();
        
        if (resultado && resultado.ok && resultado.data && resultado.data.length > 0) {
            resultado.data.forEach(dispositivo => {
                $select.append(`<option value="${dispositivo.device_id}">${dispositivo.display_name || dispositivo.device_id}</option>`);
            });
        } else {
            $select.append('<option value="" disabled>No hay dispositivos disponibles</option>');
        }
    } catch (error) {
        console.error('Error al cargar dispositivos biométricos:', error);
        const $select = $('#device_id');
        $select.find('option:not(:first)').remove();
        $select.append('<option value="" disabled>Error al cargar dispositivos</option>');
    }
}

/**
 * Guardar vinculación biométrica
 */
async function guardarVinculacionBiometrico() {
    const empleado_id = $("#biometrico_empleado_id").val();
    const biometric_user_id = $("#biometric_user_id").val().trim();
    const device_id = $("#device_id").val().trim();
    
    if (!biometric_user_id || !device_id) {
        Utils.showToast("Debe completar todos los campos", "warning");
        return;
    }
    
    try {
        Utils.showLoading('Vinculando empleado con dispositivo biométrico...');
        
        const datos = {
            empleado_id: parseInt(empleado_id),
            sede_id: userInfo.sede_id,
            biometric_user_id: biometric_user_id,
            device_id: device_id,
            activo: 1,
            creado_por: userInfo.nombre_completo || 'admin'
        };
        
        const resultado = await API.post('empleados/vincularBiometrico', datos);
        
        Utils.closeLoading();
        
        if (resultado && resultado.ok) {
            $("#modal_biometrico").modal("hide");
            Utils.showAlert("Empleado vinculado exitosamente con el dispositivo biométrico", "success");
            cargarEmpleados(); // Recargar la tabla
        } else {
            Utils.showToast(resultado?.msj || 'Error al vincular el empleado', 'error');
        }
    } catch (error) {
        Utils.closeLoading();
        console.error('Error:', error);
        Utils.showToast('Error al vincular el empleado', 'error');
    }
}

/**
 * Limpiar formulario de vinculación biométrica
 */
function limpiarFormularioBiometrico() {
    $("#form_biometrico")[0].reset();
    $("#biometrico_empleado_id").val("");
    $("#biometrico_empleado_nombre").text("");
    $("#device_id").val(""); // Limpiar el select
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text("");
}

/**
 * Inicializar tooltips
 */
function initializeTooltips() {
    // Inicializar todos los tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Reinicializar tooltips para elementos dinámicos
 */
function reinitializeTooltips() {
    // Destruir tooltips existentes
    var existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    existingTooltips.forEach(function(element) {
        var tooltip = bootstrap.Tooltip.getInstance(element);
        if (tooltip) {
            tooltip.dispose();
        }
    });
    
    // Reinicializar tooltips
    initializeTooltips();
}

/**
 * Ver empleado
 */
function verEmpleado(id) {
    window.location.href = getBaseUrl() + '/empleados/registro?edit=' + id;
}

/**
 * Vincular empleado con dispositivo biométrico
 */
function vincularBiometrico(id) {
    console.log('vincularBiometrico llamado con ID:', id, 'Tipo:', typeof id);
    console.log('Total empleados en array:', empleadosData.length);
    console.log('IDs disponibles:', empleadosData.map(emp => ({ id: emp.id, tipo: typeof emp.id })));
    
    // Convertir ID a número para comparación
    const idNum = parseInt(id);
    
    // Buscar empleado comparando tanto como número como string
    const empleado = empleadosData.find(emp => {
        return emp.id == id || emp.id === id || emp.id === idNum || parseInt(emp.id) === idNum;
    });
    
    if (!empleado) {
        console.error('Empleado no encontrado. ID buscado:', id, 'Tipo:', typeof id);
        console.error('IDs en el array:', empleadosData.map(emp => emp.id));
        Utils.showToast('Empleado no encontrado. ID: ' + id, 'error');
        return;
    }
    
    console.log('Empleado encontrado:', empleado);
    
    // Llenar datos del empleado en el modal
    $("#biometrico_empleado_id").val(id);
    $("#biometrico_empleado_nombre").text(empleado.nombre_completo || `${empleado.nombre} ${empleado.apellido_paterno} ${empleado.apellido_materno || ''}`);
    
    // Limpiar formulario
    $("#biometric_user_id").val("");
    $("#device_id").val("");
    
    // Verificar que el modal existe
    const modalElement = document.getElementById('modal_biometrico');
    if (!modalElement) {
        console.error('Modal no encontrado en el DOM');
        Utils.showToast('Error: Modal no encontrado', 'error');
        return;
    }
    
    console.log('Abriendo modal...');
    
    // Intentar con Bootstrap 5 API primero
    try {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
        console.log('Modal abierto con Bootstrap 5 API');
    } catch (e) {
        // Si falla, intentar con jQuery
        console.log('Intentando con jQuery...', e);
        $("#modal_biometrico").modal("show");
    }
}

/**
 * Editar empleado
 */
function editarEmpleado(id) {
    window.location.href = getBaseUrl() + '/empleados/registro?edit=' + id;
}

/**
 * Eliminar empleado
 */
async function eliminarEmpleado(id) {
    const empleado = empleadosData.find(emp => emp.id === id);
    if (!empleado) return;
    
    const nombreCompleto = empleado.nombre_completo || `${empleado.nombre} ${empleado.apellido_paterno} ${empleado.apellido_materno || ''}`;

    const confirmacion = await Utils.showConfirm(
        `¿Eliminar a ${nombreCompleto}?`,
        'Esta acción cambiará el estado del empleado a inactivo',
        'warning'
    );
    
    if (!confirmacion) return;
    
    try {
        Utils.showLoading('Eliminando empleado...');
        
        const resultado = await API.delete(`empleados/delete?id=${id}&sede_id=${userInfo.sede_id}`);
        
        Utils.closeLoading();
        
        if (resultado && resultado.ok) {
            Utils.showAlert("Empleado eliminado exitosamente", "success");
            cargarEmpleados(); // Recargar la tabla
        } else {
            Utils.showToast(resultado?.msj || 'Error al eliminar', 'error');
        }
    } catch (error) {
        Utils.closeLoading();
        console.error('Error:', error);
        Utils.showToast('Error al eliminar el empleado', 'error');
    }
}

/**
 * Filtrar empleados por fecha, estado y unidad
 */
function filtrarEmpleados() {
    const fechaRango = $("#fecha_rango").val();
    
    if (!fechaRango || fechaRango.trim() === '') {
        Utils.showToast('Por favor selecciona un rango de fechas', 'warning');
        return;
    }
    
    // Separar fechas (flatpickr usa " to " como separador)
    const fechas = fechaRango.split(' to ');
    fechaInicio = fechas[0] ? fechas[0].trim() : '';
    fechaFin = fechas[1] ? fechas[1].trim() : (fechas[0] ? fechas[0].trim() : '');
    estadoFiltro = $("#estado_filtro").val();
    unidadFiltro = $("#unidad_filtro").val();
    
    cargarEmpleados();
}

/**
 * Obtener nombre de unidad por ID
 */
function getUnidadNombre(id) {
    const unidades = {
        '1': 'Gerencia General',
        '2': 'Recursos Humanos',
        '3': 'Contabilidad',
        '4': 'Ventas',
        '5': 'Marketing',
        '6': 'Tecnología',
        '7': 'Operaciones'
    };
    return unidades[id] || '';
}

/**
 * Recargar/Restablecer tabla
 */
function recargarTabla() {
    // Limpiar filtros
    $("#fecha_rango").val("");
    $("#estado_filtro").val("");
    $("#unidad_filtro").val("");
    
    // Restablecer variables de filtro
    fechaInicio = '';
    fechaFin = '';
    estadoFiltro = '';
    unidadFiltro = '';
    
    // Reinicializar flatpickr con fecha de hoy
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const formatoFecha = (fecha) => fecha.toISOString().split("T")[0];
    const fechaHoy = formatoFecha(hoy);
    if (typeof flatpickr !== 'undefined') {
        flatpickr("#fecha_rango").setDate([fechaHoy, fechaHoy]);
    }
    
    cargarEmpleados();
}

/**
 * Obtener URL base
 */
function getBaseUrl() {
    const path = window.location.pathname;
    const parts = path.split('/');
    const domainPath = parts.slice(0, -1).join('/');
    return window.location.origin + domainPath;
}

/**
 * Mostrar alerta
 */
function showAlert(message, type = "info") {
    Swal.fire({
        title: type === "success" ? "Éxito" : type === "error" ? "Error" : "Información",
        text: message,
        icon: type,
        confirmButtonText: "Aceptar",
        confirmButtonColor: type === "success" ? "#28a745" : type === "error" ? "#dc3545" : "#007bff"
    });
}
