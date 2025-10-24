/**
 * Sistema de Gestión de Cargos - CAFED
 * Consumo de API REST
 */

// Variables globales
let tableCargos;
let cargosData = [];
let userInfo = null;
let isInitialLoad = true;
// Flag para controlar la primera carga

// Inicialización cuando el documento esté listo
$(document).ready(function () { // Verificar autenticación
    if (!Auth.requireAuth()) {
        return;
    }

    // Obtener información del usuario
    userInfo = Auth.getUserInfo();

    // Inicializar componentes
    initializeFlatpickr();
    initializeDataTable();
    initializeEventListeners();
    initializeTooltips();

    // Cargar datos de cargos
    cargarCargos();
});

/**
 * Cargar cargos desde la API
 */
async function cargarCargos() {
    try { // Mostrar indicador de carga en la tabla
        if (isInitialLoad) { // Mostrar el processing overlay manualmente
            $(".dataTables_processing").show();
        }

        // Hacer petición a la API
        const resultado = await API.get("cargos/getAll", {sede_id: userInfo.sede_id});

        // Ocultar processing después de la carga
        if (isInitialLoad) {
            $(".dataTables_processing").hide();
            isInitialLoad = false;
        }

        if (resultado && resultado.ok) {
            cargosData = resultado.data;

            // Cargar datos en la tabla
            tableCargos.clear().rows.add(cargosData).draw();

            // Reinicializar tooltips
            setTimeout(() => reinitializeTooltips(), 100);

            /*console.log(`${cargosData.length} cargos cargados`);*/

        } else { // Mostrar toast de error
            tableCargos.clear().draw();
            Utils.showToast(resultado ?. msj || "Error al cargar los cargos", "error");
        }
    } catch (error) { // Ocultar processing en caso de error
        if (isInitialLoad) {
            $(".dataTables_processing").hide();
            isInitialLoad = false;
        }

        console.error("Error al cargar cargos:", error);
        tableCargos.clear().draw();
        Utils.showToast("Error de conexión al cargar los cargos", "error");
    }
}

/**
 * Filtrar cargos por fecha y estado
 */
function filtrarCargos() {
    try {
        const fechas = $("#fecha_rango").val().split(/ a | to /);
        const estado = $("#estado_filtro").val();

        // Si no hay fechas seleccionadas, mostrar toast
        if (! fechas[0] || fechas[0].trim() === "") {
            Utils.showToast("Por favor selecciona un rango de fechas", "warning");
            return;
        }

        const fechaInicio = fechas[0].trim();
        const fechaFin = fechas[1] ? fechas[1].trim() : fechaInicio;

        console.log("Filtrando:", {fechaInicio, fechaFin, estado});

        // Filtrar los datos cargados
        let datosFiltrados = cargosData;

        // Filtrar por rango de fechas
        if (fechaInicio && fechaFin) {
            datosFiltrados = datosFiltrados.filter((cargo) => {
                const fechaCreacion = cargo.fecha_creacion.split(" ")[0]; // Obtener solo la fecha (YYYY-MM-DD)
                return fechaCreacion >= fechaInicio && fechaCreacion <= fechaFin;
            });
        }

        // Filtrar por estado si se seleccionó uno
        if (estado !== "") {
            datosFiltrados = datosFiltrados.filter((cargo) => cargo.estado == estado);
        }

        // Actualizar tabla con datos filtrados
        tableCargos.clear().rows.add(datosFiltrados).draw();

        console.log(`${
            datosFiltrados.length
        } cargos filtrados de ${
            cargosData.length
        } totales`);

        // Reinicializar tooltips
        setTimeout(() => reinitializeTooltips(), 100);
    } catch (error) {
        console.error("Error al filtrar cargos:", error);
        Utils.showToast("Error al filtrar los datos", "error");
    }
}

/**
 * Inicializar Flatpickr para el selector de fechas
 */
function initializeFlatpickr() {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0); // Evitar desbordes de hora

    const formatoFecha = (fecha) => fecha.toISOString().split("T")[0];

    // Configurar para que el día de hoy esté seleccionado
    const fechaHoy = formatoFecha(hoy);

    flatpickr("#fecha_rango", {
        mode: "range",
        dateFormat: "Y-m-d",
        locale: "es",
        defaultDate: [
            fechaHoy, fechaHoy
        ], // Mismo día (hoy)
    });
}

/**
 * Inicializar DataTable
 */
function initializeDataTable() {
    tableCargos = $("#table_cargos").DataTable({
        processing: true,
        serverSide: false,
        data: [],
        columns: [
            {
                data: null,
                orderable: false,
                searchable: false,
                className: "text-center",
                render: function (data, type, row) {
                    return `
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" 
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ri-more-fill align-middle"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" onclick="editarCargo(${
                        row.id
                    })">
                                        <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>Editar
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item text-danger" onclick="eliminarCargo(${
                        row.id
                    })">
                                        <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>Eliminar
                                    </a>
                                </li>
                            </ul>
                        </div>
                    `;
                }
            },
            {
                data: "nombre_cargo",
                defaultContent: "N/A",
                render: function (data) {
                    return data ? `<strong>${data}</strong>` : '<span class="text-muted">N/A</span>';
                }
            },
            {
                data: "descripcion",
                defaultContent: "-",
                render: function (data) {
                    if (! data || data.trim() === "") 
                        return '<span class="text-muted">-</span>';
                    
                    return data.length > 50 ? data.substring(0, 150) + "" : data;
                }
            },

            {
                data: "salario_base",
                defaultContent: "0.00",
                render: function (data) {
                    return data ? Utils.formatearMoneda(data) : '<span class="text-muted">S/. 0.00</span>';
                }
            }, {
                data: "estado",
                defaultContent: "0",
                render: function (data) {
                    const badgeClass = data == 1 ? "badge-soft-success" : "badge-soft-danger";
                    const text = data == 1 ? "Activo" : "Inactivo";
                    return `<span class="badge ${badgeClass}">${text}</span>`;
                }
            }, {
                data: "fecha_creacion",
                defaultContent: "-",
                render: function (data) {
                    return data ? Utils.formatearFecha(data) : '<span class="text-muted">-</span>';
                }
            },
        ],
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' + '<"row"<"col-sm-12"tr>>' + '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        buttons: [
            {
                extend: "copy",
                text: '<i class="ri-file-copy-line"></i>',
                titleAttr: "Copiar",
                className: "btn btn-soft-secondary btn-sm",
                exportOptions: {
                    columns: [
                        1,
                        2,
                        3,
                        4,
                        5
                    ]
                }
            },
            /*  {
                extend: 'excel',
                text: '<i class="ri-file-excel-line"></i>',
                titleAttr: 'Exportar a Excel',
                className: 'btn btn-soft-success btn-sm',
                title: 'Cargos-CAFED',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'pdf',
                text: '<i class="ri-file-pdf-line"></i>',
                titleAttr: 'Exportar a PDF',
                className: 'btn btn-soft-danger btn-sm',
                title: 'Cargos-CAFED',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            },
            {
                extend: 'print',
                text: '<i class="ri-printer-line"></i>',
                titleAttr: 'Imprimir',
                className: 'btn btn-soft-info btn-sm',
                title: 'Lista de Cargos - CAFED',
                exportOptions: {
                    columns: [1, 2, 3, 4, 5]
                }
            } */
        ],
        responsive: true,
        autoWidth: false,
        language: {
            processing: "Cargando datos...",
            sProcessing: "Cargando datos...",
            sLengthMenu: "Mostrar _MENU_ registros",
            sZeroRecords: "No se encontraron cargos",
            sEmptyTable: "No hay cargos registrados",
            sInfo: "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
            sInfoEmpty: "Mostrando registros del 0 al 0 de un total de 0 registros",
            sInfoFiltered: "(filtrado de un total de _MAX_ registros)",
            sSearch: "Buscar:",
            sLoadingRecords: "Cargando datos...",
            oPaginate: {
                sFirst: "Primero",
                sLast: "Último",
                sNext: "Siguiente",
                sPrevious: "Anterior"
            }
        },
        pageLength: 10
    });
}

/**
 * Inicializar event listeners
 */
function initializeEventListeners() { // Botón nuevo cargo
    $("#btn_nuevo_cargo").on("click", function () {
        abrirModalNuevoCargo();
    });

    // Formulario de cargo
    $("#form_cargo").on("submit", function (e) {
        e.preventDefault();
        guardarCargo();
    });

    // Botón filtrar
    $("#btn_filtrar").on("click", function () {
        filtrarCargos();
    });

    // Botón recargar (limpia filtros y recarga todo)
    $("#btn_recargar").on("click", function () { // Limpiar filtros
        $("#estado_filtro").val("");

        // Reinicializar flatpickr con fecha de hoy
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        const formatoFecha = (fecha) => fecha.toISOString().split("T")[0];
        const fechaHoy = formatoFecha(hoy);

        flatpickr("#fecha_rango").setDate([fechaHoy, fechaHoy]);

        // Recargar datos
        isInitialLoad = false;
        cargarCargos();
    });

    // Limpiar formulario al cerrar modal
    $("#modal_cargo").on("hidden.bs.modal", function () {
        limpiarFormulario();
    });
}

/**
 * Inicializar tooltips
 */
function initializeTooltips() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Reinicializar tooltips
 */
function reinitializeTooltips() {
    var existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    existingTooltips.forEach(function (element) {
        var tooltip = bootstrap.Tooltip.getInstance(element);
        if (tooltip) {
            tooltip.dispose();
        }
    });
    initializeTooltips();
}

/**
 * Abrir modal para nuevo cargo
 */
function abrirModalNuevoCargo() {
    limpiarFormulario();
    $("#modal_title").text("Nuevo Cargo");
    $("#btn_guardar_text").text("Guardar");
    $("#cargo_id").val("");
    $("#modal_cargo").modal("show");
}

/**
 * Editar cargo
 */
async function editarCargo(id) { /* console.log('Iniciando editarCargo con ID:', id); */

    try {
        /* console.log('Iniciando editarCargo con ID:', id);
        console.log('UserInfo:', userInfo); */

        // Verificar autenticación antes de proceder
        if (!Auth.isAuthenticated()) {
            console.error("Usuario no autenticado");
            Auth.requireAuth();
            return;
        }

        const resultado = await API.get("cargos/getById", {
            id: id,
            sede_id: userInfo.sede_id
        });

        console.log("Resultado de la API:", resultado);

        if (resultado && resultado.ok && resultado.data) {
            const cargo = resultado.data;
            /* console.log('Datos del cargo a editar:', cargo); */
            // Llenar formulario
            $("#cargo_id").val(cargo.id);
            $("#nombre_cargo").val(cargo.nombre_cargo);
            $("#descripcion").val(cargo.descripcion);
            $("#salario_base").val(cargo.salario_base);
            $("#modal_title").text("Editar Cargo");
            $("#btn_guardar_text").text("Actualizar");
            $("#modal_cargo").modal("show");
        } else {
            console.error("Error en respuesta de API:", resultado);
            Utils.showToast(resultado ?. msj || "Error al cargar los datos del cargo", "error");
        }
    } catch (error) {
        console.error("Error al editar cargo:", error);
        Utils.showToast("Error al cargar los datos", "error");
    }
}

/**
 * Guardar cargo (crear o actualizar)
 */
async function guardarCargo() {
    console.log("Iniciando guardarCargo...");

    // Verificar autenticación antes de proceder
    if (!Auth.isAuthenticated()) {
        console.error("Usuario no autenticado en guardarCargo");
        Auth.requireAuth();
        return;
    }

    // Validar formulario
    if (! validarFormulario()) {
        console.log("Formulario no válido");
        return;
    }

    const cargoId = $("#cargo_id").val();
    const isEdit = cargoId !== "";
    console.log("Modo:", isEdit ? "Editar" : "Crear", "ID:", cargoId);

    // Preparar datos
    const salarioBase = $("#salario_base").val().trim();
    const datos = {
        sede_id: userInfo.sede_id,
        nombre_cargo: $("#nombre_cargo").val().trim(),
        descripcion: $("#descripcion").val().trim() || null,
        salario_base: salarioBase !== "" ? parseFloat(salarioBase) : null,
        estado: 1 // Siempre activo por defecto
    };

    if (isEdit) {
        datos.id = parseInt(cargoId);
        datos.modificado_por = userInfo.nombre_completo || "admin";
    } else {
        datos.creado_por = userInfo.nombre_completo || "admin";
    }

    console.log("Datos a enviar:", datos);

    try {
        Utils.showLoading(isEdit ? "Actualizando cargo..." : "Guardando cargo...");

        const endpoint = isEdit ? "cargos/update" : "cargos/register";
        console.log("Enviando a endpoint:", endpoint);

        const resultado = await API.post(endpoint, datos);
        console.log("Resultado del POST:", resultado);

        Utils.closeLoading();

        if (resultado && resultado.ok) {
            $("#modal_cargo").modal("hide");

            // Recargar tabla
            await cargarCargos();

            // Éxito con SweetAlert
            Utils.showAlert(isEdit ? "Cargo actualizado exitosamente" : "Cargo registrado exitosamente", "success");
        } else { // Error con Toast
            Utils.showToast(resultado ?. msj || "Error al guardar el cargo", "error");
        }
    } catch (error) {
        Utils.closeLoading();
        console.error("Error al guardar cargo:", error);
        Utils.showToast("Error al procesar la solicitud", "error");
    }
}

/**
 * Eliminar cargo
 */
async function eliminarCargo(id) { // Confirmación con SweetAlert
    const result = await Swal.fire({
        title: "¿Eliminar cargo?",
        text: "Esta acción no se puede deshacer",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    });

    if (result.isConfirmed) {
        try {
            const resultado = await API.post("cargos/delete", {
                id: id,
                sede_id: userInfo.sede_id,
                modificado_por: userInfo.nombre_completo || "admin"
            });

            if (resultado && resultado.ok) { // Recargar tabla
                await cargarCargos();

                // Éxito con SweetAlert
                Utils.showAlert("Cargo eliminado exitosamente", "success");
            } else { // Error con Toast
                Utils.showToast(resultado ?. msj || "Error al eliminar el cargo", "error");
            }
        } catch (error) {
            console.error("Error al eliminar cargo:", error);
            Utils.showToast("Error al procesar la solicitud", "error");
        }
    }
}

/**
 * Validar formulario completo
 */
function validarFormulario() {
    let isValid = true;

    // Limpiar errores previos
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text("");

    // Validar nombre del cargo
    const nombreCargo = $("#nombre_cargo").val().trim();
    if (nombreCargo === "") {
        $("#nombre_cargo").addClass("is-invalid");
        $("#nombre_cargo_error").text("El nombre del cargo es obligatorio");
        isValid = false;
    }

    // Validar salario base (opcional)
    const salarioBase = $("#salario_base").val();
    if (salarioBase !== "" && parseFloat(salarioBase) < 0) {
        $("#salario_base").addClass("is-invalid");
        $("#salario_base_error").text("El salario base debe ser mayor o igual a 0");
        isValid = false;
    }

    return isValid;
}

/**
 * Limpiar formulario
 */
function limpiarFormulario() {
    $("#form_cargo")[0].reset();
    $("#cargo_id").val("");
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text("");
    $("#form_cargo input, #form_cargo select, #form_cargo textarea").prop("disabled", false);
    $("#btn_guardar").show();
}
