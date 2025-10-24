/**
 * Sistema de Gestión de Empleados - CAFED
 * Control de Asistencia
 */

// Variables globales
let tableEmpleados;
let selectedEmpleados = [];
let fechaInicio = '';
let fechaFin = '';
let estadoFiltro = '';
let unidadFiltro = '';

// Inicialización cuando el documento esté listo
$(document).ready(function() {
    initializeDateRange();
    initializeDataTable();
    initializeEventListeners();
    initializeTooltips();
});

/**
 * Datos de ejemplo para empleados
 */
const empleadosData = [
    {
        id: 1,
        nro_documento: "12345678",
        nombre: "Juan Carlos",
        apellido_paterno: "Pérez",
        apellido_materno: "García",
        email: "juan.perez@cafed.com",
        telefono: "987654321",
        fecha_nacimiento: "1985-03-15",
        fecha_ingreso: "2020-01-15",
        fecha_cese: null,
        cargo: "Gerente General",
        unidad_organizacional: "Gerencia General",
        sistema_pension: "AFP Integra",
        regimen_laboral: "Contrato Indefinido",
        tipo_trabajador: "Empleado",
        nivel_remunerativo: "Nivel V",
        banco: "Banco de Crédito del Perú",
        numero_cuenta: "1234567890123456",
        numero_cci: "00212345678901234567",
        cuspp: "12345678901",
        airhsp: "AIR123456",
        codigo_reloj: "REL001",
        observaciones: "Empleado con excelente desempeño",
        estado: 1,
        fecha_creacion: "2020-01-15"
    },
    {
        id: 2,
        nro_documento: "87654321",
        nombre: "María Elena",
        apellido_paterno: "Rodríguez",
        apellido_materno: "López",
        email: "maria.rodriguez@cafed.com",
        telefono: "912345678",
        fecha_nacimiento: "1990-07-22",
        fecha_ingreso: "2021-03-10",
        fecha_cese: null,
        cargo: "Desarrolladora Senior",
        unidad_organizacional: "Tecnología",
        sistema_pension: "AFP Prima",
        regimen_laboral: "Contrato Indefinido",
        tipo_trabajador: "Empleado",
        nivel_remunerativo: "Nivel IV",
        banco: "Banco Interbank",
        numero_cuenta: "9876543210987654",
        numero_cci: "00398765432109876543",
        cuspp: "98765432109",
        airhsp: "AIR987654",
        codigo_reloj: "REL002",
        observaciones: "Especialista en desarrollo web",
        estado: 1,
        fecha_creacion: "2021-03-10"
    },
    {
        id: 3,
        nro_documento: "11223344",
        nombre: "Carlos Alberto",
        apellido_paterno: "Martínez",
        apellido_materno: "Silva",
        email: "carlos.martinez@cafed.com",
        telefono: "955667788",
        fecha_nacimiento: "1988-11-08",
        fecha_ingreso: "2019-06-01",
        fecha_cese: null,
        cargo: "Analista de Recursos Humanos",
        unidad_organizacional: "Recursos Humanos",
        sistema_pension: "ONP",
        regimen_laboral: "Contrato Indefinido",
        tipo_trabajador: "Empleado",
        nivel_remunerativo: "Nivel III",
        banco: "Banco BBVA",
        numero_cuenta: "5566778899001122",
        numero_cci: "01155667788990011223",
        cuspp: "55667788990",
        airhsp: "AIR556677",
        codigo_reloj: "REL003",
        observaciones: "Experto en gestión de personal",
        estado: 1,
        fecha_creacion: "2019-06-01"
    },
    {
        id: 4,
        nro_documento: "55667788",
        nombre: "Ana Lucía",
        apellido_paterno: "Torres",
        apellido_materno: "Vargas",
        email: "ana.torres@cafed.com",
        telefono: "944556677",
        fecha_nacimiento: "1992-04-12",
        fecha_ingreso: "2022-08-20",
        fecha_cese: null,
        cargo: "Contadora",
        unidad_organizacional: "Contabilidad",
        sistema_pension: "AFP Profuturo",
        regimen_laboral: "Contrato Temporal",
        tipo_trabajador: "Contratado",
        nivel_remunerativo: "Nivel II",
        banco: "Banco Scotiabank",
        numero_cuenta: "3344556677889900",
        numero_cci: "00933445566778899001",
        cuspp: "33445566778",
        airhsp: "AIR334455",
        codigo_reloj: "REL004",
        observaciones: "Contadora especializada en auditoría",
        estado: 1,
        fecha_creacion: "2022-08-20"
    },
    {
        id: 5,
        nro_documento: "99887766",
        nombre: "Luis Fernando",
        apellido_paterno: "Herrera",
        apellido_materno: "Mendoza",
        email: "luis.herrera@cafed.com",
        telefono: "933445566",
        fecha_nacimiento: "1987-09-30",
        fecha_ingreso: "2020-11-05",
        fecha_cese: "2023-12-31",
        cargo: "Ejecutivo de Ventas",
        unidad_organizacional: "Ventas",
        sistema_pension: "AFP Habitat",
        regimen_laboral: "Contrato Indefinido",
        tipo_trabajador: "Empleado",
        nivel_remunerativo: "Nivel III",
        banco: "Banco Pichincha",
        numero_cuenta: "7788990011223344",
        numero_cci: "03477889900112233445",
        cuspp: "77889900112",
        airhsp: "AIR778899",
        codigo_reloj: "REL005",
        observaciones: "Empleado cesado por finalización de contrato",
        estado: 0,
        fecha_creacion: "2020-11-05"
    }
];

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
                                </li>
                                <li>
                                    <a href="#" class="dropdown-item text-danger" onclick="eliminarEmpleado(${row.id})" data-bs-toggle="tooltip" data-bs-placement="left" title="Eliminar empleado permanentemente">
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
                    return `<strong>${row.nombre} ${row.apellido_paterno} ${row.apellido_materno || ''}</strong>`;
                }
            },
            { data: "email" },
            { data: "cargo" },
            { data: "unidad_organizacional" },
            {
                data: "fecha_ingreso",
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString('es-ES') : "-";
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
                    columns: [1, 2, 3, 4, 5, 6, 7, 8]
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
    const empleado = empleadosData.find(emp => emp.id === id);
    if (empleado) {
        // Crear contenido del modal de vista
        const modalContent = `
            <div class="modal fade" id="modal_ver_empleado" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="ri-eye-line me-2"></i>Ver Empleado
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>N° Documento:</strong> ${empleado.nro_documento}
                                </div>
                                <div class="col-md-6">
                                    <strong>Nombres Completos:</strong> ${empleado.nombre} ${empleado.apellido_paterno} ${empleado.apellido_materno || ''}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Email:</strong> ${empleado.email}
                                </div>
                                <div class="col-md-6">
                                    <strong>Teléfono:</strong> ${empleado.telefono || 'No registrado'}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Cargo:</strong> ${empleado.cargo}
                                </div>
                                <div class="col-md-6">
                                    <strong>Unidad Organizacional:</strong> ${empleado.unidad_organizacional}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Fecha Ingreso:</strong> ${new Date(empleado.fecha_ingreso).toLocaleDateString('es-ES')}
                                </div>
                                <div class="col-md-6">
                                    <strong>Fecha Cese:</strong> ${empleado.fecha_cese ? new Date(empleado.fecha_cese).toLocaleDateString('es-ES') : 'Activo'}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Régimen Laboral:</strong> ${empleado.regimen_laboral}
                                </div>
                                <div class="col-md-6">
                                    <strong>Tipo Trabajador:</strong> ${empleado.tipo_trabajador}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Sistema Pensión:</strong> ${empleado.sistema_pension}
                                </div>
                                <div class="col-md-6">
                                    <strong>Nivel Remunerativo:</strong> ${empleado.nivel_remunerativo}
                                </div>
                            </div>
                            ${empleado.observaciones ? `
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Observaciones:</strong><br>
                                    ${empleado.observaciones}
                                </div>
                            </div>
                            ` : ''}
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Agregar modal al DOM y mostrarlo
        $('body').append(modalContent);
        $('#modal_ver_empleado').modal('show');

        // Limpiar modal cuando se cierre
        $('#modal_ver_empleado').on('hidden.bs.modal', function() {
            $(this).remove();
        });
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
function eliminarEmpleado(id) {
    const empleado = empleadosData.find(emp => emp.id === id);
    const nombreCompleto = `${empleado.nombre} ${empleado.apellido_paterno} ${empleado.apellido_materno || ''}`;

    Swal.fire({
        title: "¿Eliminar empleado?",
        html: `Esta acción eliminará permanentemente a <strong>${nombreCompleto}</strong><br>Esta acción no se puede deshacer`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            // Simular eliminación
            setTimeout(function() {
                // En un caso real, aquí se haría la llamada AJAX para eliminar
                tableEmpleados.clear().rows.add(empleadosData).draw();
                
                // Reinicializar tooltips para los nuevos elementos
                setTimeout(function() {
                    reinitializeTooltips();
                }, 100);
                
                showAlert("Empleado eliminado exitosamente", "success");
            }, 1000);
        }
    });
}

/**
 * Filtrar empleados por fecha, estado y unidad
 */
function filtrarEmpleados() {
    const fechas = $("#fecha_rango").val().split(/ a | to /);
    if (!fechas[0]) {
        showAlert('Por favor selecciona un rango de fechas', 'warning');
        return;
    }

    fechaInicio = fechas[0].trim();
    fechaFin = (fechas[1] || fechas[0]).trim();
    estadoFiltro = $("#estado_filtro").val();
    unidadFiltro = $("#unidad_filtro").val();

    // Filtrar datos localmente
    let datosFiltrados = empleadosData.filter(empleado => {
        const fechaEmpleado = new Date(empleado.fecha_ingreso);
        const fechaInicioDate = new Date(fechaInicio);
        const fechaFinDate = new Date(fechaFin);
        
        const cumpleFecha = fechaEmpleado >= fechaInicioDate && fechaEmpleado <= fechaFinDate;
        const cumpleEstado = estadoFiltro === '' || empleado.estado.toString() === estadoFiltro;
        const cumpleUnidad = unidadFiltro === '' || empleado.unidad_organizacional === getUnidadNombre(unidadFiltro);
        
        return cumpleFecha && cumpleEstado && cumpleUnidad;
    });

    // Recargar tabla con datos filtrados
    tableEmpleados.clear().rows.add(datosFiltrados).draw();
    
    // Reinicializar tooltips para los nuevos elementos
    setTimeout(function() {
        reinitializeTooltips();
    }, 100);
    
    showAlert(`Se encontraron ${datosFiltrados.length} empleado(s)`, 'success');
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
    
    // Recargar tabla con todos los datos
    tableEmpleados.clear().rows.add(empleadosData).draw();
    
    // Reinicializar tooltips para los nuevos elementos
    setTimeout(function() {
        reinitializeTooltips();
    }, 100);
    
    // Mostrar mensaje de confirmación
    showAlert('Tabla recargada exitosamente', 'success');
}

/**
 * Obtener URL base
 */
function getBaseUrl() {
    return window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/');
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
