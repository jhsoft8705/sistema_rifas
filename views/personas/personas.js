/**
 * Gestión de Personas
 */

let tablaPersonas;
let personasData = [];
let userInfo = null;
let modalPersona;

$(document).ready(function () {
    if (!Auth.requireAuth()) {
        return;
    }

    userInfo = Auth.getUserInfo();
    modalPersona = new bootstrap.Modal(document.getElementById('modal_persona'));

    $('#sede_id').val(userInfo?.sede_id || '');
    
    inicializarTabla();
    inicializarEventos();
});

function inicializarTabla() {
    tablaPersonas = $('#tabla_personas').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.API_BASE_URL + '/personas/getAll',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            },
            data: function (d) {
                d.sede_id = userInfo?.sede_id || '';
                return d;
            },
            dataSrc: function (json) {
                if (json && json.ok) {
                    personasData = json.data || [];
                    return personasData;
                } else {
                    personasData = [];
                    return [];
                }
            },
            error: function (xhr, error, thrown) {
                console.error('Error al cargar personas:', error);
                personasData = [];
                if (xhr.status === 401) {
                    Auth.logout();
                } else {
                    Utils.showAlert('Error de conexión al cargar las personas', 'error');
                }
            }
        },
        language: Utils.getDataTableLanguageES(),
        lengthChange: false,
        dom: 'frtip',
        autoWidth: false,
        columns: [
            {
                data: null,
                className: 'text-center',
                orderable: false,
                width: '80px',
                render: (_, __, row) => `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary btn-editar btn-action-table" data-id="${row.id}" title="Editar">
                            <i class="ri-edit-2-line"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar btn-action-table" data-id="${row.id}" title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `
            },
            { 
                data: 'nombre_completo',
                render: (data) => data || '-'
            },
            { 
                data: 'documento_completo',
                render: (data) => data || '-'
            },
            { 
                data: 'email',
                render: (data) => data || '-'
            },
            { 
                data: 'telefono',
                render: (data) => data || '-'
            },
            { 
                data: 'total_tickets',
                className: 'text-center',
                render: (data) => `<span class="badge bg-info">${data || 0}</span>`
            },
            { 
                data: 'total_rifas_participadas',
                className: 'text-center',
                render: (data) => `<span class="badge bg-primary">${data || 0}</span>`
            },
            {
                data: 'fecha_creacion',
                render: (fecha) => Utils.formatearFecha(fecha)
            }
        ]
    });
}

function inicializarEventos() {
    // Botón nueva persona
    $('#btn_nuevo_persona').on('click', function () {
        abrirModalPersona();
    });

    // Botón recargar
    $('#btn_recargar_personas').on('click', function () {
        tablaPersonas.ajax.reload();
    });

    // Eventos de tabla
    $('#tabla_personas tbody').on('click', '.btn-editar', function () {
        const id = $(this).data('id');
        editarPersona(id);
    });

    $('#tabla_personas tbody').on('click', '.btn-eliminar', function () {
        const id = $(this).data('id');
        eliminarPersona(id);
    });

    // Submit del formulario
    $('#form_persona').on('submit', async function (event) {
        event.preventDefault();
        await guardarPersona();
    });

    // Validación
    $('#nombres, #apellidos, #tipo_documento, #numero_documento').on('blur', function() {
        const campo = this.id;
        const mensaje = {
            'nombres': 'Los nombres son obligatorios',
            'apellidos': 'Los apellidos son obligatorios',
            'tipo_documento': 'El tipo de documento es obligatorio',
            'numero_documento': 'El número de documento es obligatorio'
        }[campo];
        if (mensaje) {
            Utils.validarCampo(campo, mensaje);
        }
    });

    $('#form_persona').on('input change', 'input, select', function () {
        if (!this.id) {
            return;
        }
        if ($(this).hasClass('is-invalid')) {
            const value = $(this).val();
            const tieneValor = value !== null && value !== undefined && String(value).trim() !== '';
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

function abrirModalPersona(persona = null) {
    limpiarFormularioPersona();

    $('#sede_id').val(userInfo?.sede_id || '');

    if (persona) {
        $('#modal_persona_title').text('Editar Persona');
        $('#persona_id').val(persona.id);
        $('#nombres').val(persona.nombres || '');
        $('#apellidos').val(persona.apellidos || '');
        $('#tipo_documento').val(persona.tipo_documento || '');
        $('#numero_documento').val(persona.numero_documento || '');
        $('#email').val(persona.email || '');
        $('#telefono').val(persona.telefono || '');
        $('#direccion').val(persona.direccion || '');
        $('#ciudad').val(persona.ciudad || '');
        $('#pais').val(persona.pais || 'Perú');
    } else {
        $('#modal_persona_title').text('Nueva Persona');
        $('#pais').val('Perú');
    }

    modalPersona.show();
}

async function editarPersona(id) {
    // Deshabilitar botón mientras carga
    const $btn = $(`button.btn-editar[data-id="${id}"]`);
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin"></i>');

    try {
        const respuesta = await API.get('personas/getById', {
            id: id,
            sede_id: userInfo?.sede_id
        });

        // Restaurar botón
        $btn.prop('disabled', false).html(originalHtml);

        if (!respuesta || !respuesta.ok || !respuesta.data) {
            Utils.showAlert(respuesta?.msj || 'No se pudo obtener la persona', 'error');
            return;
        }

        // Llenar formulario con los datos
        const persona = respuesta.data;
        limpiarFormularioPersona();
        
        $('#modal_persona_title').text('Editar Persona');
        $('#persona_id').val(persona.id);
        $('#nombres').val(persona.nombres || '');
        $('#apellidos').val(persona.apellidos || '');
        $('#tipo_documento').val(persona.tipo_documento || '');
        $('#numero_documento').val(persona.numero_documento || '');
        $('#email').val(persona.email || '');
        $('#telefono').val(persona.telefono || '');
        $('#direccion').val(persona.direccion || '');
        $('#ciudad').val(persona.ciudad || '');
        $('#pais').val(persona.pais || 'Perú');

        $('#sede_id').val(userInfo?.sede_id || '');

        // Abrir modal con todo listo
        modalPersona.show();
    } catch (error) {
        // Restaurar botón en caso de error
        $btn.prop('disabled', false).html(originalHtml);
        console.error('Error al obtener persona:', error);
        Utils.showAlert('Ocurrió un problema al obtener la persona', 'error');
    }
}

async function guardarPersona() {
    if (!validarFormularioPersona()) {
        return;
    }

    const personaId = $('#persona_id').val();
    const esEdicion = personaId !== '';

    // Deshabilitar botón de guardar para evitar doble clic
    const $btnGuardar = $('#form_persona button[type="submit"]');
    const originalBtnHtml = $btnGuardar.html();
    $btnGuardar.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-1"></i>Guardando...');

    const payload = {
        sede_id: userInfo?.sede_id || '',
        nombres: $('#nombres').val().trim(),
        apellidos: $('#apellidos').val().trim(),
        tipo_documento: $('#tipo_documento').val(),
        numero_documento: $('#numero_documento').val().trim(),
        email: $('#email').val()?.trim() || null,
        telefono: $('#telefono').val()?.trim() || null,
        direccion: $('#direccion').val()?.trim() || null,
        ciudad: $('#ciudad').val()?.trim() || null,
        pais: $('#pais').val()?.trim() || 'Perú'
    };

    if (esEdicion) {
        payload.id = personaId;
        payload.modificado_por = userInfo?.nombre_completo || 'SYSTEM';
    } else {
        payload.creado_por = userInfo?.nombre_completo || 'SYSTEM';
    }

    try {
        const endpoint = esEdicion ? 'personas/update' : 'personas/register';
        const respuesta = await API.post(endpoint, payload);

        // Restaurar botón
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);

        if (respuesta && respuesta.ok) {
            Utils.showAlert(respuesta.msj || (esEdicion ? 'Persona actualizada correctamente' : 'Persona registrada correctamente'), 'success');
            modalPersona.hide();
            // Recargar tabla
            tablaPersonas.ajax.reload();
        } else {
            // Mostrar el mensaje del servidor o uno genérico
            const mensajeError = respuesta?.msj || 'No se pudo guardar la persona';
            Utils.showAlert(mensajeError, 'error');
            console.error('Error al guardar:', respuesta);
        }
    } catch (error) {
        // Restaurar botón en caso de error
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);
        console.error('Error al guardar persona:', error);
        Utils.showAlert('Ocurrió un problema al guardar la persona', 'error');
    }
}

function limpiarFormularioPersona() {
    const form = document.getElementById('form_persona');
    form.reset();
    Utils.limpiarValidaciones('form_persona');
    $('#persona_id').val('');
    $('#sede_id').val(userInfo?.sede_id || '');
    $('#pais').val('Perú');
}

function validarFormularioPersona() {
    let esValido = true;
    Utils.limpiarValidaciones('form_persona');

    const camposObligatorios = [
        { id: 'nombres', mensaje: 'Los nombres son obligatorios' },
        { id: 'apellidos', mensaje: 'Los apellidos son obligatorios' },
        { id: 'tipo_documento', mensaje: 'El tipo de documento es obligatorio' },
        { id: 'numero_documento', mensaje: 'El número de documento es obligatorio' }
    ];

    camposObligatorios.forEach((campo) => {
        if (!Utils.validarCampo(campo.id, campo.mensaje)) {
            esValido = false;
        }
    });

    return esValido;
}

function eliminarPersona(id) {
    Utils.showConfirm(
        '¿Eliminar persona?',
        'Esta acción eliminará permanentemente la persona. Solo se puede eliminar si no tiene tickets asociados.',
        'Sí, eliminar',
        'Cancelar'
    ).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const respuesta = await API.post('personas/delete', {
                    id: id,
                    sede_id: userInfo?.sede_id
                });

                if (respuesta && respuesta.ok) {
                    Utils.showAlert(respuesta.msj, 'success');
                    // Recargar tabla
                    tablaPersonas.ajax.reload();
                } else {
                    Utils.showAlert(respuesta?.msj || 'No se pudo eliminar la persona', 'error');
                }
            } catch (error) {
                console.error('Error al eliminar persona:', error);
                Utils.showAlert('Ocurrió un problema al eliminar la persona', 'error');
            }
        }
    });
}
