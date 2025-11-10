/**
 * Sistema de Registro de Empleados - CAFED
 * Control de Asistencia
 * NOTA: Este archivo es solo para REGISTRO. Para edición ver views/empleados/update/
 */

// Variables globales
let userInfo = null;
let map = null;
let marker = null;
let selectedLat = null;
let selectedLng = null;
let cacheDocumentos = {}; // Cache local para evitar consultas repetidas
let ultimaConsulta = 0; // Timestamp de la última consulta
const DELAY_MINIMO_CONSULTA = 2000; // 2 segundos entre consultas

// Inicialización cuando el documento esté listo
$(document).ready(function() {
    if (!Auth.requireAuth()) {
        return;
    }
    
    userInfo = Auth.getUserInfo();
    
    // Mostrar placeholders de carga en los selects
    mostrarPlaceholdersSelects();
    
    // Cargar catálogos de forma secuencial (se van llenando uno por uno)
    cargarCatalogos();
    
    initializeEventListeners();
    initializeFormValidation();
    initializeTooltips();
    initializeImagePreview();
});

/**
 * Cargar todos los catálogos desde la API de forma secuencial
 * Se cargan uno por uno para mejor percepción visual
 */
async function cargarCatalogos() {
    try {
        const sedeId = userInfo.sede_id;
        const startTime = performance.now();
        
        // Cargar catálogos de forma secuencial (uno por uno)
        // Orden: Datos Personales -> Datos Laborales -> Extras
        
        // 1. Tipo de Documento (primero, es crítico)
        const tiposDoc = await API.get('maestros/tipos-documento', { sede_id: sedeId });
        Utils.poblarSelect('#tipo_documento', tiposDoc.data, 'id', 'descripcion', 'Seleccione tipo de documento');
        
        // 2. Estados Civil
        const estadosCivil = await API.get('maestros/estados-civil', { sede_id: sedeId });
        Utils.poblarSelect('#estado_civil', estadosCivil.data, 'id', 'descripcion', 'Seleccione un estado Civil');
        
        // 3. Profesiones
        const profesiones = await API.get('maestros/profesiones', { sede_id: sedeId });
        Utils.poblarSelect('#profesion', profesiones.data, 'id', 'descripcion', 'Seleccione profesión');
        
        // 4. Grados de Instrucción
        const grados = await API.get('maestros/grados-instruccion', { sede_id: sedeId });
        Utils.poblarSelect('#grado_institucion', grados.data, 'id', 'descripcion', 'Seleccione grado institución');
        
        // 5. Gerencias/Unidades Organizacionales (importante)
        const gerencias = await API.get('maestros/gerencias', { sede_id: sedeId });
        Utils.poblarSelect('#unidad_organizacional', gerencias.data, 'id', 'nombre', 'Seleccione unidad organizacional');
        
        // 6. Cargos (importante)
        const cargos = await API.get('cargos/getAll', { sede_id: sedeId });
        Utils.poblarSelect('#cargo', cargos.data, 'id', 'nombre_cargo', 'Seleccione un cargo');
        
        // 7. Regímenes Laborales
        const regimenes = await API.get('maestros/regimenes-laborales', { sede_id: sedeId });
        Utils.poblarSelect('#regimen_laboral', regimenes.data, 'id', 'nombre', 'Seleccione régimen laboral');
        
        // 8. Tipos de Trabajador
        const tipos = await API.get('maestros/tipos-trabajador', { sede_id: sedeId });
        Utils.poblarSelect('#tipo_trabajador', tipos.data, 'id', 'nombre', 'Seleccione tipo de trabajador');
        
        // 9. Niveles Remunerativos
        const niveles = await API.get('maestros/niveles-remunerativos', { sede_id: sedeId });
        Utils.poblarSelect('#nivel_remunerativo', niveles.data, 'id', 'nombre', 'Seleccione nivel remunerativo');
        
        // 10. Turnos (importante)
        const turnos = await API.get('maestros/turnos', { sede_id: sedeId });
        Utils.poblarSelect('#turno', turnos.data, 'id', 'nombre_turno', 'Seleccione un turno');
        
        // 11. Sistemas de Pensión
        const sistemas = await API.get('maestros/sistemas-pension', { sede_id: sedeId });
        Utils.poblarSelect('#sistema_pension', sistemas.data, 'id', 'nombre', 'Seleccione sistema de pensión');
        
        // 12. Bancos
        const bancos = await API.get('maestros/bancos', { sede_id: sedeId });
        Utils.poblarSelect('#banco', bancos.data, 'id', 'nombre', 'Seleccione un banco');
        
        // 13. Ubigeos (para direcciones)
        const ubigeos = await API.get('maestros/ubigeos', { sede_id: sedeId });
        // Poblar los 3 selects de ubigeo con los mismos datos
        Utils.poblarSelect('#ubigeo_actual', ubigeos.data, 'id', 'nombre_completo', 'Seleccione ubigeo (opcional)');
        Utils.poblarSelect('#ubigeo_reniec', ubigeos.data, 'id', 'nombre_completo', 'Seleccione ubigeo (opcional)');
        Utils.poblarSelect('#ubigeo_laboral', ubigeos.data, 'id', 'nombre_completo', 'Seleccione ubigeo (opcional)');
        
        const endTime = performance.now();
        console.log(`Catálogos cargados en ${(endTime - startTime).toFixed(0)}ms`);
        
    } catch (error) {
        console.error('Error al cargar catálogos:', error);
        Utils.showAlert('Error al cargar los catálogos del sistema. Por favor, recargue la página.', 'error');
    }
}

/**
 * Mostrar placeholders de carga en los selects
 */
function mostrarPlaceholdersSelects() {
    const selectsIds = [
        'tipo_documento', 'estado_civil', 'profesion', 'grado_institucion',
        'unidad_organizacional', 'cargo', 'sistema_pension', 'regimen_laboral',
        'tipo_trabajador', 'nivel_remunerativo', 'banco', 'turno'
    ];
    
    selectsIds.forEach(id => {
        $(`#${id}`).html('<option value="">Cargando...</option>');
    });
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
        Utils.redirect('/empleados');
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
    
    // Botón buscar ubicación en mapa
    $("#btn_buscar_ubicacion").on("click", function() {
        abrirModalMapa();
    });
    
    // Botón consultar RENIEC/SUNAT
    $("#btn_consultar_reniec").on("click", function() {
        consultarReniecSunat();
    });
    
    // Permitir consultar con Enter en el campo de documento
    $("#nro_documento").on("keypress", function(e) {
        if (e.which === 13) {
            e.preventDefault();
            consultarReniecSunat();
        }
    });
    
    // Botón confirmar ubicación
    $("#btn_confirmar_ubicacion").on("click", function() {
        confirmarUbicacion();
    });
    
    // Botón abrir mapa en nueva ventana
    $("#btn_abrir_mapa").on("click", function() {
        const url = $("#url_maps").val();
        if (url) {
            window.open(url, '_blank');
        }
    });
    
    // Buscar dirección en el mapa
    $("#btn_search_address").on("click", function() {
        buscarDireccion();
    });
    
    // Permitir buscar con Enter
    $("#search_address").on("keypress", function(e) {
        if (e.which === 13) {
            e.preventDefault();
            buscarDireccion();
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
                Utils.showAlert("Por favor seleccione una imagen válida (JPG, JPEG o PNG)", "error");
                $(this).val('');
                return;
            }
            
            // Validar tamaño (2MB máximo)
            const maxSize = 2 * 1024 * 1024; // 2MB en bytes
            if (file.size > maxSize) {
                Utils.showAlert("La imagen no debe superar los 2MB", "error");
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
        $("#preview_foto").attr("src", window.BASE_URL + "/assets/images/users/user-dummy-img.jpg");
        $(this).hide();
    });
}

/**
 * Inicializar validaciones del formulario
 */
function initializeFormValidation() {
    // Validación en tiempo real para campos obligatorios
    $("#tipo_documento").on("change", function() {
        Utils.validarCampo("tipo_documento", "Debe seleccionar un tipo de documento");
    });

    $("#nro_documento").on("blur", function() {
        Utils.validarCampo("nro_documento", "El número de documento es requerido");
    });
    
    $("#ruc").on("blur", function() {
        const ruc = $(this).val().trim();
        if (ruc === "") {
            $("#ruc").addClass("is-invalid");
            $("#ruc_error").text("El RUC es requerido");
        } else if (!Utils.isValidRUC(ruc)) {
            $("#ruc").addClass("is-invalid");
            $("#ruc_error").text("El RUC debe tener 11 dígitos");
        } else {
            $("#ruc").removeClass("is-invalid");
            $("#ruc_error").text("");
        }
    });

    $("#nombre").on("blur", function() {
        Utils.validarCampo("nombre", "Los nombres son requeridos");
    });

    $("#apellido_paterno").on("blur", function() {
        Utils.validarCampo("apellido_paterno", "El apellido paterno es requerido");
    });

    $("#sexo").on("change", function() {
        Utils.validarCampo("sexo", "Debe seleccionar un sexo");
    });

    $("#fecha_ingreso").on("change", function() {
        Utils.validarCampo("fecha_ingreso", "La fecha de ingreso es requerida");
    });

    $("#unidad_organizacional").on("change", function() {
        Utils.validarCampo("unidad_organizacional", "Debe seleccionar una unidad organizacional");
    });

    $("#cargo").on("change", function() {
        Utils.validarCampo("cargo", "Debe seleccionar un cargo");
    });

    $("#regimen_laboral").on("change", function() {
        Utils.validarCampo("regimen_laboral", "Debe seleccionar un régimen laboral");
    });

    $("#tipo_trabajador").on("change", function() {
        Utils.validarCampo("tipo_trabajador", "Debe seleccionar un tipo de trabajador");
    });

    $("#nivel_remunerativo").on("change", function() {
        Utils.validarCampo("nivel_remunerativo", "Debe seleccionar un nivel remunerativo");
    });
    
    $("#turno").on("change", function() {
        Utils.validarCampo("turno", "Debe seleccionar un turno");
    });

    // Validaciones para direcciones
    $("#direccion_actual").on("blur", function() {
        Utils.validarCampo("direccion_actual", "La dirección actual es requerida");
    });
    
    // Validación opcional de email (solo si se llena)
    $("#email").on("blur", function() {
        const email = $(this).val().trim();
        if (email && !Utils.isValidEmail(email)) {
            $("#email").addClass("is-invalid");
            $("#email_error").text("Ingrese un email válido");
        } else {
            $("#email").removeClass("is-invalid");
            $("#email_error").text("");
        }
    });
}

/**
 * Inicializar tooltips (usa Utils)
 */
function initializeTooltips() {
    Utils.initTooltips();
}

/**
 * Guardar empleado (CON MANEJO DE FOTO EN FORM-DATA)
 */
async function guardarEmpleado() {
    if (!validarFormulario()) {
        return;
    }

    try {
        const btnGuardar = $("#btn_guardar");
        const originalText = btnGuardar.html();
        btnGuardar.prop("disabled", true);
        
        // Mostrar loading
        Utils.showLoading('Registrando empleado...');
        
        const archivoFoto = document.getElementById('foto_empleado').files[0];
        
        // Función auxiliar para obtener valor de select (maneja valores vacíos correctamente)
        const getSelectValue = (selector) => {
            const val = $(selector).val();
            return (val && val !== '') ? parseInt(val) : null;
        };
        
        // Función auxiliar para obtener valor de texto (maneja valores vacíos correctamente)
        const getTextValue = (selector) => {
            const val = $(selector).val().trim();
            return (val !== '') ? val : null;
        };
        
        // Función auxiliar para obtener valor de select string (para campos como sexo)
        const getSelectStringValue = (selector) => {
            const val = $(selector).val();
            return (val && val !== '') ? val : null;
        };
        
        // Preparar datos del empleado
        const datosEmpleado = {
            sede_id: userInfo.sede_id,
            tipo_documento_id: getSelectValue("#tipo_documento"),
            nro_documento: getTextValue("#nro_documento"),
            ruc: getTextValue("#ruc"),
            nombre: getTextValue("#nombre"),
            apellido_paterno: getTextValue("#apellido_paterno"),
            apellido_materno: getTextValue("#apellido_materno"),
            telefono: getTextValue("#telefono"),
            email: getTextValue("#email"),
            sexo: getSelectStringValue("#sexo"),
            profesion_id: getSelectValue("#profesion"),
            grado_institucion_id: getSelectValue("#grado_institucion"),
            fecha_nacimiento: getTextValue("#fecha_nacimiento"),
            estado_civil_id: getSelectValue("#estado_civil"),
            fecha_ingreso: $("#fecha_ingreso").val(), // Requerido, no puede ser null
            fecha_cese: getTextValue("#fecha_cese"),
            gerencia_id: getSelectValue("#unidad_organizacional"),
            cargo_id: getSelectValue("#cargo"),
            turno_id: getSelectValue("#turno"),
            tipo_jornada: getSelectStringValue("#tipo_jornada") || 'Presencial',
            es_fiscalizado: $("#es_fiscalizado").is(':checked') ? 1 : 0,
            sistema_pension_id: $("#no_paga_seguro").is(':checked') ? null : getSelectValue("#sistema_pension"), // Si no paga seguro, sistema_pension debe ser null
            no_paga_seguro: $("#no_paga_seguro").is(':checked') ? 1 : 0,
            regimen_laboral_id: getSelectValue("#regimen_laboral"),
            tipo_trabajador_id: getSelectValue("#tipo_trabajador"),
            nivel_remunerativo_id: getSelectValue("#nivel_remunerativo"),
            banco_id: getSelectValue("#banco"),
            numero_cuenta: getTextValue("#numero_cuenta"),
            numero_cci: getTextValue("#numero_cci"),
            cuspp: getTextValue("#cuspp"),
            airhsp: getTextValue("#airhsp"),
            codigo_reloj: getTextValue("#codigo_reloj"),
            coordenada_x: getTextValue("#coordenada_x"),
            coordenada_y: getTextValue("#coordenada_y"),
            url_maps: getTextValue("#url_maps"),
            observaciones: getTextValue("#observaciones"),
            estado: 1,
            creado_por: userInfo.nombre_completo,
            direcciones: {
                actual: {
                    direccion: getTextValue("#direccion_actual"),
                    referencia: getTextValue("#referencia_actual"),
                    ubigeo_id: getSelectValue("#ubigeo_actual"),
                    es_principal: $("#es_principal_actual").is(':checked') ? 1 : 0
                },
                reniec: {
                    direccion: getTextValue("#direccion_reniec"),
                    referencia: getTextValue("#referencia_reniec"),
                    ubigeo_id: getSelectValue("#ubigeo_reniec"),
                    es_principal: $("#es_principal_reniec").is(':checked') ? 1 : 0
                },
                laboral: {
                    direccion: getTextValue("#direccion_laboral"),
                    referencia: getTextValue("#referencia_laboral"),
                    ubigeo_id: getSelectValue("#ubigeo_laboral"),
                    es_principal: $("#es_principal_laboral").is(':checked') ? 1 : 0
                }
            }
        };
        
        // PASO 1: Crear empleado (siempre JSON)
        const resultado = await API.post('empleados/create', datosEmpleado);
        
        if (!resultado || !resultado.ok) {
            Utils.closeLoading();
            btnGuardar.prop("disabled", false).html(originalText);
            Utils.showToast(resultado?.msj || 'Error al guardar el empleado', 'error');
            return;
        }
        
        // PASO 2: Subir foto si existe (form-data)
        if (archivoFoto && resultado.data && resultado.data.id) {
            // Actualizar mensaje del loading
            Utils.showLoading('Subiendo foto del empleado...');
            
            const formData = new FormData();
            formData.append('empleado_id', resultado.data.id);
            formData.append('sede_id', datosEmpleado.sede_id);
            formData.append('foto', archivoFoto);
            formData.append('modificado_por', userInfo.nombre_completo);
            
            // Enviar con fetch (no podemos usar API.post porque es FormData)
            const token = localStorage.getItem('token');
            const response = await fetch(window.BASE_URL + '/api/empleados/uploadFoto', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`
                },
                body: formData
            });
            
            const resultadoFoto = await response.json();
            
            if (!resultadoFoto.ok) {
                console.warn('Error al subir foto:', resultadoFoto.msj);
                // No mostrar error crítico, solo warning en consola
            }
        }
        
        // Cerrar loading
        Utils.closeLoading();
        btnGuardar.prop("disabled", false).html(originalText);
        
       Utils.showAlert(
            "Empleado registrado exitosamente", 
            "success"
        ).then(() => {
            Utils.redirect('/empleados');
        }); 
        
    } catch (error) {
        console.error('Error:', error);
        Utils.closeLoading();
        $("#btn_guardar").prop("disabled", false).html(originalText);
        Utils.showToast('Error al guardar el empleado', 'error');
    }
}


/**
 * Consultar datos en RENIEC/SUNAT usando APIS.NET.PE
 */
async function consultarReniecSunat() {
    const nroDocumento = $("#nro_documento").val().trim();
    const tipoDocumento = $("#tipo_documento").val();
    
    // Validar que haya número de documento
    if (!nroDocumento) {
        Utils.showToast("Por favor ingrese el número de documento", "warning");
        $("#nro_documento").focus();
        return;
    }
    
    // Validar formato del documento (debe ser numérico)
    if (!/^\d+$/.test(nroDocumento)) {
        Utils.showToast("El número de documento debe contener solo dígitos numéricos", "warning");
        $("#nro_documento").focus();
        return;
    }
    
    // Verificar cache local (válido por 1 hora)
    const cacheKey = `${nroDocumento.length === 8 ? 'dni' : 'ruc'}_${nroDocumento}`;
    const cacheData = cacheDocumentos[cacheKey];
    if (cacheData && (Date.now() - cacheData.timestamp) < 3600000) {
        // Usar datos del cache
        aplicarDatosConsulta(cacheData.data);
        Utils.showToast("Datos cargados desde caché", "info");
        return;
    }
    
    // Control de rate limiting: esperar al menos 2 segundos entre consultas
    const tiempoTranscurrido = Date.now() - ultimaConsulta;
    if (tiempoTranscurrido < DELAY_MINIMO_CONSULTA) {
        const tiempoRestante = Math.ceil((DELAY_MINIMO_CONSULTA - tiempoTranscurrido) / 1000);
        Utils.showToast(`Por favor espere ${tiempoRestante} segundo(s) antes de consultar nuevamente`, "warning");
        return;
    }
    
    // Determinar el endpoint según la longitud del documento
    // RUC: 11 dígitos, DNI: 8 dígitos
    const tipoDoc = nroDocumento.length === 11 ? 'ruc' : 'dni';
    
    try {
        const btnConsultar = $("#btn_consultar_reniec");
        const originalHtml = btnConsultar.html();
        btnConsultar.prop("disabled", true).html('<i class="ri-loader-4-line ri-spin"></i> Consultando...');
        
        // Actualizar timestamp de última consulta
        ultimaConsulta = Date.now();
        
        console.log("Consultando API a través del proxy:", nroDocumento, tipoDoc);
        
        // Consultar a través del proxy del backend (evita problemas de CORS)
        const resultado = await API.get('maestros/consultar-documento', {
            numero: nroDocumento,
            tipo: tipoDoc
        });
        
        console.log("Respuesta del proxy:", resultado);
        
        // Verificar si hubo error
        if (!resultado || !resultado.ok) {
            const mensaje = resultado?.msj || 'Error al consultar el documento';
            
            if (mensaje.includes('429') || mensaje.includes('límite')) {
                Utils.showToast("Se ha excedido el límite de consultas. Por favor, espere unos momentos e intente nuevamente.", "warning");
            } else {
                Utils.showToast(mensaje, "error");
            }
            
            btnConsultar.prop("disabled", false).html(originalHtml);
            return;
        }
        
        const data = resultado.data;
        
        if (!data) {
            Utils.showToast("No se recibieron datos de la consulta", "warning");
            btnConsultar.prop("disabled", false).html(originalHtml);
            return;
        }
        
        // Guardar en cache
        cacheDocumentos[cacheKey] = {
            data: data,
            timestamp: Date.now()
        };
        
        // Aplicar datos al formulario
        aplicarDatosConsulta(data);
        
        btnConsultar.prop("disabled", false).html(originalHtml);
        
    } catch (error) {
        console.error("Error completo al consultar RENIEC/SUNAT:", error);
        console.error("Tipo de error:", error.name);
        console.error("Mensaje:", error.message);
        
        // Manejar errores específicos
        if (error.message && error.message.includes('429')) {
            Utils.showToast("Se ha excedido el límite de consultas a la API. Por favor, espere unos minutos e intente nuevamente.", "error");
        } else {
            Utils.showToast(`Error al consultar los datos: ${error.message || 'Error desconocido'}. Consulte la consola para más detalles.`, "error");
        }
        
        $("#btn_consultar_reniec").prop("disabled", false).html('<i class="ri-search-line"></i> Consultar');
    }
}

/**
 * Aplicar datos de la consulta al formulario
 * @param {Object} data Datos de la respuesta de la API
 */
async function aplicarDatosConsulta(data) {
    // Mapear tipo de documento según el código de la API
    if (data.tipoDocumento) {
        await mapearTipoDocumento(data.tipoDocumento);
    }
    
    // La API de DNI devuelve los campos ya separados, mientras que RUC solo devuelve nombre completo
    let partesNombre;
    if (data.apellidoPaterno && data.apellidoMaterno && data.nombres) {
        // API de DNI: usar campos directamente
        partesNombre = {
            nombres: data.nombres.trim(),
            apellidoPaterno: data.apellidoPaterno.trim(),
            apellidoMaterno: data.apellidoMaterno.trim()
        };
    } else if (data.nombre) {
        // API de RUC: separar nombre completo
        const nombreCompleto = data.nombre.trim();
        partesNombre = separarNombreCompleto(nombreCompleto);
    } else {
        Utils.showToast("No se encontraron datos de nombre en la respuesta", "warning");
        return;
    }
    
    // Llenar campos del formulario
    $("#nombre").val(partesNombre.nombres || "").removeClass("is-invalid");
    $("#apellido_paterno").val(partesNombre.apellidoPaterno || "").removeClass("is-invalid");
    $("#apellido_materno").val(partesNombre.apellidoMaterno || "").removeClass("is-invalid");
    
    // Si el número de documento de la API es diferente, actualizarlo
    if (data.numeroDocumento && data.numeroDocumento !== $("#nro_documento").val().trim()) {
        $("#nro_documento").val(data.numeroDocumento);
    }
    
    // Si es RUC (tipoDocumento: "6" o 11 dígitos), también llenar el campo RUC
    const nroDoc = $("#nro_documento").val().trim();
    if ((data.tipoDocumento === "6" || nroDoc.length === 11) && data.numeroDocumento) {
        $("#ruc").val(data.numeroDocumento).removeClass("is-invalid");
    }
    
    // Mostrar información adicional si está disponible
    let mensajeInfo = `Datos encontrados: ${partesNombre.nombres} ${partesNombre.apellidoPaterno} ${partesNombre.apellidoMaterno}`.trim();
    if (data.estado && data.estado !== "") {
        mensajeInfo += ` - Estado: ${data.estado}`;
    }
    if (data.condicion && data.condicion !== "") {
        mensajeInfo += ` - Condición: ${data.condicion}`;
    }
    
    Utils.showToast(mensajeInfo, "success");
    
    // Enfocar el siguiente campo
    $("#nombre").focus();
}

/**
 * Mapear tipo de documento de la API a nuestro select
 * @param {string} tipoDocumentoApi Código del tipo de documento de la API
 */
async function mapearTipoDocumento(tipoDocumentoApi) {
    // Mapeo de códigos de la API a descripciones comunes
    // tipoDocumento: "6" = RUC, "1" = DNI, "4" = CE, etc.
    const mapeoTipos = {
        "1": "DNI",
        "4": "Carné de Extranjería",
        "6": "RUC",
        "7": "Pasaporte"
    };
    
    const descripcionBuscada = mapeoTipos[tipoDocumentoApi];
    
    if (!descripcionBuscada) {
        console.warn(`Tipo de documento no reconocido: ${tipoDocumentoApi}`);
        return;
    }
    
    // Buscar en el select el tipo de documento que coincida
    const selectTipoDoc = $("#tipo_documento");
    let encontrado = false;
    
    selectTipoDoc.find("option").each(function() {
        const texto = $(this).text().toUpperCase().trim();
        const descripcionUpper = descripcionBuscada.toUpperCase();
        
        // Buscar coincidencias parciales (ej: "DNI", "RUC", etc.)
        if (texto.includes(descripcionUpper) || descripcionUpper.includes(texto)) {
            selectTipoDoc.val($(this).val());
            encontrado = true;
            return false; // Salir del each
        }
    });
    
    if (!encontrado) {
        console.warn(`No se encontró el tipo de documento "${descripcionBuscada}" en el select`);
    }
}

/**
 * Separar nombre completo en nombres y apellidos
 * La API de SUNAT devuelve el formato: APELLIDO_PATERNO APELLIDO_MATERNO NOMBRES
 * Ejemplo: "VILLA FLORES JHON ALEX" -> apellidoPaterno: "VILLA", apellidoMaterno: "FLORES", nombres: "JHON ALEX"
 * @param {string} nombreCompleto Nombre completo de la persona
 * @returns {Object} Objeto con nombres, apellidoPaterno y apellidoMaterno
 */
function separarNombreCompleto(nombreCompleto) {
    if (!nombreCompleto || nombreCompleto.trim() === "") {
        return { nombres: "", apellidoPaterno: "", apellidoMaterno: "" };
    }
    
    // Limpiar y normalizar el nombre
    const nombreLimpio = nombreCompleto.trim().replace(/\s+/g, " ");
    const partes = nombreLimpio.split(" ");
    
    if (partes.length === 0) {
        return { nombres: "", apellidoPaterno: "", apellidoMaterno: "" };
    }
    
    // Si solo hay una parte, es el nombre completo (no se puede separar)
    if (partes.length === 1) {
        return { nombres: partes[0], apellidoPaterno: "", apellidoMaterno: "" };
    }
    
    // Si hay dos partes: primera es apellido paterno, segunda es nombres
    if (partes.length === 2) {
        return { nombres: partes[1], apellidoPaterno: partes[0], apellidoMaterno: "" };
    }
    
    // Si hay tres o más partes (formato SUNAT):
    // - Primera parte: apellido paterno
    // - Segunda parte: apellido materno
    // - Resto: nombres
    const apellidoPaterno = partes[0];
    const apellidoMaterno = partes[1];
    const nombres = partes.slice(2).join(" ");
    
    return {
        nombres: nombres || "",
        apellidoPaterno: apellidoPaterno || "",
        apellidoMaterno: apellidoMaterno || ""
    };
}

/**
 * Validar formulario completo
 */
function validarFormulario() {
    let isValid = true;
    
    // Limpiar errores previos
    Utils.limpiarValidaciones();

    // Validar solo campos obligatorios (quitados: profesion, grado_institucion, email, estado_civil)
    const camposObligatorios = [
        { id: "tipo_documento", mensaje: "Debe seleccionar un tipo de documento" },
        { id: "nro_documento", mensaje: "El número de documento es requerido" },
        { id: "ruc", mensaje: "El RUC es requerido" },
        { id: "nombre", mensaje: "Los nombres son requeridos" },
        { id: "apellido_paterno", mensaje: "El apellido paterno es requerido" },
        { id: "sexo", mensaje: "Debe seleccionar un sexo" },
        { id: "fecha_ingreso", mensaje: "La fecha de ingreso es requerida" },
        { id: "unidad_organizacional", mensaje: "Debe seleccionar una unidad organizacional" },
        { id: "cargo", mensaje: "Debe seleccionar un cargo" },
        { id: "regimen_laboral", mensaje: "Debe seleccionar un régimen laboral" },
        { id: "tipo_trabajador", mensaje: "Debe seleccionar un tipo de trabajador" },
        { id: "nivel_remunerativo", mensaje: "Debe seleccionar un nivel remunerativo" },
        { id: "turno", mensaje: "Debe seleccionar un turno" },
        { id: "direccion_actual", mensaje: "La dirección actual es requerida" }
    ];

    camposObligatorios.forEach(campo => {
        if (!Utils.validarCampo(campo.id, campo.mensaje)) {
            isValid = false;
        }
    });
    
    // Validar RUC (ya está en campos obligatorios pero validamos formato)
    const ruc = $("#ruc").val().trim();
    if (ruc && !Utils.isValidRUC(ruc)) {
        $("#ruc").addClass("is-invalid");
        $("#ruc_error").text("El RUC debe tener 11 dígitos");
        isValid = false;
    }

    // Validar email SOLO si se proporciona (es opcional)
    const email = $("#email").val().trim();
    if (email && !Utils.isValidEmail(email)) {
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
 * Limpiar formulario
 */
function limpiarFormulario() {
    Utils.showConfirm(
        "¿Limpiar formulario?",
        "Se perderán todos los datos ingresados",
        "Sí, limpiar",
        "Cancelar"
    ).then((result) => {
        if (result.isConfirmed) {
            $("#form_empleado")[0].reset();
            Utils.limpiarValidaciones();
            $("#sistema_pension").prop("disabled", false);
            $("#estado").val("1"); // Restaurar estado activo
            // Restaurar dirección principal por defecto
            $("#es_principal_actual").prop("checked", true);
            // Restaurar preview de foto
            $("#preview_foto").attr("src", window.BASE_URL + "/assets/images/users/user-dummy-img.jpg");
            $("#btn_eliminar_foto").hide();
            // Limpiar coordenadas
            $("#coordenada_x").val("");
            $("#coordenada_y").val("");
            $("#url_maps").val("");
            $("#btn_abrir_mapa").hide();
            
            Utils.showAlert("Formulario limpiado exitosamente", "success");
        }
    });
}

/**
 * Abrir modal de mapa
 */
function abrirModalMapa() {
    const modal = new bootstrap.Modal(document.getElementById('modal_mapa'));
    modal.show();
    
    // Inicializar mapa cuando se abra el modal
    setTimeout(() => {
        if (!map) {
            initializeMap();
        } else {
            map.invalidateSize();
        }
    }, 300);
}

/**
 * Inicializar mapa de Leaflet
 */
function initializeMap() {
    // Coordenadas por defecto: Lima, Perú
    const defaultLat = -12.0464;
    const defaultLng = -77.0428;
    
    map = L.map('map').setView([defaultLat, defaultLng], 13);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Click en el mapa para seleccionar ubicación
    map.on('click', function(e) {
        seleccionarUbicacion(e.latlng.lat, e.latlng.lng);
    });
}

/**
 * Buscar dirección en el mapa
 */
async function buscarDireccion() {
    const address = $("#search_address").val().trim();
    
    if (!address) {
        Utils.showToast("Ingrese una dirección para buscar", "warning");
        return;
    }
    
    try {
        // Usar API de Nominatim de OpenStreetMap
        const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(address)}&countrycodes=pe`);
        const data = await response.json();
        
        if (data && data.length > 0) {
            const lat = parseFloat(data[0].lat);
            const lng = parseFloat(data[0].lon);
            
            map.setView([lat, lng], 16);
            seleccionarUbicacion(lat, lng);
            
            Utils.showToast("Ubicación encontrada", "success");
        } else {
            Utils.showToast("No se encontró la dirección. Intente con otra búsqueda", "warning");
        }
    } catch (error) {
        console.error("Error al buscar dirección:", error);
        Utils.showToast("Error al buscar la dirección", "error");
    }
}

/**
 * Seleccionar ubicación en el mapa
 */
function seleccionarUbicacion(lat, lng) {
    selectedLat = lat;
    selectedLng = lng;
    
    // Remover marcador anterior si existe
    if (marker) {
        map.removeLayer(marker);
    }
    
    // Agregar nuevo marcador
    marker = L.marker([lat, lng]).addTo(map);
    
    // Actualizar información
    $("#selected_location").html(`
        <strong>Latitud:</strong> ${lat.toFixed(6)}<br>
        <strong>Longitud:</strong> ${lng.toFixed(6)}
    `);
    
    // Habilitar botón confirmar
    $("#btn_confirmar_ubicacion").prop("disabled", false);
}

/**
 * Confirmar ubicación seleccionada
 */
function confirmarUbicacion() {
    if (selectedLat && selectedLng) {
        $("#coordenada_x").val(selectedLat.toFixed(6));
        $("#coordenada_y").val(selectedLng.toFixed(6));
        
        // Generar URL de Google Maps
        const url = `https://www.google.com/maps?q=${selectedLat},${selectedLng}`;
        $("#url_maps").val(url);
        $("#btn_abrir_mapa").show();
        
        // Cerrar modal
        bootstrap.Modal.getInstance(document.getElementById('modal_mapa')).hide();
        
        Utils.showToast("Ubicación guardada correctamente", "success");
    }
}
