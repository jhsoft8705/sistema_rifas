/**
 * Gestión de Datos de la Organización (Sede)
 * Listado = getById de la sede actual; solo actualizar.
 */

let userInfo = null;

$(document).ready(async function () {
    if (!Auth.requireAuth()) {
        return;
    }

    userInfo = Auth.getUserInfo();
    if (!userInfo || !userInfo.sede_id) {
        Utils.showToast('No se pudo obtener la sede actual', 'error');
        return;
    }

    inicializarEventosOrganizacion();
    await cargarOrganizacion();
});

function inicializarEventosOrganizacion() {
    $('#form_organizacion').on('submit', async function (event) {
        event.preventDefault();
        await guardarOrganizacion();
    });

    $('#form_organizacion').on('input change', 'input, select, textarea', function () {
        if (!this.id) return;
        if ($(this).hasClass('is-invalid')) {
            const value = $(this).val();
            const tieneValor = (value !== null && value !== undefined && String(value).trim() !== '');
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

async function cargarOrganizacion() {
    if (!userInfo || !userInfo.sede_id) return;

    try {
        Utils.showLoading('Cargando datos de la organización...');

        const respuesta = await API.get('organizacion/getById', { id: userInfo.sede_id });
        Utils.closeLoading();

        if (respuesta && respuesta.ok && respuesta.data) {
            rellenarFormulario(respuesta.data);
        } else {
            Utils.showToast(respuesta?.msj || 'No se pudo cargar la organización', 'warning');
        }
    } catch (error) {
        Utils.closeLoading();
        console.error('Error al cargar organización:', error);
        Utils.showToast('Error de conexión al cargar la organización', 'error');
    }
}

function rellenarFormulario(d) {
    $('#organizacion_id').val(d.id || '');
    $('#organizacion_codigo').val(d.codigo || '');
    $('#organizacion_nombre').val(d.nombre || '');
    $('#organizacion_pais').val(d.pais || '');
    $('#organizacion_descripcion').val(d.descripcion || '');
    $('#organizacion_direccion').val(d.direccion || '');
    $('#organizacion_telefono').val(d.telefono || '');
    $('#organizacion_email').val(d.email || '');
    $('#organizacion_moneda').val(d.moneda || '');
    $('#organizacion_simbolo_moneda').val(d.simbolo_moneda || '');
    $('#organizacion_codigo_moneda').val(d.codigo_moneda || '');
    $('#organizacion_zona_horaria').val(d.zona_horaria || '');
    $('#organizacion_url_logo').val(d.url_logo || '');
    $('#organizacion_url_landing').val(d.url_landing || '');
    $('#organizacion_dias_validez_ticket').val(d.dias_validez_ticket ?? 90);
    $('#organizacion_estado').val(d.estado ?? 1);
}

function validarFormularioOrganizacion() {
    let esValido = true;
    Utils.limpiarValidaciones('form_organizacion');

    if (!Utils.validarCampo('organizacion_codigo', 'El código es obligatorio')) {
        esValido = false;
    }
    if (!Utils.validarCampo('organizacion_nombre', 'El nombre es obligatorio')) {
        esValido = false;
    }

    return esValido;
}

async function guardarOrganizacion() {
    if (!validarFormularioOrganizacion()) {
        return;
    }

    const id = $('#organizacion_id').val();
    if (!id) {
        Utils.showToast('No hay organización cargada para actualizar', 'warning');
        return;
    }

    const payload = {
        id: parseInt(id, 10),
        codigo: $('#organizacion_codigo').val().trim(),
        nombre: $('#organizacion_nombre').val().trim(),
        pais: $('#organizacion_pais').val()?.trim() || null,
        descripcion: $('#organizacion_descripcion').val()?.trim() || null,
        direccion: $('#organizacion_direccion').val()?.trim() || null,
        telefono: $('#organizacion_telefono').val()?.trim() || null,
        email: $('#organizacion_email').val()?.trim() || null,
        moneda: $('#organizacion_moneda').val()?.trim() || null,
        simbolo_moneda: $('#organizacion_simbolo_moneda').val()?.trim() || null,
        codigo_moneda: $('#organizacion_codigo_moneda').val()?.trim() || null,
        zona_horaria: $('#organizacion_zona_horaria').val()?.trim() || null,
        url_logo: $('#organizacion_url_logo').val()?.trim() || null,
        url_landing: $('#organizacion_url_landing').val()?.trim() || null,
        dias_validez_ticket: $('#organizacion_dias_validez_ticket').val() ? parseInt($('#organizacion_dias_validez_ticket').val(), 10) : null,
        estado: $('#organizacion_estado').val() ? parseInt($('#organizacion_estado').val(), 10) : null,
        modificado_por: userInfo?.nombre_completo || 'SYSTEM'
    };

    try {
        Utils.showLoading('Guardando cambios...');
        const respuesta = await API.post('organizacion/update', payload);
        Utils.closeLoading();

        if (respuesta && respuesta.ok) {
            Utils.showToast(respuesta.msj, 'success');
            await cargarOrganizacion();
        } else {
            Utils.showToast(respuesta?.msj || 'No se pudo actualizar la organización', 'error');
        }
    } catch (error) {
        console.error('Error al guardar organización:', error);
        Utils.closeLoading();
        Utils.showToast('Ocurrió un problema al guardar', 'error');
    }
}
