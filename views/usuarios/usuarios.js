/**
 * Gestión de Usuarios
 * Registrar, actualizar, dar de baja
 */

let tablaUsuarios;
let usuariosData = [];
let userInfo = null;
let modalUsuario;
let rolesData = [];

$(document).ready(async function () {
    if (!Auth.requireAuth()) {
        return;
    }

    userInfo = Auth.getUserInfo();
    modalUsuario = new bootstrap.Modal(document.getElementById('modal_usuario'));

    inicializarTablaUsuarios();
    inicializarEventosUsuarios();

    await Promise.all([cargarUsuarios(), cargarRoles()]);
});

function inicializarTablaUsuarios() {
    tablaUsuarios = $('#tabla_usuarios').DataTable({
        processing: false,
        serverSide: false,
        data: [],
        language: Utils.getDataTableLanguageES(),
        lengthChange: false,
        dom: 'frtip',
        columns: [
            {
                data: null,
                className: 'text-center',
                orderable: false,
                width: '100px',
                render: (_, __, row) => {
                    const deshabilitarBaja = row.estado === 0 ? 'disabled' : '';
                    return `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary btn-editar-usuario" data-id="${row.id}" title="Editar">
                            <i class="ri-edit-2-line"></i>
                        </button>
                        <button class="btn btn-sm btn-warning btn-baja-usuario" data-id="${row.id}" ${deshabilitarBaja} title="Dar de baja">
                            <i class="ri-user-unfollow-line"></i>
                        </button>
                    </div>
                    `;
                }
            },
            { data: 'username' },
            { data: 'email', render: (d) => d || '-' },
            {
                data: null,
                render: (_, __, row) => {
                    const n = [row.primer_nombre, row.apellido_paterno, row.apellido_materno].filter(Boolean).join(' ');
                    return n || '-';
                }
            },
            { data: 'telefono', render: (d) => d || '-' },
            { data: 'rol_nombre', render: (d) => d || '-' },
            {
                data: 'estado',
                render: (estado) => {
                    const map = {
                        0: { text: 'Inactivo', class: 'badge-soft-secondary' },
                        1: { text: 'Activo', class: 'badge-soft-success' }
                    };
                    const info = map[estado] ?? map[0];
                    return `<span class="badge ${info.class}">${info.text}</span>`;
                }
            },
            {
                data: 'fecha_creacion',
                render: (fecha) => Utils.formatearFecha(fecha?.split(' ')[0] ?? '')
            }
        ]
    });
}

function inicializarEventosUsuarios() {
    $('#btn_nuevo_usuario').on('click', () => abrirModalUsuario());

    $('#btn_filtrar_usuarios').on('click', () => cargarUsuarios());
    $('#btn_recargar_usuarios').on('click', () => {
        $('#filtro_estado_usuarios').val('');
        cargarUsuarios();
    });

    $('#tabla_usuarios tbody').on('click', '.btn-editar-usuario', async function () {
        const id = $(this).data('id');
        await editarUsuario(id);
    });

    $('#tabla_usuarios tbody').on('click', '.btn-baja-usuario', function () {
        const id = $(this).data('id');
        darDeBajaUsuario(id);
    });

    $('#form_usuario').on('submit', async function (event) {
        event.preventDefault();
        await guardarUsuario();
    });

    $('#form_usuario').on('input change', 'input, select, textarea', function () {
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

async function cargarUsuarios() {
    if (!userInfo) return;

    try {
        Utils.showLoading('Cargando usuarios...');

        const sedeId = userInfo.sede_id;
        const estado = $('#filtro_estado_usuarios').val();
        const params = { sede_id: sedeId };
        if (estado !== '') params.estado = estado;

        const respuesta = await API.get('usuarios/getAll', params);
        Utils.closeLoading();

        if (respuesta && respuesta.ok) {
            usuariosData = respuesta.data || [];
            tablaUsuarios.clear().rows.add(usuariosData).draw();
        } else {
            usuariosData = [];
            tablaUsuarios.clear().draw();
            Utils.showToast(respuesta?.msj || 'No se pudo obtener usuarios', 'warning');
        }
    } catch (error) {
        Utils.closeLoading();
        console.error('Error al cargar usuarios:', error);
        usuariosData = [];
        tablaUsuarios.clear().draw();
        Utils.showToast('Error de conexión al cargar usuarios', 'error');
    }
}

function abrirModalUsuario(usuario = null) {
    limpiarFormularioUsuario();
    $('#usuario_sede_id').val(userInfo?.sede_id || '');
    // Asegurar que el select de roles tenga opciones (por si se abrió antes de cargarRoles)
    if ($('#usuario_rol_id option').length <= 1 && rolesData.length) {
        const $sel = $('#usuario_rol_id');
        $sel.find('option:not(:first)').remove();
        rolesData.forEach(r => {
            $sel.append(`<option value="${r.id}">${r.nombre}${r.descripcion ? ' - ' + r.descripcion : ''}</option>`);
        });
    }

    if (usuario) {
        $('#modal_usuario_title').text('Editar Usuario');
        $('#usuario_id').val(usuario.id);
        $('#usuario_username').val(usuario.username || '');
        $('#usuario_email').val(usuario.email || '');
        $('#usuario_primer_nombre').val(usuario.primer_nombre || '');
        $('#usuario_apellido_paterno').val(usuario.apellido_paterno || '');
        $('#usuario_apellido_materno').val(usuario.apellido_materno || '');
        $('#usuario_telefono').val(usuario.telefono || '');
        $('#usuario_rol_id').val(usuario.rol_id || '');
        $('#usuario_estado').val(usuario.estado ?? 1);
        $('#usuario_password').prop('required', false).val('');
        $('#cont_usuario_password label').html('Contraseña <small class="text-muted">(dejar en blanco para no cambiar)</small>');
        $('#cont_usuario_estado').show();
    } else {
        $('#modal_usuario_title').text('Nuevo Usuario');
        $('#usuario_estado').val(1);
        $('#usuario_rol_id').val('');
        $('#usuario_password').prop('required', true).val('');
        $('#cont_usuario_password label').html('Contraseña <span class="text-danger">*</span>');
        $('#cont_usuario_estado').hide();
    }

    modalUsuario.show();
}

async function editarUsuario(id) {
    try {
        Utils.showLoading('Cargando usuario...');
        const respuesta = await API.get('usuarios/getById', {
            id: id,
            sede_id: userInfo?.sede_id
        });
        Utils.closeLoading();

        if (respuesta && respuesta.ok && respuesta.data) {
            abrirModalUsuario(respuesta.data);
        } else {
            Utils.showToast(respuesta?.msj || 'No se pudo obtener el usuario', 'error');
        }
    } catch (error) {
        console.error('Error al obtener usuario:', error);
        Utils.closeLoading();
        Utils.showToast('Ocurrió un problema al obtener el usuario', 'error');
    }
}

function validarFormularioUsuario(esEdicion) {
    let esValido = true;
    Utils.limpiarValidaciones('form_usuario');

    if (!Utils.validarCampo('usuario_username', 'El usuario (login) es obligatorio')) esValido = false;
    if (!Utils.validarCampo('usuario_email', 'El email es obligatorio')) esValido = false;
    if (!Utils.validarCampo('usuario_primer_nombre', 'Los nombres son obligatorios')) esValido = false;
    if (!Utils.validarCampo('usuario_apellido_paterno', 'El apellido paterno es obligatorio')) esValido = false;

    if (!esEdicion) {
        const pass = $('#usuario_password').val() || '';
        if (pass.length < 6) {
            $('#usuario_password').addClass('is-invalid');
            $('#usuario_password_error').text('La contraseña debe tener al menos 6 caracteres');
            esValido = false;
        }
    }

    return esValido;
}

async function guardarUsuario() {
    const usuarioId = $('#usuario_id').val();
    const esEdicion = usuarioId !== '';

    if (!validarFormularioUsuario(esEdicion)) return;

    const payload = {
        sede_id: userInfo?.sede_id || '',
        username: $('#usuario_username').val().trim(),
        email: $('#usuario_email').val().trim(),
        primer_nombre: $('#usuario_primer_nombre').val().trim(),
        apellido_paterno: $('#usuario_apellido_paterno').val().trim(),
        apellido_materno: $('#usuario_apellido_materno').val()?.trim() || '',
        telefono: $('#usuario_telefono').val()?.trim() || '',
        rol_id: $('#usuario_rol_id').val() ? parseInt($('#usuario_rol_id').val(), 10) : null
    };

    if (esEdicion) {
        payload.id = parseInt(usuarioId, 10);
        payload.estado = $('#usuario_estado').val();
        payload.modificado_por = userInfo?.nombre_completo || 'SYSTEM';
    } else {
        payload.password = $('#usuario_password').val();
        payload.creado_por = userInfo?.nombre_completo || 'SYSTEM';
    }

    try {
        Utils.showLoading(esEdicion ? 'Actualizando usuario...' : 'Registrando usuario...');
        const endpoint = esEdicion ? 'usuarios/update' : 'usuarios/register';
        const respuesta = await API.post(endpoint, payload);
        Utils.closeLoading();

        if (respuesta && respuesta.ok) {
            Utils.showToast(respuesta.msj, 'success');
            modalUsuario.hide();
            await cargarUsuarios();
        } else {
            Utils.showToast(respuesta?.msj || 'No se pudo guardar el usuario', 'error');
        }
    } catch (error) {
        console.error('Error al guardar usuario:', error);
        Utils.closeLoading();
        Utils.showToast('Ocurrió un problema al guardar', 'error');
    }
}

function limpiarFormularioUsuario() {
    const form = document.getElementById('form_usuario');
    form.reset();
    Utils.limpiarValidaciones('form_usuario');
    $('#usuario_id').val('');
    $('#usuario_sede_id').val(userInfo?.sede_id || '');
}

function darDeBajaUsuario(id) {
    Swal.fire({
        title: '¿Dar de baja al usuario?',
        text: 'El usuario dejará de poder iniciar sesión. Puede reactivarlo editando y cambiando el estado.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, dar de baja',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#f0ad4e',
        cancelButtonColor: '#6c757d'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                Utils.showLoading('Procesando...');
                const respuesta = await API.post('usuarios/disable', {
                    id: id,
                    sede_id: userInfo?.sede_id,
                    modificado_por: userInfo?.nombre_completo || 'SYSTEM'
                });
                Utils.closeLoading();

                if (respuesta && respuesta.ok) {
                    Utils.showToast(respuesta.msj, 'success');
                    await cargarUsuarios();
                } else {
                    Utils.showToast(respuesta?.msj || 'No se pudo dar de baja', 'error');
                }
            } catch (error) {
                console.error('Error al dar de baja:', error);
                Utils.closeLoading();
                Utils.showToast('Ocurrió un problema', 'error');
            }
        }
    });
}
