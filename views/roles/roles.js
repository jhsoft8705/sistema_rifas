/**
 * roles.js - Gestión de Roles
 */

let tablaRoles;
let rolActualId = null;

$(document).ready(function() {
    // Verificar autenticación
    if (!Auth.isAuthenticated()) {
        window.location.href = window.BASE_URL + '/admin-login';
        return;
    }

    // Verificar si la tabla ya está inicializada
    if ($.fn.DataTable.isDataTable('#tabla_roles')) {
        tablaRoles = $('#tabla_roles').DataTable();
        tablaRoles.destroy();
    }

    // Inicializar tabla (ya carga los datos automáticamente)
    inicializarTabla();

    // Eventos
    $('#btn_nuevo_rol').on('click', function() {
        abrirModalNuevo();
    });

    $('#btn_guardar_rol').on('click', function() {
        guardarRol();
    });

    $('#btn_guardar_permisos_rol').on('click', function() {
        guardarPermisosRol();
    });
});

function inicializarTabla() {
    // Destruir tabla si ya existe
    if ($.fn.DataTable.isDataTable('#tabla_roles')) {
        $('#tabla_roles').DataTable().destroy();
    }

    tablaRoles = $('#tabla_roles').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        processing: true,
        serverSide: false,
        destroy: true, // Permite reinicializar la tabla
        ajax: async function(data, callback, settings) {
            try {
                const response = await API.get('roles/getAll');
                console.log('Respuesta de roles:', response);
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
                    
                    console.log(`Roles únicos: ${datosUnicos.length} de ${response.data?.length || 0}`);
                    callback({
                        data: datosUnicos
                    });
                } else {
                    console.error('Error al cargar roles:', response?.msj);
                    Swal.fire('Error', response?.msj || 'Error al cargar los roles', 'error');
                    callback({
                        data: []
                    });
                }
            } catch (error) {
                console.error('Error en AJAX:', error);
                Swal.fire('Error', 'Error al cargar los roles desde el servidor', 'error');
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
                        <button class="btn btn-sm btn-info me-1" onclick="editarRol(${row.id})" title="Editar">
                            <i class="ri-edit-line"></i>
                        </button>
                        <button class="btn btn-sm btn-warning me-1" onclick="asignarPermisos(${row.id})" title="Permisos">
                            <i class="ri-shield-check-line"></i>
                        </button>
                    `;
                }
            },
            { data: 'nombre' },
            { data: 'descripcion', defaultContent: '-' },
            {
                data: 'nivel_acceso',
                render: function(data) {
                    const niveles = { 1: 'Básico', 2: 'Intermedio', 3: 'Avanzado', 4: 'Admin' };
                    return niveles[data] || data;
                }
            },
            { data: 'usuarios_asignados', className: 'text-center' },
            { data: 'permisos_asignados', className: 'text-center' },
            {
                data: 'estado',
                render: function(data) {
                    return data == 1
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                }
            }
        ],
        order: [[1, 'asc']]
    });
}

function cargarRoles() {
    tablaRoles.ajax.reload();
}

function abrirModalNuevo() {
    $('#form_rol')[0].reset();
    $('#rol_id').val('');
    $('#modal_rol_label').text('Nuevo Rol');
    $('#modal_rol').modal('show');
}

function editarRol(id) {
    API.get(`roles/getById?id=${id}`)
        .then(response => {
            if (response.ok && response.data) {
                const rol = response.data;
                $('#rol_id').val(rol.id);
                $('#rol_nombre').val(rol.nombre);
                $('#rol_descripcion').val(rol.descripcion || '');
                $('#rol_nivel_acceso').val(rol.nivel_acceso);
                $('#rol_estado').val(rol.estado);
                $('#modal_rol_label').text('Editar Rol');
                $('#modal_rol').modal('show');
            } else {
                Swal.fire('Error', response.msj || 'No se pudo cargar el rol', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al cargar el rol', 'error');
        });
}

function guardarRol() {
    const formData = {
        id: $('#rol_id').val() || null,
        nombre: $('#rol_nombre').val(),
        descripcion: $('#rol_descripcion').val(),
        nivel_acceso: $('#rol_nivel_acceso').val(),
        estado: $('#rol_estado').val()
    };

    if (!formData.nombre.trim()) {
        Swal.fire('Error', 'El nombre es obligatorio', 'error');
        return;
    }

    const endpoint = formData.id ? 'roles/update' : 'roles/register';
    const method = formData.id ? 'PUT' : 'POST';

    API.post(endpoint, formData)
        .then(response => {
            if (response.ok) {
                Swal.fire('Éxito', response.msj || 'Rol guardado correctamente', 'success');
                $('#modal_rol').modal('hide');
                cargarRoles();
            } else {
                Swal.fire('Error', response.msj || 'Error al guardar el rol', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Error al guardar el rol', 'error');
        });
}

function asignarPermisos(rolId) {
    rolActualId = rolId;
    
    // Cargar permisos disponibles
    Promise.all([
        API.get('permisos/getAll'),
        API.get(`roles/getPermisos?rol_id=${rolId}`)
    ]).then(([permisosResponse, permisosRolResponse]) => {
        if (permisosResponse.ok && permisosRolResponse.ok) {
            const permisos = permisosResponse.data || [];
            const permisosRol = permisosRolResponse.data || [];
            
            // Crear mapa de permisos asignados
            const permisosAsignados = {};
            permisosRol.forEach(p => {
                if (p.asignado == 1) {
                    permisosAsignados[p.id] = true;
                }
            });

            // Agrupar por módulo
            const permisosPorModulo = {};
            permisos.forEach(permiso => {
                if (!permisosPorModulo[permiso.modulo]) {
                    permisosPorModulo[permiso.modulo] = [];
                }
                permisosPorModulo[permiso.modulo].push(permiso);
            });

            // Renderizar permisos
            let html = '';
            Object.keys(permisosPorModulo).forEach(modulo => {
                html += `
                    <div class="col-md-6 mb-4">
                        <h6 class="border-bottom pb-2">${modulo}</h6>
                        <div class="ms-3">
                `;
                permisosPorModulo[modulo].forEach(permiso => {
                    const checked = permisosAsignados[permiso.id] ? 'checked' : '';
                    html += `
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" value="${permiso.id}" 
                                id="permiso_${permiso.id}" ${checked}>
                            <label class="form-check-label" for="permiso_${permiso.id}">
                                ${permiso.nombre}
                                ${permiso.descripcion ? `<small class="text-muted d-block">${permiso.descripcion}</small>` : ''}
                            </label>
                        </div>
                    `;
                });
                html += `
                        </div>
                    </div>
                `;
            });

            $('#contenedor_permisos').html(html);
            $('#modal_permisos_rol').modal('show');
        } else {
            Swal.fire('Error', 'Error al cargar los permisos', 'error');
        }
    }).catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al cargar los permisos', 'error');
    });
}

function guardarPermisosRol() {
    const permisosSeleccionados = [];
    $('#contenedor_permisos input[type="checkbox"]:checked').each(function() {
        permisosSeleccionados.push(parseInt($(this).val()));
    });

    API.post('roles/asignarPermisos', {
        rol_id: rolActualId,
        permisos_ids: permisosSeleccionados
    }).then(response => {
        if (response.ok) {
            Swal.fire('Éxito', response.msj || 'Permisos asignados correctamente', 'success');
            $('#modal_permisos_rol').modal('hide');
            cargarRoles();
        } else {
            Swal.fire('Error', response.msj || 'Error al asignar permisos', 'error');
        }
    }).catch(error => {
        console.error('Error:', error);
        Swal.fire('Error', 'Error al asignar permisos', 'error');
    });
}
