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
let tablaNumeros = null;
let userInfo = null;
let premiosCatalogo = [];
let rifaSeleccionada = null;

const $modalRifa = $('#modal_rifa');
const $modalPremios = $('#modal_premios_rifa');
const $modalNumeros = $('#modal_numeros_rifa');

$(document).ready(async () => {
    if (!Auth.requireAuth()) return;

    userInfo = Auth.getUserInfo();

    inicializarSelectSede();
    inicializarFlatpickr();
    inicializarTablas();
    inicializarEventosUI();

    await cargarPremiosCatalogo();
    await cargarRifas();
});

function inicializarSelectSede() {
    const option = `<option value="${userInfo.sede_id}">${userInfo.sede_nombre || 'Sede principal'}</option>`;
    $('#filtro_sede_rifa').html(option).val(userInfo.sede_id);
    $('#sede_id_rifa').val(userInfo.sede_id);
}

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
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        columns: [
            {
                data: null,
                className: 'text-center',
                orderable: false,
                width: '140px',
                render: (_, __, row) => `
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-primary btn-editar" data-id="${row.id}" title="Editar">
                            <i class="ri-edit-2-line"></i>
                        </button>
                        <button class="btn btn-outline-primary btn-premios" data-id="${row.id}" title="Premios">
                            <i class="ri-gift-line"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-numeros" data-id="${row.id}" title="Números">
                            <i class="ri-grid-line"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-eliminar" data-id="${row.id}" title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `
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
                render: (estado) => `<span class="badge badge-soft-info">${estado.replaceAll('_', ' ')}</span>`
            },
            {
                data: 'fecha_sorteo',
                render: SafeUtils.formatDate
            }
        ]
    });

    tablaNumeros = $('#tabla_numeros_rifa').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json' },
        paging: true,
        pageLength: 25,
        columns: [
            { data: 'numero_formateado' },
            {
                data: 'estado',
                render: (estado) => `<span class="badge bg-${obtenerClaseEstadoNumero(estado)}">${estado}</span>`
            },
            {
                data: 'reservado_hasta',
                render: SafeUtils.formatDate
            },
            {
                data: null,
                render: (row) => row.nombres ? `${row.nombres} ${row.apellidos}`.trim() : '-'
            },
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: (row) => `
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary btn-seleccionar-numero" data-id="${row.id}" title="Seleccionar">
                            <i class="ri-checkbox-circle-line"></i>
                        </button>
                        <button class="btn btn-outline-danger btn-liberar-numero" data-id="${row.id}" title="Liberar">
                            <i class="ri-close-circle-line"></i>
                        </button>
                    </div>
                `
            }
        ]
    });
}

function inicializarEventosUI() {
    $('#btn_filtrar_rifas').on('click', cargarRifas);
    $('#btn_recargar_rifas').on('click', () => {
        $('#filtro_estado_rifa').val('');
        cargarRifas();
    });
    $('#btn_nueva_rifa').on('click', () => abrirModalRifa());
    $('#btn_exportar_10').on('click', () => imprimirCartillas(10));
    $('#btn_exportar_20').on('click', () => imprimirCartillas(20));

    $('#tabla_rifas tbody').on('click', '.btn-editar', async function () {
        await editarRifa($(this).data('id'));
    });
    $('#tabla_rifas tbody').on('click', '.btn-premios', async function () {
        await mostrarModalPremios($(this).data('id'));
    });
    $('#tabla_rifas tbody').on('click', '.btn-numeros', async function () {
        await mostrarModalNumeros($(this).data('id'));
    });
    $('#tabla_rifas tbody').on('click', '.btn-eliminar', async function () {
        await eliminarRifa($(this).data('id'));
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

    $('#form_asignar_numero').on('submit', async (event) => {
        event.preventDefault();
        await guardarEstadoNumero();
    });
    $('#btn_filtrar_numeros').on('click', () => cargarNumerosRifa($('#numeros_rifa_id_hidden').val()));
    $('#btn_recargar_numeros').on('click', () => {
        $('#filtro_estado_numero').val('');
        cargarNumerosRifa($('#numeros_rifa_id_hidden').val());
    });
    $('#btn_limpiar_participante').on('click', () => {
        $('#form_asignar_numero')[0].reset();
        $('#numero_id_hidden').val('');
        $('#numero_formateado_resumen').val('');
    });

    $('#tabla_numeros_rifa tbody').on('click', '.btn-seleccionar-numero', function () {
        seleccionarNumero(tablaNumeros.row($(this).closest('tr')).data());
    });
    $('#tabla_numeros_rifa tbody').on('click', '.btn-liberar-numero', function () {
        liberarNumero(tablaNumeros.row($(this).closest('tr')).data());
    });
}

async function cargarPremiosCatalogo() {
    try {
        const respuesta = await API.get('premios/getAll', { sede_id: userInfo.sede_id });
        if (respuesta?.ok) {
            premiosCatalogo = respuesta.data || [];
            const options = ['<option value="">Sin premio principal</option>']
                .concat(premiosCatalogo.map(p => `<option value="${p.id}">${p.nombre}</option>`));
            $('#premio_id, #premio_rifa_select').html(options.join(''));
        }
    } catch (error) {
        console.error('Error al cargar premios del catálogo:', error);
    }
}

async function cargarRifas() {
    try {
        SafeUtils.showLoading('Cargando rifas...');
        const params = {
            sede_id: $('#filtro_sede_rifa').val() || userInfo.sede_id,
            estado: $('#filtro_estado_rifa').val() || ''
        };
        const respuesta = await API.get('rifas/getAll', params);
        SafeUtils.closeLoading();

        if (respuesta?.ok) {
            tablaRifas.clear().rows.add(respuesta.data || []).draw();
        } else {
            tablaRifas.clear().draw();
            SafeUtils.showToast(respuesta?.msj || 'No se pudieron cargar las rifas', 'warning');
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al cargar las rifas', 'error');
        console.error(error);
    }
}

async function abrirModalRifa(detalle = null) {
    const form = document.getElementById('form_rifa');
    form.reset();
    form.classList.remove('was-validated');
    $('#rifa_id').val('');
    $('#contenedor_regenerar_numeros').toggleClass('d-none', true);
    $('#regenerar_numeros').prop('checked', false);

    if (detalle) {
        $('#modal_rifa_title').text('Editar rifa');
        $('#rifa_id').val(detalle.id);
        $('#sede_id_rifa').val(detalle.sede_id);
        $('#premio_id').val(detalle.premio_principal_id || '');
        $('#estado_rifa').val(detalle.estado);
        $('#codigo_rifa').val(detalle.codigo);
        $('#nombre_rifa').val(detalle.nombre);
        $('#precio_ticket').val(detalle.precio_ticket);
        $('#descripcion_rifa').val(detalle.descripcion || '');
        $('#numero_inicial').val(detalle.numero_inicial);
        $('#numero_final').val(detalle.numero_final);
        $('#cantidad_digitos').val(detalle.cantidad_digitos || 4);
        $('#cantidad_maxima_por_persona').val(detalle.cantidad_maxima_por_persona || 1);
        $('#cantidad_maxima_tickets').val(detalle.cantidad_maxima_tickets || '');
        $('#numeros_por_volantario').val(detalle.numeros_por_volantario || 100);
        $('#numeros_por_pagina').val(detalle.numeros_por_pagina || 10);
        $('#tipo_numeracion').val(detalle.tipo_numeracion || 'CORRELATIVO');
        $('#prefijo_numero').val(detalle.prefijo_numero || '');
        $('#sufijo_numero').val(detalle.sufijo_numero || '');
        $('#texto_promocional').val(detalle.texto_promocional || '');
        $('#reglas_participacion').val(detalle.reglas_participacion || '');
        $('#terminos_rifa').val(detalle.terminos_condiciones || '');
        $('#mostrar_contador').val(detalle.mostrar_contador || 1);
        $('#mostrar_participantes').val(detalle.mostrar_participantes || 1);
        $('#mostrar_tickets_vendidos').val(detalle.mostrar_tickets_vendidos || 1);
        $('#permitir_seleccion_numero').prop('checked', detalle.permitir_seleccion_numero == 1);
        $('#asignacion_automatica').prop('checked', detalle.asignacion_automatica == 1);
        $('#fecha_inicio_venta').val(formatearFechaInput(detalle.fecha_inicio_venta));
        $('#fecha_fin_venta').val(formatearFechaInput(detalle.fecha_fin_venta));
        $('#fecha_sorteo').val(formatearFechaInput(detalle.fecha_sorteo));

        $('#contenedor_regenerar_numeros').toggleClass('d-none', false);
    } else {
        $('#modal_rifa_title').text('Nueva rifa');
        $('#estado_rifa').val('BORRADOR');
        $('#mostrar_contador, #mostrar_participantes, #mostrar_tickets_vendidos').val(1);
        $('#permitir_seleccion_numero, #asignacion_automatica').prop('checked', true);
        $('#sede_id_rifa').val(userInfo.sede_id);
    }

    $modalRifa.modal('show');
}

async function editarRifa(id) {
    try {
        SafeUtils.showLoading('Cargando rifa...');
        const respuesta = await API.get('rifas/getById', { id, sede_id: userInfo.sede_id });
        SafeUtils.closeLoading();

        if (respuesta?.ok && respuesta.data) {
            rifaSeleccionada = respuesta.data;
            abrirModalRifa(respuesta.data);
        } else {
            SafeUtils.showToast(respuesta?.msj || 'No se pudo obtener la rifa', 'error');
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al obtener la rifa', 'error');
        console.error(error);
    }
}

function construirPayloadRifa() {
    const payload = {
        sede_id: userInfo.sede_id,
        premio_id: parseIntOrNull($('#premio_id').val()),
        codigo: $('#codigo_rifa').val().trim(),
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
        generar_volantarios: 1,
        numeros_por_volantario: parseIntOrNull($('#numeros_por_volantario').val()) || 100,
        formato_impresion: 'A4',
        numeros_por_pagina: parseIntOrNull($('#numeros_por_pagina').val()) || 10,
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

async function guardarRifa() {
    const form = document.getElementById('form_rifa');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    const payload = construirPayloadRifa();
    const rifaId = $('#rifa_id').val();
    const esEdicion = Boolean(rifaId);

    if (esEdicion) {
        payload.id = parseInt(rifaId, 10);
        payload.estado = $('#estado_rifa').val();
        payload.estado_activo = payload.estado === 'CANCELADA' ? 0 : 1;
        payload.modificado_por = userInfo.nombre_completo || 'SYSTEM';
    } else {
        payload.creado_por = userInfo.nombre_completo || 'SYSTEM';
    }

    try {
        SafeUtils.showLoading(esEdicion ? 'Actualizando rifa...' : 'Registrando rifa...');
        const endpoint = esEdicion ? 'rifas/update' : 'rifas/register';
        const respuesta = await API.post(endpoint, payload);
        SafeUtils.closeLoading();

        if (respuesta?.ok) {
            SafeUtils.showToast(respuesta.msj, 'success');
            $modalRifa.modal('hide');
            await cargarRifas();
        } else {
            SafeUtils.showToast(respuesta?.msj || 'No se pudo guardar la rifa', 'error');
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Ocurrió un problema al guardar la rifa', 'error');
        console.error(error);
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
            await cargarRifas();
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
        SafeUtils.showLoading('Cargando rifa...');
        const respuesta = await API.get('rifas/getById', { id: rifaId, sede_id: userInfo.sede_id });
        SafeUtils.closeLoading();

        if (!(respuesta?.ok && respuesta.data)) {
            SafeUtils.showToast(respuesta?.msj || 'No se pudo obtener la rifa', 'error');
            return;
        }

        rifaSeleccionada = respuesta.data;
        $('#premios_rifa_id_hidden').val(rifaSeleccionada.id);
        $('#premios_rifa_nombre').text(`${rifaSeleccionada.codigo} - ${rifaSeleccionada.nombre}`);
        limpiarFormularioPremio();
        await cargarPremiosRifa();
        $modalPremios.modal('show');
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al obtener la rifa', 'error');
        console.error(error);
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

    if (!premios.length) {
        tbody.append('<tr><td colspan="6" class="text-center text-muted">Sin premios configurados</td></tr>');
        return;
    }

    premios.forEach((premio) => {
        const badgePrincipal = premio.es_principal ? '<span class="badge bg-primary">Sí</span>' : '<span class="badge bg-secondary">No</span>';
        const badgeEstado = premio.estado == 1 ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>';

        tbody.append(`
            <tr>
                <td>
                    <strong>${premio.premio_nombre}</strong><br>
                    <small class="text-muted">${premio.titulo || ''}</small>
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
}

async function guardarPremioRifa() {
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

async function mostrarModalNumeros(rifaId) {
    try {
        SafeUtils.showLoading('Cargando rifa...');
        const respuesta = await API.get('rifas/getById', { id: rifaId, sede_id: userInfo.sede_id });
        SafeUtils.closeLoading();

        if (!(respuesta?.ok && respuesta.data)) {
            SafeUtils.showToast(respuesta?.msj || 'No se pudo obtener la rifa', 'error');
            return;
        }

        rifaSeleccionada = respuesta.data;
        $('#numeros_rifa_id_hidden').val(rifaSeleccionada.id);
        $('#numeros_rifa_nombre').text(`${rifaSeleccionada.codigo} - ${rifaSeleccionada.nombre}`);
        $('#numero_id_hidden').val('');
        $('#numero_formateado_resumen').val('');
        $('#form_asignar_numero')[0].reset();

        await cargarNumerosRifa(rifaSeleccionada.id);
        $modalNumeros.modal('show');
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al obtener la rifa', 'error');
        console.error(error);
    }
}

async function cargarNumerosRifa(rifaId) {
    if (!rifaId) return;

    try {
        SafeUtils.showLoading('Cargando números...');
        const estado = $('#filtro_estado_numero').val() || '';
        const respuesta = await API.get('rifas/numeros/get', { rifa_id: rifaId, estado });
        SafeUtils.closeLoading();

        if (respuesta?.ok) {
            tablaNumeros.clear().rows.add(respuesta.data || []).draw();
        } else {
            tablaNumeros.clear().draw();
            SafeUtils.showToast(respuesta?.msj || 'No se pudieron obtener los números', 'warning');
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al obtener los números', 'error');
        console.error(error);
    }
}

function seleccionarNumero(data) {
    if (!data) return;
    $('#numero_id_hidden').val(data.id);
    $('#numero_formateado_resumen').val(data.numero_formateado);
    $('#numero_estado').val(data.estado);
    $('#numero_reservado_hasta').val(data.reservado_hasta ? formatearFechaInput(data.reservado_hasta) : '');
    $('#numero_sesion_reserva').val(data.reservado_por_sesion || '');
}

async function liberarNumero(data) {
    if (!data) return;
    $('#numero_id_hidden').val(data.id);
    $('#numero_estado').val('DISPONIBLE');
    $('#numero_reservado_hasta').val('');
    $('#numero_sesion_reserva').val('');
    await guardarEstadoNumero();
}

async function guardarEstadoNumero() {
    const numeroId = $('#numero_id_hidden').val();
    const rifaId = $('#numeros_rifa_id_hidden').val();
    if (!numeroId || !rifaId) {
        SafeUtils.showToast('Selecciona un número antes de guardar', 'warning');
        return;
    }

    const payload = {
        numero_id: parseInt(numeroId, 10),
        rifa_id: parseInt(rifaId, 10),
        estado: $('#numero_estado').val(),
        ticket_id: null,
        reservado_hasta: $('#numero_reservado_hasta').val() || null,
        reservado_por_sesion: $('#numero_sesion_reserva').val()?.trim() || null,
        modificado_por: userInfo.nombre_completo || 'SYSTEM'
    };

    try {
        const respuesta = await API.post('rifas/numeros/update', payload);
        if (respuesta?.ok) {
            SafeUtils.showToast(respuesta.msj, 'success');
            await cargarNumerosRifa(rifaId);
            $('#numero_id_hidden').val('');
            $('#numero_formateado_resumen').val('');
            $('#form_asignar_numero')[0].reset();
        } else {
            SafeUtils.showToast(respuesta?.msj || 'No se pudo actualizar el número', 'error');
        }
    } catch (error) {
        SafeUtils.showToast('Error al actualizar el número', 'error');
        console.error(error);
    }
}

function imprimirCartillas(numerosPorPagina) {
    if (!rifaSeleccionada) {
        SafeUtils.showToast('Selecciona primero una rifa desde el botón "Números".', 'warning');
        return;
    }

    const inicio = parseInt(rifaSeleccionada.numero_inicial, 10);
    const fin = parseInt(rifaSeleccionada.numero_final, 10);
    const digitos = rifaSeleccionada.cantidad_digitos || 4;
    const prefijo = rifaSeleccionada.prefijo_numero || '';
    const sufijo = rifaSeleccionada.sufijo_numero || '';

    const win = window.open('', '_blank');
    win.document.write('<html><head><title>Cartillas de rifas</title>');
    win.document.write('<style>body{font-family:Arial;} .page{page-break-after:always;margin-bottom:24px;} .grid{display:grid;grid-template-columns:repeat(5,1fr);gap:12px;} .cell{border:1px solid #222;padding:12px;text-align:center;font-size:18px;font-weight:bold;border-radius:6px;}</style>');
    win.document.write('</head><body>');
    win.document.write(`<h2>${rifaSeleccionada.nombre} - Cartillas (${numerosPorPagina} por página)</h2>`);

    let contador = 0;
    win.document.write('<div class="page"><div class="grid">');

    for (let numero = inicio; numero <= fin; numero++) {
        const formateado = `${prefijo}${numero.toString().padStart(digitos, '0')}${sufijo}`;
        win.document.write(`<div class="cell">${formateado}</div>`);
        contador++;
        if (contador % numerosPorPagina === 0 && numero < fin) {
            win.document.write('</div></div><div class="page"><div class="grid">');
        }
    }

    win.document.write('</div></div>');
    win.document.write('</body></html>');
    win.document.close();
    win.focus();
    win.print();
}

function obtenerClaseEstadoNumero(estado) {
    switch (estado) {
        case 'DISPONIBLE': return 'success';
        case 'RESERVADO': return 'warning';
        case 'VENDIDO': return 'primary';
        case 'BLOQUEADO': return 'danger';
        default: return 'secondary';
    }
}

function formatearFechaInput(fecha) {
    if (!fecha) return '';
    const date = new Date(fecha);
    if (Number.isNaN(date.getTime())) return fecha;
    const pad = (n) => String(n).padStart(2, '0');
    return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())} ${pad(date.getHours())}:${pad(date.getMinutes())}`;
}

function parseIntOrNull(value) {
    if (value === undefined || value === null || value === '') return null;
    const parsed = parseInt(value, 10);
    return Number.isNaN(parsed) ? null : parsed;
}
