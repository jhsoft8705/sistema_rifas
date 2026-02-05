/**
 * Gestión de Rifas y Sorteos - Vista Administrativa
 * Inspirado en la experiencia del módulo de empleados
 */

const SafeUtils = {
    showLoading(message = 'Procesando...') {
        if (window.Utils?.showLoading) {
            Utils.showLoading(message);
        }
    },
    closeLoading() {
        if (window.Utils?.closeLoading) {
            Utils.closeLoading();
        }
    },
    showToast(message, type = 'info') {
        if (window.Utils?.showToast) {
            Utils.showToast(message, type);
        } else {
            const logFn = type === 'error' ? console.error : console.log;
            logFn(message);
        }
    },
    formatCurrency(value) {
        if (window.Utils?.formatearMoneda) {
            return Utils.formatearMoneda(value);
        }
        const amount = parseFloat(value || 0);
        return `S/. ${amount.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    },
    formatDate(value) {
        if (window.Utils?.formatearFecha) {
            return Utils.formatearFecha(value);
        }
        return value ? new Date(value).toLocaleString('es-PE') : '-';
    }
};

let tablaRifas = null;
let userInfo = null;
let premiosCatalogo = [];
let rifaSeleccionada = null;
let rifasData = [];
let valoresOriginalesNumeros = { numero_inicial: null, numero_final: null }; // Para detectar cambios

let modalRifa = null;
let modalPremios = null;

$(document).ready(async () => {
    if (!Auth.requireAuth()) return;

    userInfo = Auth.getUserInfo();
    modalRifa = new bootstrap.Modal(document.getElementById('modal_rifa'));
    modalPremios = new bootstrap.Modal(document.getElementById('modal_premios_rifa'));

    inicializarFlatpickr();
    inicializarTablas();
    inicializarEventosUI();

    await cargarPremiosCatalogo();
});

function inicializarFlatpickr() {
    if (typeof flatpickr !== 'function') {
        console.warn('Flatpickr no disponible');
        return;
    }
    const config = {
        enableTime: true,
        dateFormat: 'Y-m-d H:i',
        locale: 'es'
    };
    flatpickr('#fecha_inicio_venta', config);
    flatpickr('#fecha_fin_venta', config);
    flatpickr('#fecha_sorteo', config);
}

function inicializarTablas() {
    tablaRifas = $('#tabla_rifas').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.API_BASE_URL + '/rifas/getAll',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            },
            data: function (d) {
                d.sede_id = userInfo?.sede_id || '';
                const estado = $('#filtro_estado_rifa').val();
                if (estado !== '') {
                    d.estado = estado;
                }
                return d;
            },
            dataSrc: function (json) {
                if (json && json.ok) {
                    rifasData = json.data || [];
                    return rifasData;
                } else {
                    rifasData = [];
                    return [];
                }
            },
            error: function (xhr, error, thrown) {
                console.error('Error al cargar rifas:', error);
                rifasData = [];
                if (xhr.status === 401) {
                    Auth.logout();
                } else {
                    Utils.showAlert('Error de conexión al cargar las rifas', 'error');
                }
            }
        },
        language: Utils.getDataTableLanguageES(),
        lengthChange: false,
        dom: 'frtip',
        columns: [
            {
                data: null,
                className: 'text-center',
                orderable: false,
                width: '340px',
                render: (_, __, row) => {
                    const esCerrada = row.estado === 'CERRADA' || row.estado === 'FINALIZADA';
                    const baseUrl = window.BASE_URL || '';
                    const urlPublicidad = `${baseUrl}/admin-rifas-publicidad?rifa_id=${row.id}`;
                    return `
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        ${!esCerrada ? `
                        <button class="btn btn-sm btn-primary btn-editar" data-id="${row.id}" title="Editar" style="min-width: 80px;">
                            <i class="ri-edit-2-line me-1"></i>Editar
                        </button>
                        ` : ''}
                        <button class="btn btn-sm btn-outline-primary btn-premios" data-id="${row.id}" title="Premios" style="min-width: 80px;">
                            <i class="ri-gift-line me-1"></i>Premios
                        </button>
                        <a href="${urlPublicidad}" class="btn btn-sm btn-outline-info btn-publicidad" title="Imprimir en publicidad" style="min-width: 80px;">
                            <i class="ri-megaphone-line me-1"></i>Publicidad
                        </a>
                        ${!esCerrada ? `
                        <button class="btn btn-sm btn-outline-warning btn-cerrar" data-id="${row.id}" title="Cerrar Rifa" style="min-width: 80px;">
                            <i class="ri-lock-line me-1"></i>Cerrar
                        </button>
                        ` : ''}
                    </div>
                `
                }
            },
            { data: 'codigo' },
            { data: 'nombre' },
            {
                data: 'premio_principal_nombre',
                render: (value, _, row) => value || (row.total_premios > 0 ? `${row.total_premios} premio(s)` : '-')
            },
            {
                data: 'precio_ticket',
                render: SafeUtils.formatCurrency
            },
            {
                data: null,
                render: (row) => `${row.numeros_disponibles ?? 0}/${row.total_numeros ?? 0}`
            },
            {
                data: 'estado',
                render: (estado) => obtenerBadgeEstadoRifa(estado)
            },
            {
                data: 'fecha_sorteo',
                render: (valor) => formatearFechaListadoRifa(valor)
            }
        ]
    });

}

function inicializarEventosUI() {
    $('#btn_filtrar_rifas').on('click', function () {
        tablaRifas.ajax.reload();
    });
    $('#btn_recargar_rifas').on('click', function () {
        $('#filtro_estado_rifa').val('');
        tablaRifas.ajax.reload();
    });
    $('#btn_nueva_rifa').on('click', async function () {
        const $btn = $('#btn_nueva_rifa');
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        try {
            await cargarPremiosCatalogo();
            abrirModalRifa();
        } catch (error) {
            console.error('Error al preparar modal de nueva rifa:', error);
            Utils.showAlert('Ocurrió un problema al preparar el formulario', 'error');
        } finally {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });

    $('#tabla_rifas tbody').on('click', '.btn-editar', async function () {
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        try {
            await editarRifa($(this).data('id'));
        } finally {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
    $('#tabla_rifas tbody').on('click', '.btn-premios', async function () {
        const $btn = $(this);
        const originalHtml = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');
        try {
            await mostrarModalPremios($(this).data('id'));
        } finally {
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
    $('#tabla_rifas tbody').on('click', '.btn-cerrar', async function () {
        await cerrarRifa($(this).data('id'));
    });


    $('#form_rifa').on('submit', async (event) => {
        event.preventDefault();
        await guardarRifa();
    });

    $('#form_premio_rifa').on('submit', async (event) => {
        event.preventDefault();
        await guardarPremioRifa();
    });
    $('#btn_cancelar_premio_rifa').on('click', limpiarFormularioPremio);

    $('#form_rifa').on('input change', 'input, select, textarea', function () {
        if (!this.id) {
            return;
        }
        if ($(this).hasClass('is-invalid')) {
            const value = $(this).val();
            const tieneValor = Array.isArray(value)
                ? value.length > 0
                : (value !== null && value !== undefined && String(value).trim() !== '');
            if (tieneValor) {
                $(this).removeClass('is-invalid');
                const errorId = `${this.id}_error`;
                if (document.getElementById(errorId)) {
                    $(`#${errorId}`).text('');
                }
            }
        }
    });

}

async function cargarPremiosCatalogo(options = {}) {
    if (!userInfo) return [];

    const { includeIds = [], showFeedback = false } = options;
    const includeSet = new Set(includeIds.filter(Boolean).map(id => Number(id)));

    const $selectPremioPrincipal = $('#premio_id');
    const $selectPremioRifa = $('#premio_rifa_select');
    const $alertaSinPremios = $('#alerta_sin_premios_activos');
    const $helpPremioPrincipal = $('#premio_principal_help');
    const $btnGuardarPremioRifa = $('#btn_guardar_premio_rifa');

    try {
        const respuesta = await API.get('premios/getAll', { sede_id: userInfo.sede_id });

        if (!respuesta?.ok) {
            throw new Error(respuesta?.msj || 'No se pudieron cargar los premios');
        }

        const todos = respuesta.data || [];
        const activos = [];
        const vistos = new Set();

        todos.forEach((premio) => {
            const id = Number(premio.id);
            const esActivo = Number(premio.estado) === 1;
            if ((esActivo || includeSet.has(id)) && !vistos.has(id)) {
                activos.push(premio);
                vistos.add(id);
            }
        });

        premiosCatalogo = activos;

        const opcionesPremioPrincipal = ['<option value="">Sin premio principal</option>'];
        const opcionesPremioRifa = ['<option value="">Seleccionar premio</option>'];

        if (activos.length) {
            activos.forEach((premio) => {
                const etiqueta = premio.codigo ? `${premio.codigo} - ${premio.nombre}` : premio.nombre;
                const option = `<option value="${premio.id}">${etiqueta}</option>`;
                opcionesPremioPrincipal.push(option);
                opcionesPremioRifa.push(option);
            });

            if ($alertaSinPremios.length) {
                $alertaSinPremios.addClass('d-none');
            }
            if ($helpPremioPrincipal.length) {
                $helpPremioPrincipal.text('Opcional: vincula un premio registrado como premio principal de la rifa.');
            }
            if ($btnGuardarPremioRifa.length) {
                $btnGuardarPremioRifa.prop('disabled', false);
            }
            if ($selectPremioRifa.length) {
                $selectPremioRifa.prop('disabled', false);
            }
        } else {
            opcionesPremioPrincipal.push('<option value="" disabled>No hay premios activos disponibles</option>');
            opcionesPremioRifa[0] = '<option value="" disabled>Sin premios activos</option>';

            if ($alertaSinPremios.length) {
                $alertaSinPremios.removeClass('d-none');
            }
            if ($helpPremioPrincipal.length) {
                $helpPremioPrincipal.text('No hay premios activos disponibles en la sede.');
            }
            if ($btnGuardarPremioRifa.length) {
                $btnGuardarPremioRifa.prop('disabled', true);
            }
            if ($selectPremioRifa.length) {
                $selectPremioRifa.prop('disabled', true);
            }
            if (showFeedback) {
                SafeUtils.showToast('No hay premios activos disponibles. Registra uno en el módulo de premios.', 'info');
            }
        }

        if ($selectPremioPrincipal.length) {
            $selectPremioPrincipal.html(opcionesPremioPrincipal.join(''));
        }
        if ($selectPremioRifa.length) {
            $selectPremioRifa.html(opcionesPremioRifa.join(''));
        }

        return premiosCatalogo;
    } catch (error) {
        console.error('Error al cargar premios del catálogo:', error);
        premiosCatalogo = [];

        if ($alertaSinPremios.length) {
            $alertaSinPremios.removeClass('d-none');
        }
        if ($helpPremioPrincipal.length) {
            $helpPremioPrincipal.text('No se pudieron cargar los premios de la sede.');
        }
        if ($selectPremioPrincipal.length) {
            $selectPremioPrincipal.html('<option value="">Sin premio principal</option>');
        }
        if ($selectPremioRifa.length) {
            $selectPremioRifa.html('<option value="">Seleccionar premio</option>').prop('disabled', true);
        }
        if ($btnGuardarPremioRifa.length) {
            $btnGuardarPremioRifa.prop('disabled', true);
        }
        if (showFeedback) {
            SafeUtils.showToast('No se pudieron cargar los premios. Intenta nuevamente.', 'warning');
        }
        return [];
    }
}


function limpiarFormularioRifa() {
    const form = document.getElementById('form_rifa');
    form.reset();
    Utils.limpiarValidaciones('form_rifa');

    $('#rifa_id').val('');
    $('#sede_id_rifa').val(userInfo?.sede_id || '');
    $('#contenedor_regenerar_numeros').addClass('d-none');
    $('#regenerar_numeros').prop('checked', false);
    valoresOriginalesNumeros.numero_inicial = null;
    valoresOriginalesNumeros.numero_final = null;
    $('#info_cambio_numeros').hide();

    ['fecha_inicio_venta', 'fecha_fin_venta', 'fecha_sorteo'].forEach((id) => setFechaCampo(id, ''));

    $('#estado_rifa').val('BORRADOR');
    // Restaurar el campo de estado (habilitarlo y quitar ayuda)
    $('#estado_rifa').prop('disabled', false);
    $('#estado_rifa').removeAttr('title');
    $('#estado_rifa').next('.form-text').remove();
    
    $('#mostrar_contador').val('1');
    $('#mostrar_participantes').val('1');
    $('#mostrar_tickets_vendidos').val('1');
    $('#permitir_seleccion_numero').prop('checked', true);
    $('#asignacion_automatica').prop('checked', true);
}

/**
 * Detectar cambios en numeración y mostrar información
 */
function actualizarInfoCambioNumeros() {
    if (!valoresOriginalesNumeros.numero_inicial || !valoresOriginalesNumeros.numero_final) {
        $('#info_cambio_numeros').hide();
        return;
    }
    
    const inicialActual = parseInt($('#numero_inicial').val(), 10);
    const finalActual = parseInt($('#numero_final').val(), 10);
    const inicialOriginal = valoresOriginalesNumeros.numero_inicial;
    const finalOriginal = valoresOriginalesNumeros.numero_final;
    
    if (isNaN(inicialActual) || isNaN(finalActual)) {
        $('#info_cambio_numeros').hide();
        return;
    }
    
    const cambioInicial = inicialActual !== inicialOriginal;
    const cambioFinal = finalActual !== finalOriginal;
    
    if (!cambioInicial && !cambioFinal) {
        $('#info_cambio_numeros').hide();
        return;
    }
    
    let mensaje = '';
    let tipoAlerta = 'info';
    
    if (finalActual > finalOriginal) {
        const nuevosNumeros = finalActual - finalOriginal;
        mensaje = `Se agregarán automáticamente ${nuevosNumeros} número(s) nuevo(s) (del ${finalOriginal + 1} al ${finalActual}). Los números existentes se mantendrán.`;
        tipoAlerta = 'success';
    } else if (finalActual < finalOriginal) {
        mensaje = `Se reducirá el rango a ${finalActual}. Si hay tickets vendidos fuera de este rango, la actualización será rechazada.`;
        tipoAlerta = 'warning';
    }
    
    if (inicialActual < inicialOriginal) {
        const nuevosIniciales = inicialOriginal - inicialActual;
        mensaje += (mensaje ? ' ' : '') + `Se agregarán números del ${inicialActual} al ${inicialOriginal - 1}.`;
    } else if (inicialActual > inicialOriginal) {
        mensaje += (mensaje ? ' ' : '') + `El número inicial aumentará a ${inicialActual}. Si hay tickets vendidos antes de este número, la actualización será rechazada.`;
        tipoAlerta = 'warning';
    }
    
    if (mensaje) {
        $('#texto_cambio_numeros').text(mensaje);
        $('#info_cambio_numeros').removeClass('alert-soft-info alert-soft-success alert-soft-warning')
            .addClass(`alert-soft-${tipoAlerta}`).show();
    } else {
        $('#info_cambio_numeros').hide();
    }
}

function setFechaCampo(id, valor) {
    const input = document.getElementById(id);
    if (!input) {
        return;
    }

    if (typeof flatpickr === 'function' && input._flatpickr) {
        if (valor) {
            input._flatpickr.setDate(valor, true, 'Y-m-d H:i');
        } else {
            input._flatpickr.clear();
        }
    } else {
        $(`#${id}`).val(valor || '');
    }
}

async function abrirModalRifa(detalle = null) {
    limpiarFormularioRifa();

    const includeIds = [];
    if (detalle?.premio_principal_id) {
        includeIds.push(detalle.premio_principal_id);
    }

    await cargarPremiosCatalogo({ includeIds });

    if (detalle) {
        $('#modal_rifa_title').text('Editar rifa');
        $('#rifa_id').val(detalle.id);
        $('#sede_id_rifa').val(detalle.sede_id);
        $('#premio_id').val(detalle.premio_principal_id || '');
        
        // Si la rifa está cerrada o finalizada, deshabilitar el campo de estado
        const esCerrada = detalle.estado === 'CERRADA' || detalle.estado === 'FINALIZADA';
        $('#estado_rifa').val(detalle.estado);
        $('#estado_rifa').prop('disabled', esCerrada);
        
        if (esCerrada) {
            $('#estado_rifa').attr('title', 'No se puede cambiar el estado de una rifa cerrada');
            // Agregar ayuda visual
            if (!$('#estado_rifa').next('.form-text').length) {
                $('#estado_rifa').after('<small class="form-text text-muted"><i class="ri-information-line"></i> Una rifa cerrada no puede cambiar de estado</small>');
            }
        } else {
            $('#estado_rifa').removeAttr('title');
            $('#estado_rifa').next('.form-text').remove();
        }
        
        $('#nombre_rifa').val(detalle.nombre);
        $('#precio_ticket').val(detalle.precio_ticket);
        $('#descripcion_rifa').val(detalle.descripcion || '');
        $('#numero_inicial').val(detalle.numero_inicial);
        $('#numero_final').val(detalle.numero_final);
        // Guardar valores originales para detectar cambios
        valoresOriginalesNumeros.numero_inicial = detalle.numero_inicial;
        valoresOriginalesNumeros.numero_final = detalle.numero_final;
        $('#cantidad_digitos').val(detalle.cantidad_digitos || 4);
        $('#cantidad_maxima_por_persona').val(detalle.cantidad_maxima_por_persona || 1);
        $('#cantidad_maxima_tickets').val(detalle.cantidad_maxima_tickets || '');
        $('#tipo_numeracion').val(detalle.tipo_numeracion || 'CORRELATIVO');
        $('#prefijo_numero').val(detalle.prefijo_numero || '');
        $('#sufijo_numero').val(detalle.sufijo_numero || '');
        $('#texto_promocional').val(detalle.texto_promocional || '');
        $('#reglas_participacion').val(detalle.reglas_participacion || '');
        $('#terminos_rifa').val(detalle.terminos_condiciones || '');
        // Valores 0 y 1: no usar || porque 0 es falsy y se reemplazaría por 1
        $('#mostrar_contador').val(detalle.mostrar_contador !== undefined && detalle.mostrar_contador !== null ? String(detalle.mostrar_contador) : '1');
        $('#mostrar_participantes').val(detalle.mostrar_participantes !== undefined && detalle.mostrar_participantes !== null ? String(detalle.mostrar_participantes) : '1');
        $('#mostrar_tickets_vendidos').val(detalle.mostrar_tickets_vendidos !== undefined && detalle.mostrar_tickets_vendidos !== null ? String(detalle.mostrar_tickets_vendidos) : '1');
        $('#permitir_seleccion_numero').prop('checked', detalle.permitir_seleccion_numero == 1);
        $('#asignacion_automatica').prop('checked', detalle.asignacion_automatica == 1);
        setFechaCampo('fecha_inicio_venta', formatearFechaInput(detalle.fecha_inicio_venta));
        setFechaCampo('fecha_fin_venta', formatearFechaInput(detalle.fecha_fin_venta));
        setFechaCampo('fecha_sorteo', formatearFechaInput(detalle.fecha_sorteo));

        $('#contenedor_regenerar_numeros').toggleClass('d-none', false);
        actualizarInfoCambioNumeros();
        
        // Listeners para detectar cambios en números
        $('#numero_inicial, #numero_final').off('input change').on('input change', actualizarInfoCambioNumeros);
    } else {
        $('#modal_rifa_title').text('Nueva rifa');
        $('#estado_rifa').val('BORRADOR');
        $('#mostrar_contador, #mostrar_participantes, #mostrar_tickets_vendidos').val(1);
        $('#permitir_seleccion_numero, #asignacion_automatica').prop('checked', true);
        $('#sede_id_rifa').val(userInfo.sede_id);
    }

    modalRifa.show();
}

async function editarRifa(id) {
    const $btn = $(`button.btn-editar[data-id="${id}"]`);
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

    try {
        const [respuestaRifa, respuestaPremios] = await Promise.all([
            API.get('rifas/getById', { id, sede_id: userInfo.sede_id }),
            cargarPremiosCatalogo()
        ]);

        if (respuestaRifa?.ok && respuestaRifa.data) {
            rifaSeleccionada = respuestaRifa.data;
            abrirModalRifa(respuestaRifa.data);
        } else {
            Utils.showAlert(respuestaRifa?.msj || 'No se pudo obtener la rifa', 'error');
        }
    } catch (error) {
        console.error('Error al obtener rifa:', error);
        Utils.showAlert('Ocurrió un problema al obtener la rifa', 'error');
    } finally {
        $btn.prop('disabled', false).html(originalHtml);
    }
}

function construirPayloadRifa() {
    const payload = {
        sede_id: userInfo.sede_id,
        premio_id: parseIntOrNull($('#premio_id').val()),
        codigo: null, // Se genera automáticamente en el backend
        nombre: $('#nombre_rifa').val().trim(),
        descripcion: $('#descripcion_rifa').val()?.trim() || null,
        numero_intentos: 5,
        intento_ganador: 5,
        precio_ticket: parseFloat($('#precio_ticket').val()),
        cantidad_maxima_tickets: parseIntOrNull($('#cantidad_maxima_tickets').val()),
        cantidad_maxima_por_persona: parseIntOrNull($('#cantidad_maxima_por_persona').val()),
        usa_numeracion_boletos: 1,
        tipo_numeracion: $('#tipo_numeracion').val(),
        numero_inicial: parseInt($('#numero_inicial').val(), 10),
        numero_final: parseInt($('#numero_final').val(), 10),
        cantidad_digitos: parseIntOrNull($('#cantidad_digitos').val()),
        prefijo_numero: $('#prefijo_numero').val()?.trim() || null,
        sufijo_numero: $('#sufijo_numero').val()?.trim() || null,
        permitir_seleccion_numero: $('#permitir_seleccion_numero').is(':checked') ? 1 : 0,
        asignacion_automatica: $('#asignacion_automatica').is(':checked') ? 1 : 0,
        mostrar_numeros_disponibles: 1,
        numeros_bloqueados: null,
        numeros_especiales: null,
        fecha_inicio_venta: $('#fecha_inicio_venta').val(),
        fecha_fin_venta: $('#fecha_fin_venta').val(),
        fecha_sorteo: $('#fecha_sorteo').val(),
        mostrar_contador: parseInt($('#mostrar_contador').val(), 10),
        mostrar_participantes: parseInt($('#mostrar_participantes').val(), 10),
        mostrar_tickets_vendidos: parseInt($('#mostrar_tickets_vendidos').val(), 10),
        texto_promocional: $('#texto_promocional').val()?.trim() || null,
        reglas_participacion: $('#reglas_participacion').val()?.trim() || null,
        terminos_condiciones: $('#terminos_rifa').val()?.trim() || null,
        premios: [] // premios adicionales se gestionan por modal específico
    };

    const regenerar = $('#regenerar_numeros').is(':checked') ? 1 : 0;
    payload.regenerar_numeros = regenerar;

    return payload;
}

function marcarCampoInvalido(fieldId, mensaje) {
    const $field = $(`#${fieldId}`);
    $field.addClass('is-invalid');
    $(`#${fieldId}_error`).text(mensaje);
}

function validarFormularioRifa() {
    let esValido = true;
    Utils.limpiarValidaciones('form_rifa');

    const camposObligatorios = [
        { id: 'nombre_rifa', mensaje: 'El nombre de la rifa es obligatorio' },
        { id: 'precio_ticket', mensaje: 'El precio del ticket es obligatorio' },
        { id: 'numero_inicial', mensaje: 'El número inicial es obligatorio' },
        { id: 'numero_final', mensaje: 'El número final es obligatorio' },
        { id: 'fecha_inicio_venta', mensaje: 'La fecha de inicio de venta es obligatoria' },
        { id: 'fecha_fin_venta', mensaje: 'La fecha de fin de venta es obligatoria' },
        { id: 'fecha_sorteo', mensaje: 'La fecha del sorteo es obligatoria' }
    ];

    camposObligatorios.forEach((campo) => {
        if (!Utils.validarCampo(campo.id, campo.mensaje)) {
            esValido = false;
        }
    });

    const precio = parseFloat($('#precio_ticket').val());
    if (Number.isNaN(precio) || precio <= 0) {
        marcarCampoInvalido('precio_ticket', 'El precio del ticket debe ser mayor a 0');
        esValido = false;
    }

    const numeroInicial = parseInt($('#numero_inicial').val(), 10);
    const numeroFinal = parseInt($('#numero_final').val(), 10);

    if (!Number.isNaN(numeroInicial) && numeroInicial < 0) {
        marcarCampoInvalido('numero_inicial', 'El número inicial no puede ser negativo');
        esValido = false;
    }

    if (!Number.isNaN(numeroInicial) && !Number.isNaN(numeroFinal) && numeroFinal < numeroInicial) {
        marcarCampoInvalido('numero_final', 'El número final debe ser mayor o igual al inicial');
        esValido = false;
    }

    const parseFecha = (valor) => {
        if (!valor) return null;
        const limpio = valor.trim();
        const isoLike = limpio.includes('T') ? limpio : limpio.replace(' ', 'T');
        const fecha = new Date(isoLike);
        return Number.isNaN(fecha.getTime()) ? null : fecha;
    };

    const fechaInicio = parseFecha($('#fecha_inicio_venta').val());
    const fechaFin = parseFecha($('#fecha_fin_venta').val());
    const fechaSorteo = parseFecha($('#fecha_sorteo').val());

    if (fechaInicio && fechaFin && fechaFin < fechaInicio) {
        marcarCampoInvalido('fecha_fin_venta', 'La fecha fin de venta debe ser posterior a la fecha de inicio');
        esValido = false;
    }

    if (fechaFin && fechaSorteo && fechaSorteo < fechaFin) {
        marcarCampoInvalido('fecha_sorteo', 'La fecha del sorteo debe ser posterior a la fecha fin de venta');
        esValido = false;
    }

    return esValido;
}

async function guardarRifa() {
    if (!validarFormularioRifa()) {
        return;
    }

    const payload = construirPayloadRifa();
    const rifaId = $('#rifa_id').val();
    const esEdicion = Boolean(rifaId);

    if (esEdicion) {
        payload.id = parseInt(rifaId, 10);
        // El código se obtiene del backend en actualización, no se envía
        payload.estado = $('#estado_rifa').val();
        payload.estado_activo = payload.estado === 'CANCELADA' ? 0 : 1;
        payload.modificado_por = userInfo.nombre_completo || 'SYSTEM';
    } else {
        payload.estado = $('#estado_rifa').val() || 'BORRADOR';
        payload.creado_por = userInfo.nombre_completo || 'SYSTEM';
        if (payload.premio_id) {
            payload.premios = [{
                premio_id: payload.premio_id,
                es_principal: 1,
                orden: null,
                titulo: null,
                descripcion: null,
                cantidad: 1,
                valor_estimado: null
            }];
        } else {
            payload.premios = [];
        }
    }

    // Deshabilitar botón de guardar para evitar doble clic
    const $btnGuardar = $('#btn_guardar_rifa');
    const originalBtnHtml = $btnGuardar.html();
    $btnGuardar.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...');

    try {
        const endpoint = esEdicion ? 'rifas/update' : 'rifas/register';
        const respuesta = await API.post(endpoint, payload);

        // Restaurar botón
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);

        if (respuesta?.ok) {
            Utils.showAlert(respuesta.msj || (esEdicion ? 'Rifa actualizada correctamente' : 'Rifa registrada correctamente'), 'success');
            modalRifa.hide();
            tablaRifas.ajax.reload();
        } else {
            Utils.showAlert(respuesta?.msj || 'No se pudo guardar la rifa', 'error');
        }
    } catch (error) {
        // Restaurar botón en caso de error
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);
        console.error('Error al guardar rifa:', error);
        Utils.showAlert('Ocurrió un problema al guardar la rifa', 'error');
    }
}

async function cerrarRifa(id) {
    const rifa = rifasData.find(r => r.id == id);
    if (!rifa) {
        Utils.showAlert('No se encontró la información de la rifa', 'error');
        return;
    }

    const confirmar = await Utils.showConfirm(
        '¿Cerrar rifa?',
        `¿Está seguro de cerrar la rifa "${rifa.nombre}"? Esta acción cambiará el estado a CERRADA.`,
        'Sí, cerrar',
        'Cancelar'
    );

    if (!confirmar.isConfirmed) return;

    // Deshabilitar botón
    const $btn = $(`button.btn-cerrar[data-id="${id}"]`);
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

    try {
        const respuesta = await API.post('rifas/cerrar', {
            id: id,
            sede_id: userInfo?.sede_id,
            modificado_por: userInfo?.nombre_completo || 'SYSTEM'
        });

        if (respuesta && respuesta.ok) {
            Utils.showAlert(respuesta.msj || 'Rifa cerrada correctamente', 'success');
            tablaRifas.ajax.reload();
        } else {
            Utils.showAlert(respuesta?.msj || 'No se pudo cerrar la rifa', 'error');
        }
    } catch (error) {
        console.error('Error al cerrar rifa:', error);
        Utils.showAlert('Ocurrió un problema al cerrar la rifa', 'error');
    } finally {
        $btn.prop('disabled', false).html(originalHtml);
    }
}

async function eliminarRifa(id) {
    const confirmar = await Swal.fire({
        title: 'Eliminar rifa',
        text: '¿Seguro que deseas eliminar esta rifa? Esta acción no podrá revertirse.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmar.isConfirmed) return;

    try {
        SafeUtils.showLoading('Eliminando rifa...');
        const respuesta = await API.post('rifas/delete', {
            id,
            sede_id: userInfo.sede_id,
            modificado_por: userInfo.nombre_completo || 'SYSTEM'
        });
        SafeUtils.closeLoading();

        if (respuesta?.ok) {
            SafeUtils.showToast(respuesta.msj, 'success');
            tablaRifas.ajax.reload();
        } else {
            SafeUtils.showToast(respuesta?.msj || 'No se pudo eliminar la rifa', 'error');
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al eliminar la rifa', 'error');
        console.error(error);
    }
}

async function mostrarModalPremios(rifaId) {
    try {
        const respuesta = await API.get('rifas/getById', { id: rifaId, sede_id: userInfo.sede_id });

        if (!(respuesta?.ok && respuesta.data)) {
            Utils.showAlert(respuesta?.msj || 'No se pudo obtener la rifa', 'error');
            return;
        }

        rifaSeleccionada = respuesta.data;
        $('#premios_rifa_id_hidden').val(rifaSeleccionada.id);
        $('#premios_rifa_nombre').text(`${rifaSeleccionada.codigo} - ${rifaSeleccionada.nombre}`);
        const premiosAsociados = Array.isArray(rifaSeleccionada.premios)
            ? rifaSeleccionada.premios.map(item => item.premio_id)
            : [];
        await cargarPremiosCatalogo({ includeIds: premiosAsociados, showFeedback: true });
        limpiarFormularioPremio();
        await cargarPremiosRifa();
        modalPremios.show();
    } catch (error) {
        console.error(error);
        Utils.showAlert('Error al obtener la rifa', 'error');
    }
}

async function cargarPremiosRifa() {
    const rifaId = $('#premios_rifa_id_hidden').val();
    if (!rifaId) return;

    try {
        const respuesta = await API.get('rifas/premios/get', { rifa_id: rifaId });
        if (respuesta?.ok) {
            renderizarTablaPremios(respuesta.data || []);
        } else {
            renderizarTablaPremios([]);
            SafeUtils.showToast(respuesta?.msj || 'No se pudieron obtener los premios', 'warning');
        }
    } catch (error) {
        SafeUtils.showToast('Error al obtener los premios', 'error');
        console.error(error);
    }
}

function renderizarTablaPremios(premios) {
    const tbody = $('#tabla_premios_rifa tbody');
    tbody.empty();

    // Filtrar solo premios activos (estado = 1)
    const premiosActivos = premios.filter(p => p.estado == 1);

    if (!premiosActivos.length) {
        tbody.append('<tr><td colspan="6" class="text-center text-muted">Sin premios configurados</td></tr>');
        return;
    }

    premiosActivos.forEach((premio) => {
        const badgePrincipal = premio.es_principal ? '<span class="badge bg-primary">Sí</span>' : '<span class="badge bg-secondary">No</span>';
        const badgeEstado = premio.estado == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';

        tbody.append(`
            <tr>
                <td>
                    <strong>${premio.premio_nombre || 'Premio'}</strong>
                </td>
                <td class="text-center">${premio.orden || '-'}</td>
                <td class="text-center">${badgePrincipal}</td>
                <td class="text-center">${premio.cantidad || 1}</td>
                <td class="text-center">${badgeEstado}</td>
                <td class="text-center">
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary btn-editar-premio" data-premio='${JSON.stringify(premio)}'>
                            <i class="ri-edit-2-line"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-eliminar-premio" data-id="${premio.id}">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `);
    });

    $('.btn-editar-premio').on('click', function () {
        const data = $(this).data('premio');
        editarPremioRifa(data);
    });
    $('.btn-eliminar-premio').on('click', async function () {
        await eliminarPremioRifa($(this).data('id'));
    });
}

function editarPremioRifa(data) {
    $('#premios_rifa_id_hidden').data('premioEditar', data.id);
    $('#premio_rifa_select').val(data.premio_id);
    $('#premio_rifa_orden').val(data.orden);
    $('#premio_rifa_principal').prop('checked', data.es_principal == 1);
    $('#premio_rifa_cantidad').val(data.cantidad);
    $('#premio_rifa_valor').val(data.valor_estimado);
    $('#premio_rifa_estado').val(data.estado);
    $('#premio_rifa_titulo').val(data.titulo || '');
    $('#premio_rifa_descripcion').val(data.descripcion || '');
}

function limpiarFormularioPremio() {
    $('#premios_rifa_id_hidden').removeData('premioEditar');
    document.getElementById('form_premio_rifa').reset();
    $('#premio_rifa_estado').val(1);
    $('#premio_rifa_principal').prop('checked', false);
    $('#premio_rifa_select').prop('disabled', premiosCatalogo.length === 0);
    $('#btn_guardar_premio_rifa').prop('disabled', premiosCatalogo.length === 0);
}

async function guardarPremioRifa() {
    if (!premiosCatalogo.length) {
        SafeUtils.showToast('Registra un premio activo en el módulo de premios para poder asociarlo.', 'warning');
        return;
    }

    const form = document.getElementById('form_premio_rifa');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const rifaId = $('#premios_rifa_id_hidden').val();
    const premioEditar = $('#premios_rifa_id_hidden').data('premioEditar');
    const payload = {
        rifa_id: parseInt(rifaId, 10),
        sede_id: userInfo.sede_id,
        premio_id: parseInt($('#premio_rifa_select').val(), 10),
        orden: parseIntOrNull($('#premio_rifa_orden').val()),
        es_principal: $('#premio_rifa_principal').is(':checked') ? 1 : 0,
        titulo: $('#premio_rifa_titulo').val()?.trim() || null,
        descripcion: $('#premio_rifa_descripcion').val()?.trim() || null,
        cantidad: parseIntOrNull($('#premio_rifa_cantidad').val()) || 1,
        valor_estimado: parseFloat($('#premio_rifa_valor').val()) || null
    };

    try {
        let respuesta;
        if (premioEditar) {
            payload.id = premioEditar;
            payload.estado = parseInt($('#premio_rifa_estado').val(), 10);
            payload.modificado_por = userInfo.nombre_completo || 'SYSTEM';
            respuesta = await API.post('rifas/premios/update', payload);
        } else {
            payload.creado_por = userInfo.nombre_completo || 'SYSTEM';
            respuesta = await API.post('rifas/premios/register', payload);
        }

        if (respuesta?.ok) {
            SafeUtils.showToast(respuesta.msj, 'success');
            limpiarFormularioPremio();
            await cargarPremiosRifa();
        } else {
            SafeUtils.showToast(respuesta?.msj || 'No se pudo guardar el premio', 'error');
        }
    } catch (error) {
        SafeUtils.showToast('Error al guardar el premio', 'error');
        console.error(error);
    }
}

async function eliminarPremioRifa(id) {
    const confirmar = await Swal.fire({
        title: 'Eliminar premio',
        text: '¿Deseas quitar este premio del sorteo?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    if (!confirmar.isConfirmed) return;

    try {
        const respuesta = await API.post('rifas/premios/delete', {
            id,
            rifa_id: $('#premios_rifa_id_hidden').val(),
            sede_id: userInfo.sede_id,
            modificado_por: userInfo.nombre_completo || 'SYSTEM'
        });

        if (respuesta?.ok) {
            SafeUtils.showToast(respuesta.msj, 'success');
            await cargarPremiosRifa();
        } else {
            SafeUtils.showToast(respuesta?.msj || 'No se pudo eliminar el premio', 'error');
        }
    } catch (error) {
        SafeUtils.showToast('Error al eliminar el premio', 'error');
        console.error(error);
    }
}


function obtenerBadgeEstadoRifa(estado) {
    const map = {
        BORRADOR: { text: 'Borrador', class: 'badge-soft-secondary' },
        PUBLICADA: { text: 'Publicada', class: 'badge-soft-info' },
        EN_VENTA: { text: 'En venta', class: 'badge-soft-success' },
        CERRADA: { text: 'Cerrada', class: 'badge-soft-warning' },
        SORTEO_REALIZADO: { text: 'Sorteo realizado', class: 'badge-soft-primary' },
        FINALIZADA: { text: 'Finalizada', class: 'badge-soft-secondary' },
        CANCELADA: { text: 'Cancelada', class: 'badge-soft-danger' }
    };

    const info = map[estado] || {
        text: estado ? estado.replaceAll('_', ' ') : '-',
        class: 'badge-soft-secondary'
    };

    return `<span class="badge ${info.class}">${info.text}</span>`;
}

function formatearFechaListadoRifa(valor) {
    if (!valor) {
        return '-';
    }

    const normalizado = valor.replace(' ', 'T');
    const fecha = new Date(normalizado);

    if (!Number.isNaN(fecha.getTime())) {
        return fecha.toLocaleString('es-PE', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    const partes = valor.split(' ');
    if (partes.length === 2) {
        const [fechaStr, hora] = partes;
        const [anio, mes, dia] = fechaStr.split('-');
        if (anio && mes && dia) {
            return `${dia.padStart(2, '0')}/${mes.padStart(2, '0')}/${anio} ${hora}`;
        }
    }

    return valor;
}

function parseIntOrNull(value) {
    if (value === undefined || value === null || value === '') return null;
    const parsed = parseInt(value, 10);
    return Number.isNaN(parsed) ? null : parsed;
}

function formatearFechaInput(fecha) {
    if (!fecha) return '';
    const date = new Date(fecha);
    if (Number.isNaN(date.getTime())) return fecha;
    const pad = (n) => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
}
