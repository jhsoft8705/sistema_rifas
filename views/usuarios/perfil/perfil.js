/**
 * Gestión de Perfil de Usuario
 * Actualizar datos y cambiar contraseña
 */

let perfilData = null;
let userInfo = null;

// Función helper para hacer peticiones PUT/POST
async function apiPutOrPost(endpoint, data) {
    if (typeof API === 'undefined') {
        throw new Error('API no está disponible');
    }
    
    // Intentar usar PUT si está disponible, sino usar POST
    if (typeof API.put === 'function') {
        return await API.put(endpoint, data);
    } else {
        console.warn('API.put no disponible, usando API.post');
        return await API.post(endpoint, data);
    }
}

$(document).ready(async function () {
    if (!Auth.requireAuth()) {
        return;
    }

    // Verificar que API esté disponible
    if (typeof API === 'undefined') {
        console.error('API no está disponible. Verifique que Auth.js se haya cargado correctamente.');
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar los módulos necesarios. Por favor, recargue la página.'
        });
        return;
    }

    userInfo = Auth.getUserInfo();
    
    inicializarEventosPerfil();
    await cargarPerfil();
});

/**
 * Inicializar eventos del formulario
 */
function inicializarEventosPerfil() {
    // Formulario de datos
    $('#form_perfil_datos').on('submit', async function (e) {
        e.preventDefault();
        await guardarDatos();
    });

    $('#btn_cancelar_datos').on('click', function () {
        cargarDatosEnFormulario();
    });

    // Formulario de contraseña
    $('#form_perfil_password').on('submit', async function (e) {
        e.preventDefault();
        await cambiarPassword();
    });

    $('#btn_cancelar_password').on('click', function () {
        $('#form_perfil_password')[0].reset();
        $('#form_perfil_password').removeClass('was-validated');
    });

    // Validación de confirmación de contraseña
    $('#perfil_password_nueva_confirmar').on('input', function () {
        const nueva = $('#perfil_password_nueva').val();
        const confirmar = $(this).val();
        
        if (confirmar && nueva !== confirmar) {
            $(this).addClass('is-invalid');
            $(this).next('.invalid-feedback').text('Las contraseñas no coinciden');
        } else {
            $(this).removeClass('is-invalid');
        }
    });
}

/**
 * Cargar perfil del usuario
 */
async function cargarPerfil() {
    try {
        Swal.fire({
            title: 'Cargando...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const response = await API.get('perfil/getPerfil');
        
        Swal.close();

        if (response.ok && response.data) {
            perfilData = response.data;
            cargarDatosEnFormulario();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.msj || 'Error al cargar el perfil'
            });
        }
    } catch (error) {
        Swal.close();
        console.error('Error al cargar perfil:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al cargar el perfil'
        });
    }
}

/**
 * Cargar datos en el formulario
 */
function cargarDatosEnFormulario() {
    if (!perfilData) return;

    $('#perfil_username').val(perfilData.username || '');
    $('#perfil_email').val(perfilData.email || '');
    $('#perfil_primer_nombre').val(perfilData.primer_nombre || '');
    $('#perfil_apellido_paterno').val(perfilData.apellido_paterno || '');
    $('#perfil_apellido_materno').val(perfilData.apellido_materno || '');
    $('#perfil_telefono').val(perfilData.telefono || '');
    $('#perfil_rol').val(perfilData.rol_nombre || '-');
    $('#perfil_sede').val(perfilData.sede_nombre || '-');
    
    if (perfilData.ultimo_acceso) {
        const fecha = new Date(perfilData.ultimo_acceso);
        $('#perfil_ultimo_acceso').val(Utils.formatearFecha(fecha.toISOString().split('T')[0]) + ' ' + fecha.toLocaleTimeString('es-PE'));
    } else {
        $('#perfil_ultimo_acceso').val('Nunca');
    }
}

/**
 * Guardar datos del perfil
 */
async function guardarDatos() {
    const form = $('#form_perfil_datos')[0];
    
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    try {
        Swal.fire({
            title: 'Guardando...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const data = {
            email: $('#perfil_email').val().trim(),
            primer_nombre: $('#perfil_primer_nombre').val().trim(),
            apellido_paterno: $('#perfil_apellido_paterno').val().trim(),
            apellido_materno: $('#perfil_apellido_materno').val().trim(),
            telefono: $('#perfil_telefono').val().trim()
        };

        const response = await apiPutOrPost('perfil/updateDatos', data);
        
        Swal.close();

        if (response.ok) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: response.msj || 'Datos actualizados correctamente',
                timer: 2000,
                showConfirmButton: false
            });

            // Recargar perfil para obtener datos actualizados
            await cargarPerfil();
            
            // Actualizar información del usuario en Auth si es necesario
            if (typeof Auth !== 'undefined' && Auth.updateUserInfo) {
                Auth.updateUserInfo();
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.msj || 'Error al actualizar los datos'
            });
        }
    } catch (error) {
        Swal.close();
        console.error('Error al guardar datos:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al guardar los datos'
        });
    }
}

/**
 * Cambiar contraseña
 */
async function cambiarPassword() {
    const form = $('#form_perfil_password')[0];
    
    // Validar que las contraseñas coincidan
    const nueva = $('#perfil_password_nueva').val();
    const confirmar = $('#perfil_password_nueva_confirmar').val();
    
    if (nueva !== confirmar) {
        $('#perfil_password_nueva_confirmar').addClass('is-invalid');
        $('#perfil_password_nueva_confirmar').next('.invalid-feedback').text('Las contraseñas no coinciden');
        form.classList.add('was-validated');
        return;
    }

    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }

    // Validar longitud mínima
    if (nueva.length < 6) {
        $('#perfil_password_nueva').addClass('is-invalid');
        $('#perfil_password_nueva').next('.invalid-feedback').text('La contraseña debe tener al menos 6 caracteres');
        form.classList.add('was-validated');
        return;
    }

    try {
        Swal.fire({
            title: 'Cambiando contraseña...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const data = {
            password_actual: $('#perfil_password_actual').val(),
            password_nueva: nueva,
            password_nueva_confirmar: confirmar
        };

        const response = await apiPutOrPost('perfil/cambiarPassword', data);
        
        Swal.close();

        if (response.ok) {
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: response.msj || 'Contraseña cambiada correctamente',
                timer: 2000,
                showConfirmButton: false
            });

            // Limpiar formulario
            $('#form_perfil_password')[0].reset();
            $('#form_perfil_password').removeClass('was-validated');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.msj || 'Error al cambiar la contraseña'
            });
        }
    } catch (error) {
        Swal.close();
        console.error('Error al cambiar contraseña:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error de conexión al cambiar la contraseña'
        });
    }
}
