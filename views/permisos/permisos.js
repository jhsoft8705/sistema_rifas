/**
 * permisos.js - Gestión de Permisos
 */

let tablaPermisos;

$(document).ready(function() {
    // Verificar autenticación
    if (!Auth.isAuthenticated()) {
        window.location.href = window.BASE_URL + '/admin-login';
        return;
    }

    // Verificar si la tabla ya está inicializada
    if ($.fn.DataTable.isDataTable('#tabla_permisos')) {
        tablaPermisos = $('#tabla_permisos').DataTable();
        tablaPermisos.destroy();
    }

    // Inicializar tabla (ya carga los datos automáticamente)
    inicializarTabla();

    // Eventos
    $('#btn_nuevo_permiso').on('click', function() {
        abrirModalNuevo();
    });

    $('#btn_guardar_permiso').on('click', function() {
        guardarPermiso();
    });
});

function inicializarTabla() {
    // Destruir tabla si ya existe
    if ($.fn.DataTable.isDataTable('#tabla_permisos')) {
        $('#tabla_permisos').DataTable().destroy();
    }

    tablaPermisos = $('#tabla_permisos').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        processing: true,
        serverSide: false,
        destroy: true, // Permite reinicializar la tabla
        ajax: async function(data, callback, settings) {
            try {
                const response = await API.get('permisos/getAll');
                console.log('Respuesta de permisos:', response);
                if (response && response.ok) {
                    // Eliminar duplicados por ID antes de mostrar
                    const datosUnicos = [];
                    const idsVistos = {};
                    
                    (response.data || []).forEach(function(row) {
                        if (!idsVistos[row.id]) {
                            idsVistos[row.id] = true;
                            datosUnicos.push(row);
                        }
                    });
                    
                    console.log(`Permisos únicos: ${datosUnicos.length} de ${response.data?.length || 0}`);
                    callback({
                        data: datosUnicos
                    });
                } else {
                    console.error('Error al cargar permisos:', response?.msj);
                    Swal.fire('Error', response?.msj || 'Error al cargar los permisos', 'error');
                    callback({
                        data: []
                    });
                }
            } catch (error) {
                console.error('Error en AJAX:', error);
                Swal.fire('Error', 'Error al cargar los permisos desde el servidor', 'error');
                callback({
                    data: []
                });
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-sm btn-info" onclick="editarPermiso(${row.id})" title="Editar">
                            <i class="ri-edit-line"></i>
                        </button>
                    `;
                }
            },
            { data: 'nombre' },
            { data: 'descripcion', defaultContent: '-' },
            { data: 'modulo' },
            { data: 'accion' },
            {
                data: 'estado',
                render: function(data) {
                    return data == 1
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                }
            }
        ],
        order: [[3, 'asc'], [4, 'asc']]
    });
}

function cargarPermisos() {
    if (tablaPermisos && $.fn.DataTable.isDataTable('#tabla_permisos')) {
        tablaPermisos.ajax.reload();
    }
}

function abrirModalNuevo() {
    $('#form_permiso')[0].reset();
    $('#permiso_id').val('');
    $('#modal_permiso_label').text('Nuevo Permiso');
    $('#modal_permiso').modal('show');
}

function editarPermiso(id) {
    API.get(`permisos/getById?id=${id}`)
        .then(response => {
            if (response.ok && response.data) {
                const permiso = response.data;
                $('#permiso_id').val(permiso.id);
                $('#permiso_nombre').val(permiso.nombre);
                $('#permiso_descripcion').val(permiso.descripcion || '');
                $('#permiso_modulo').val(permiso.modulo);
                $('#permiso_accion').val(permiso.accion);
                $('#permiso_estado').val(permiso.estado);
                $('#modal_permiso_label').text('Editar Permiso');
                $('#modal_permiso').modal('show');
            } else {
                Swal.fire('Error', response.msj || 'No se pudo cargar el permiso', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al cargar el permiso', 'error');
        });
}

function guardarPermiso() {
    const formData = {
        id: $('#permiso_id').val() || null,
        nombre: $('#permiso_nombre').val(),
        descripcion: $('#permiso_descripcion').val(),
        modulo: $('#permiso_modulo').val(),
        accion: $('#permiso_accion').val(),
        estado: $('#permiso_estado').val()
    };

    if (!formData.nombre.trim() || !formData.modulo.trim() || !formData.accion.trim()) {
        Swal.fire('Error', 'Todos los campos obligatorios deben ser completados', 'error');
        return;
    }

    const endpoint = formData.id ? 'permisos/update' : 'permisos/register';

    API.post(endpoint, formData)
        .then(response => {
            if (response.ok) {
                Swal.fire('Éxito', response.msj || 'Permiso guardado correctamente', 'success');
                $('#modal_permiso').modal('hide');
                cargarPermisos();
            } else {
                Swal.fire('Error', response.msj || 'Error al guardar el permiso', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al guardar el permiso', 'error');
        });
}
