/**
 * Sistema de Registro de Empleados - CAFED
 * Control de Asistencia
 */

// Variables globales
let isEditMode = false;
let empleadoId = null;

// Inicialización cuando el documento esté listo
$(document).ready(function() {
    initializeEventListeners();
    initializeFormValidation();
    initializeTooltips();
    initializeImagePreview();
    checkEditMode();
});

/**
 * Verificar si estamos en modo edición
 */
function checkEditMode() {
    const urlParams = new URLSearchParams(window.location.search);
    const editId = urlParams.get('edit');
    
    if (editId) {
        isEditMode = true;
        empleadoId = editId;
        $("#btn_guardar_text").text("Actualizar Empleado");
        $("h4").text("Editar Empleado");
        loadEmpleadoData(editId);
    }
}

/**
 * Cargar datos del empleado para edición
 */
function loadEmpleadoData(id) {
    // Simular carga de datos (en un caso real, esto sería una llamada AJAX)
    const empleadoEjemplo = {
        id: id,
        tipo_documento: "1",
        nro_documento: "12345678",
        nombre: "Juan Carlos",
        apellido_paterno: "Pérez",
        apellido_materno: "García",
        email: "juan.perez@cafed.com",
        telefono: "987654321",
        fecha_nacimiento: "1985-03-15",
        fecha_ingreso: "2020-01-15",
        fecha_cese: "",
        unidad_organizacional: "1",
        cargo: "1",
        regimen_laboral: "1",
        tipo_trabajador: "1",
        nivel_remunerativo: "1",
        sistema_pension: "1",
        banco: "1",
        numero_cuenta: "1234567890123456",
        numero_cci: "00212345678901234567",
        cuspp: "12345678901",
        airhsp: "AIR123456",
        codigo_reloj: "REL001",
        observaciones: "Empleado con excelente desempeño",
        estado: "1",
        // Direcciones
        direccion_actual: "Av. Javier Prado Este 1234, San Isidro",
        referencia_actual: "Cerca del centro comercial Real Plaza",
        ubigeo_actual: "2",
        es_principal_actual: true,
        direccion_reniec: "Jr. Las Flores 567, Miraflores",
        referencia_reniec: "Frente al parque Kennedy",
        ubigeo_reniec: "1",
        es_principal_reniec: false,
        direccion_laboral: "Av. Arequipa 456, Miraflores",
        referencia_laboral: "Oficina en el 3er piso",
        ubigeo_laboral: "1",
        es_principal_laboral: false
    };

    // Llenar formulario
    $("#empleado_id").val(empleadoEjemplo.id);
    $("#tipo_documento").val(empleadoEjemplo.tipo_documento);
    $("#nro_documento").val(empleadoEjemplo.nro_documento);
    $("#nombre").val(empleadoEjemplo.nombre);
    $("#apellido_paterno").val(empleadoEjemplo.apellido_paterno);
    $("#apellido_materno").val(empleadoEjemplo.apellido_materno);
    $("#email").val(empleadoEjemplo.email);
    $("#telefono").val(empleadoEjemplo.telefono);
    $("#fecha_nacimiento").val(empleadoEjemplo.fecha_nacimiento);
    $("#fecha_ingreso").val(empleadoEjemplo.fecha_ingreso);
    $("#fecha_cese").val(empleadoEjemplo.fecha_cese);
    $("#unidad_organizacional").val(empleadoEjemplo.unidad_organizacional);
    $("#cargo").val(empleadoEjemplo.cargo);
    $("#regimen_laboral").val(empleadoEjemplo.regimen_laboral);
    $("#tipo_trabajador").val(empleadoEjemplo.tipo_trabajador);
    $("#nivel_remunerativo").val(empleadoEjemplo.nivel_remunerativo);
    $("#sistema_pension").val(empleadoEjemplo.sistema_pension);
    $("#banco").val(empleadoEjemplo.banco);
    $("#numero_cuenta").val(empleadoEjemplo.numero_cuenta);
    $("#numero_cci").val(empleadoEjemplo.numero_cci);
    $("#cuspp").val(empleadoEjemplo.cuspp);
    $("#airhsp").val(empleadoEjemplo.airhsp);
    $("#codigo_reloj").val(empleadoEjemplo.codigo_reloj);
    $("#observaciones").val(empleadoEjemplo.observaciones);
    $("#estado").val(empleadoEjemplo.estado);
    
    // Llenar direcciones
    $("#direccion_actual").val(empleadoEjemplo.direccion_actual);
    $("#referencia_actual").val(empleadoEjemplo.referencia_actual);
    $("#ubigeo_actual").val(empleadoEjemplo.ubigeo_actual);
    $("#es_principal_actual").prop("checked", empleadoEjemplo.es_principal_actual);
    
    $("#direccion_reniec").val(empleadoEjemplo.direccion_reniec);
    $("#referencia_reniec").val(empleadoEjemplo.referencia_reniec);
    $("#ubigeo_reniec").val(empleadoEjemplo.ubigeo_reniec);
    $("#es_principal_reniec").prop("checked", empleadoEjemplo.es_principal_reniec);
    
    $("#direccion_laboral").val(empleadoEjemplo.direccion_laboral);
    $("#referencia_laboral").val(empleadoEjemplo.referencia_laboral);
    $("#ubigeo_laboral").val(empleadoEjemplo.ubigeo_laboral);
    $("#es_principal_laboral").prop("checked", empleadoEjemplo.es_principal_laboral);
}

/**
 * Inicializar event listeners
 */
function initializeEventListeners() {
    // Formulario de empleado
    $("#form_empleado").on("submit", function(e) {
        e.preventDefault();
        guardarEmpleado();
    });

    // Botón limpiar
    $("#btn_limpiar").on("click", function() {
        limpiarFormulario();
    });

    // Botón cancelar/volver
    $("#btn_cancelar").on("click", function() {
        window.location.href = getBaseUrl() + '/empleados';
    });

    // Manejar switch de "No paga seguro"
    $("#no_paga_seguro").on("change", function() {
        const sistemaPension = $("#sistema_pension");
        if ($(this).is(":checked")) {
            sistemaPension.prop("disabled", true).val("");
        } else {
            sistemaPension.prop("disabled", false);
        }
    });

    // Manejar switches de dirección principal (solo una puede estar marcada)
    $('input[name^="es_principal_"]').on("change", function() {
        if ($(this).is(":checked")) {
            // Desmarcar los otros switches
            $('input[name^="es_principal_"]').not(this).prop("checked", false);
        }
    });
}

/**
 * Inicializar preview de imagen
 */
function initializeImagePreview() {
    // Manejar cambio de archivo de foto
    $("#foto_empleado").on("change", function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validar tipo de archivo
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!validTypes.includes(file.type)) {
                showAlert("Por favor seleccione una imagen válida (JPG, JPEG o PNG)", "error");
                $(this).val('');
                return;
            }
            
            // Validar tamaño (2MB máximo)
            const maxSize = 2 * 1024 * 1024; // 2MB en bytes
            if (file.size > maxSize) {
                showAlert("La imagen no debe superar los 2MB", "error");
                $(this).val('');
                return;
            }
            
            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                $("#preview_foto").attr("src", e.target.result);
                $("#btn_eliminar_foto").show();
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Botón eliminar foto
    $("#btn_eliminar_foto").on("click", function() {
        $("#foto_empleado").val('');
        $("#preview_foto").attr("src", getBaseUrl() + "/assets/images/users/user-dummy-img.jpg");
        $(this).hide();
    });
}

/**
 * Inicializar validaciones del formulario
 */
function initializeFormValidation() {
    // Validación en tiempo real para campos obligatorios
    $("#tipo_documento").on("change", function() {
        validarCampo("tipo_documento", "Debe seleccionar un tipo de documento");
    });

    $("#nro_documento").on("blur", function() {
        validarCampo("nro_documento", "El número de documento es requerido");
    });

    $("#nombre").on("blur", function() {
        validarCampo("nombre", "Los nombres son requeridos");
    });

    $("#apellido_paterno").on("blur", function() {
        validarCampo("apellido_paterno", "El apellido paterno es requerido");
    });

    $("#email").on("blur", function() {
        const email = $(this).val().trim();
        if (email === "") {
            validarCampo("email", "El email es requerido");
        } else if (!isValidEmail(email)) {
            $("#email").addClass("is-invalid");
            $("#email_error").text("Ingrese un email válido");
        } else {
            $("#email").removeClass("is-invalid");
            $("#email_error").text("");
        }
    });

    $("#fecha_ingreso").on("change", function() {
        validarCampo("fecha_ingreso", "La fecha de ingreso es requerida");
    });

    $("#unidad_organizacional").on("change", function() {
        validarCampo("unidad_organizacional", "Debe seleccionar una unidad organizacional");
    });

    $("#cargo").on("change", function() {
        validarCampo("cargo", "Debe seleccionar un cargo");
    });

    $("#regimen_laboral").on("change", function() {
        validarCampo("regimen_laboral", "Debe seleccionar un régimen laboral");
    });

    $("#tipo_trabajador").on("change", function() {
        validarCampo("tipo_trabajador", "Debe seleccionar un tipo de trabajador");
    });

    $("#nivel_remunerativo").on("change", function() {
        validarCampo("nivel_remunerativo", "Debe seleccionar un nivel remunerativo");
    });

    $("#estado").on("change", function() {
        validarCampo("estado", "Debe seleccionar un estado");
    });

    // Validaciones para direcciones
    $("#direccion_actual").on("blur", function() {
        validarCampo("direccion_actual", "La dirección actual es requerida");
    });

    // Validaciones para nuevos campos
    $("#sexo").on("change", function() {
        validarCampo("sexo", "Debe seleccionar un sexo");
    });

    $("#estado_civil").on("change", function() {
        validarCampo("estado_civil", "Debe seleccionar un estado civil");
    });

    $("#profesion").on("change", function() {
        validarCampo("profesion", "Debe seleccionar una profesión");
    });

    $("#grado_institucion").on("change", function() {
        validarCampo("grado_institucion", "Debe seleccionar un grado de institución");
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
 * Guardar empleado (crear o actualizar)
 */
function guardarEmpleado() {
    if (!validarFormulario()) {
        return;
    }

    const formData = new FormData($("#form_empleado")[0]);
    
    // Mostrar loading
    const btnGuardar = $("#btn_guardar");
    const originalText = btnGuardar.html();
    btnGuardar.prop("disabled", true).html('<i class="ri-loader-4-line me-1"></i>Guardando...');

    // Simular guardado (en un caso real, esto sería una llamada AJAX)
    setTimeout(function() {
        btnGuardar.prop("disabled", false).html(originalText);
        
        showAlert(
            isEditMode ? "Empleado actualizado exitosamente" : "Empleado registrado exitosamente", 
            "success"
        ).then(() => {
            // Redirigir a la lista de empleados
            window.location.href = getBaseUrl() + '/empleados';
        });
    }, 2000);
}

/**
 * Validar formulario completo
 */
function validarFormulario() {
    let isValid = true;
    
    // Limpiar errores previos
    $(".is-invalid").removeClass("is-invalid");
    $(".invalid-feedback").text("");

    // Validar campos obligatorios
    const camposObligatorios = [
        { id: "tipo_documento", mensaje: "Debe seleccionar un tipo de documento" },
        { id: "nro_documento", mensaje: "El número de documento es requerido" },
        { id: "nombre", mensaje: "Los nombres son requeridos" },
        { id: "apellido_paterno", mensaje: "El apellido paterno es requerido" },
        { id: "sexo", mensaje: "Debe seleccionar un sexo" },
        { id: "estado_civil", mensaje: "Debe seleccionar un estado civil" },
        { id: "profesion", mensaje: "Debe seleccionar una profesión" },
        { id: "grado_institucion", mensaje: "Debe seleccionar un grado de institución" },
        { id: "email", mensaje: "El email es requerido" },
        { id: "fecha_ingreso", mensaje: "La fecha de ingreso es requerida" },
        { id: "unidad_organizacional", mensaje: "Debe seleccionar una unidad organizacional" },
        { id: "cargo", mensaje: "Debe seleccionar un cargo" },
        { id: "regimen_laboral", mensaje: "Debe seleccionar un régimen laboral" },
        { id: "tipo_trabajador", mensaje: "Debe seleccionar un tipo de trabajador" },
        { id: "nivel_remunerativo", mensaje: "Debe seleccionar un nivel remunerativo" },
        { id: "estado", mensaje: "Debe seleccionar un estado" },
        { id: "direccion_actual", mensaje: "La dirección actual es requerida" }
    ];

    camposObligatorios.forEach(campo => {
        if (!validarCampo(campo.id, campo.mensaje)) {
            isValid = false;
        }
    });

    // Validar email
    const email = $("#email").val().trim();
    if (email && !isValidEmail(email)) {
        $("#email").addClass("is-invalid");
        $("#email_error").text("Ingrese un email válido");
        isValid = false;
    }

    // Validar fechas
    const fechaIngreso = $("#fecha_ingreso").val();
    const fechaCese = $("#fecha_cese").val();
    
    if (fechaIngreso && fechaCese) {
        if (new Date(fechaCese) <= new Date(fechaIngreso)) {
            $("#fecha_cese").addClass("is-invalid");
            $("#fecha_cese_error").text("La fecha de cese debe ser posterior a la fecha de ingreso");
            isValid = false;
        }
    }

    return isValid;
}

/**
 * Validar campo individual
 */
function validarCampo(fieldId, errorMessage) {
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
}

/**
 * Validar email
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

/**
 * Limpiar formulario
 */
function limpiarFormulario() {
    Swal.fire({
        title: "¿Limpiar formulario?",
        text: "Se perderán todos los datos ingresados",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Sí, limpiar",
        cancelButtonText: "Cancelar"
    }).then((result) => {
        if (result.isConfirmed) {
            $("#form_empleado")[0].reset();
            $("#empleado_id").val("");
            $(".is-invalid").removeClass("is-invalid");
            $(".invalid-feedback").text("");
            $("#sistema_pension").prop("disabled", false);
            // Restaurar dirección principal por defecto
            $("#es_principal_actual").prop("checked", true);
            // Restaurar preview de foto
            $("#preview_foto").attr("src", getBaseUrl() + "/assets/images/users/user-dummy-img.jpg");
            $("#btn_eliminar_foto").hide();
            
            // Restaurar texto del botón si estaba en modo edición
            if (isEditMode) {
                $("#btn_guardar_text").text("Actualizar Empleado");
            } else {
                $("#btn_guardar_text").text("Guardar Empleado");
            }
            
            showAlert("Formulario limpiado exitosamente", "success");
        }
    });
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
    return Swal.fire({
        title: type === "success" ? "Éxito" : type === "error" ? "Error" : "Información",
        text: message,
        icon: type,
        confirmButtonText: "Aceptar",
        confirmButtonColor: type === "success" ? "#28a745" : type === "error" ? "#dc3545" : "#007bff"
    });
}