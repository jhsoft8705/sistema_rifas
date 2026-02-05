/**
 * Gestión de Usuarios
 * Registrar, actualizar, dar de baja
 */

let tablaUsuarios;
let usuariosData = [];
let userInfo = null;
let modalUsuario;
let rolesData = [];

$(document).ready(function () {
    if (!Auth.requireAuth()) {
        return;
    }

    userInfo = Auth.getUserInfo();
    modalUsuario = new bootstrap.Modal(document.getElementById('modal_usuario'));

    inicializarTablaUsuarios();
    inicializarEventosUsuarios();
});

function inicializarTablaUsuarios() {
    tablaUsuarios = $('#tabla_usuarios').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.API_BASE_URL + '/usuarios/getAll',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            },
            data: function (d) {
                d.sede_id = userInfo?.sede_id || '';
                const estado = $('#filtro_estado_usuarios').val();
                if (estado !== '') {
                    d.estado = estado;
                }
                return d;
            },
            dataSrc: function (json) {
                if (json && json.ok) {
                    usuariosData = json.data || [];
                    return usuariosData;
                } else {
                    usuariosData = [];
                    return [];
                }
            },
            error: function (xhr, error, thrown) {
                console.error('Error al cargar usuarios:', error);
                usuariosData = [];
                if (xhr.status === 401) {
                    Auth.logout();
                } else {
                    Utils.showAlert('Error de conexión al cargar los usuarios', 'error');
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
                width: '100px',
                render: (_, __, row) => {
                    const deshabilitarBaja = row.estado === 0 ? 'disabled' : '';
                    return `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary btn-editar btn-action-table" data-id="${row.id}" title="Editar">
                            <i class="ri-edit-2-line"></i>
                        </button>
                        <button class="btn btn-sm btn-warning btn-baja btn-action-table" data-id="${row.id}" ${deshabilitarBaja} title="Dar de baja">
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
                render: (fecha) => Utils.formatearFecha(fecha)
            }
        ]
    });
}

function inicializarEventosUsuarios() {
    // Botón nuevo usuario - abrir modal primero
    $('#btn_nuevo_usuario').on('click', function () {
        abrirModalUsuario();
    });

    // Cargar roles cuando el modal esté completamente abierto (solo para nuevo registro)
    $('#modal_usuario').on('shown.bs.modal', function () {
        // Solo cargar si es nuevo registro (no tiene usuario_id)
        if (!$('#usuario_id').val()) {
            cargarRolesSelect();
        }
    });

    // Botones de filtro
    $('#btn_filtrar_usuarios').on('click', function () {
        tablaUsuarios.ajax.reload();
    });

    $('#btn_recargar_usuarios').on('click', function () {
        $('#filtro_estado_usuarios').val('');
        tablaUsuarios.ajax.reload();
    });

    // Eventos de tabla
    $('#tabla_usuarios tbody').on('click', '.btn-editar', function () {
        const id = $(this).data('id');
        editarUsuario(id);
    });

    $('#tabla_usuarios tbody').on('click', '.btn-baja', function () {
        const id = $(this).data('id');
        darDeBajaUsuario(id);
    });

    // Submit del formulario
    $('#form_usuario').on('submit', async function (event) {
        event.preventDefault();
        await guardarUsuario();
    });

    // Validación
    $('#form_usuario').on('input change', 'input, select, textarea', function () {
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

async function cargarRolesSelect() {
    if (!userInfo) return;

    const $select = $('#usuario_rol_id');
    if (!$select.length) return;

    const valorSeleccionado = $select.val();

    $select.prop('disabled', true);
    $select.html('<option value="">Cargando roles...</option>');

    try {
        const respuesta = await API.get('usuarios/getRoles', {
            sede_id: userInfo.sede_id
        });

        if (respuesta && respuesta.ok) {
            rolesData = respuesta.data || [];
            if (rolesData.length) {
                const opciones = ['<option value="">Seleccione rol...</option>'].concat(
                    rolesData.map(rol => {
                        const texto = rol.descripcion ? `${rol.nombre} - ${rol.descripcion}` : rol.nombre;
                        return `<option value="${rol.id}">${texto}</option>`;
                    })
                );
                $select.html(opciones.join(''));
            } else {
                rolesData = [];
                $select.html('<option value="">Sin roles registrados</option>');
            }
        } else {
            rolesData = [];
            $select.html('<option value="">No se pudieron cargar roles</option>');
            if (respuesta?.msj) {
                Utils.showAlert(respuesta.msj, 'warning');
            }
        }
    } catch (error) {
        console.error('Error al cargar roles:', error);
        rolesData = [];
        $select.html('<option value="">Error al cargar roles</option>');
        Utils.showAlert('Ocurrió un problema al cargar los roles', 'error');
    } finally {
        if (valorSeleccionado) {
            $select.val(valorSeleccionado);
        }
        $select.prop('disabled', false);
    }
}

function abrirModalUsuario(usuario = null) {
    limpiarFormularioUsuario();

    $('#usuario_sede_id').val(userInfo?.sede_id || '');

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

    // Abrir modal primero - el select se cargará cuando el modal esté completamente abierto
    modalUsuario.show();
}

async function editarUsuario(id) {
    // Deshabilitar botón mientras carga
    const $btn = $(`button.btn-editar[data-id="${id}"]`);
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

    try {
        // Precargar datos y roles en paralelo antes de abrir el modal
        const [respuestaUsuario, respuestaRoles] = await Promise.all([
            API.get('usuarios/getById', {
                id: id,
                sede_id: userInfo?.sede_id
            }),
            API.get('usuarios/getRoles', {
                sede_id: userInfo.sede_id
            })
        ]);

        // Restaurar botón
        $btn.prop('disabled', false).html(originalHtml);

        if (!respuestaUsuario || !respuestaUsuario.ok || !respuestaUsuario.data) {
            Utils.showAlert(respuestaUsuario?.msj || 'No se pudo obtener el usuario', 'error');
            return;
        }

        // Cargar roles en el select
        if (respuestaRoles && respuestaRoles.ok) {
            rolesData = respuestaRoles.data || [];
            const $select = $('#usuario_rol_id');
            if (rolesData.length) {
                const opciones = ['<option value="">Seleccione rol...</option>'].concat(
                    rolesData.map(rol => {
                        const texto = rol.descripcion ? `${rol.nombre} - ${rol.descripcion}` : rol.nombre;
                        return `<option value="${rol.id}">${texto}</option>`;
                    })
                );
                $select.html(opciones.join(''));
            } else {
                $select.html('<option value="">Sin roles registrados</option>');
            }
        }

        // Llenar formulario con los datos
        const usuario = respuestaUsuario.data;
        limpiarFormularioUsuario();
        
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

        $('#usuario_sede_id').val(userInfo?.sede_id || '');

        // Abrir modal con todo listo
        modalUsuario.show();
    } catch (error) {
        // Restaurar botón en caso de error
        $btn.prop('disabled', false).html(originalHtml);
        console.error('Error al obtener usuario:', error);
        Utils.showAlert('Ocurrió un problema al obtener el usuario', 'error');
    }
}

function validarFormularioUsuario() {
    let esValido = true;
    Utils.limpiarValidaciones('form_usuario');

    const usuarioId = $('#usuario_id').val();
    const esEdicion = usuarioId !== '';

    const camposObligatorios = [
        { id: 'usuario_username', mensaje: 'El usuario (login) es obligatorio' },
        { id: 'usuario_email', mensaje: 'El email es obligatorio' },
        { id: 'usuario_primer_nombre', mensaje: 'Los nombres son obligatorios' },
        { id: 'usuario_apellido_paterno', mensaje: 'El apellido paterno es obligatorio' },
        { id: 'usuario_rol_id', mensaje: 'El rol es obligatorio' }
    ];

    camposObligatorios.forEach((campo) => {
        if (!Utils.validarCampo(campo.id, campo.mensaje)) {
            esValido = false;
        }
    });

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
    if (!validarFormularioUsuario()) {
        return;
    }

    const usuarioId = $('#usuario_id').val();
    const esEdicion = usuarioId !== '';

    // Deshabilitar botón de guardar para evitar doble clic
    const $btnGuardar = $('#form_usuario button[type="submit"]');
    const originalBtnHtml = $btnGuardar.html();
    $btnGuardar.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-1"></i>Guardando...');

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
        const endpoint = esEdicion ? 'usuarios/update' : 'usuarios/register';
        const respuesta = await API.post(endpoint, payload);

        // Restaurar botón
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);

        if (respuesta && respuesta.ok) {
            Utils.showAlert(respuesta.msj || (esEdicion ? 'Usuario actualizado correctamente' : 'Usuario registrado correctamente'), 'success');
            modalUsuario.hide();
            // Recargar tabla sin mostrar loading manual
            tablaUsuarios.ajax.reload();
        } else {
            // Mostrar el mensaje del servidor o uno genérico
            const mensajeError = respuesta?.msj || 'No se pudo guardar el usuario';
            Utils.showAlert(mensajeError, 'error');
            console.error('Error al guardar:', respuesta);
        }
    } catch (error) {
        // Restaurar botón en caso de error
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);
        console.error('Error al guardar usuario:', error);
        Utils.showAlert('Ocurrió un problema al guardar el usuario', 'error');
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
    Utils.showConfirm(
        '¿Dar de baja al usuario?',
        'El usuario dejará de poder iniciar sesión. Puede reactivarlo editando y cambiando el estado.',
        'Sí, dar de baja',
        'Cancelar'
    ).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const respuesta = await API.post('usuarios/disable', {
                    id: id,
                    sede_id: userInfo?.sede_id,
                    modificado_por: userInfo?.nombre_completo || 'SYSTEM'
                });

                if (respuesta && respuesta.ok) {
                    Utils.showAlert(respuesta.msj, 'success');
                    // Recargar tabla sin mostrar loading manual
                    tablaUsuarios.ajax.reload();
                } else {
                    Utils.showAlert(respuesta?.msj || 'No se pudo dar de baja al usuario', 'error');
                }
            } catch (error) {
                console.error('Error al dar de baja:', error);
                Utils.showAlert('Ocurrió un problema al dar de baja al usuario', 'error');
            }
        }
    });
}
