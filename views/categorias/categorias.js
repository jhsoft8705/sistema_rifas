/**
 * Gestión de Categorías de Premios
 */

let tablaCategorias;
let categoriasData = [];
let userInfo = null;
let modalCategoria;

$(document).ready(async function () {
    if (!Auth.requireAuth()) {
        return;
    }

    userInfo = Auth.getUserInfo();
    modalCategoria = new bootstrap.Modal(document.getElementById('modal_categoria'));

    await inicializarSelectsCategorias();
    inicializarTablaCategorias();
    inicializarEventosCategorias();

    cargarCategoriasPremios();
});

async function inicializarSelectsCategorias() {
    if (!userInfo) {
        return;
    }

    const sedeOption = `<option value="${userInfo.sede_id}">${userInfo.sede_nombre || 'Sede Principal'}</option>`;

    $('#filtro_sede').html(sedeOption).val(userInfo.sede_id);
    $('#sede_id_categoria').val(userInfo.sede_id);
}

function inicializarTablaCategorias() {
    tablaCategorias = $('#tabla_categorias').DataTable({
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
                width: '80px',
                render: (_, __, row) => `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary btn-editar-categoria" data-id="${row.id}" title="Editar">
                            <i class="ri-edit-2-line"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar-categoria" data-id="${row.id}" title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `
            },
            { data: 'nombre' },
            {
                data: 'descripcion',
                render: (data) => data || '-'
            },
            {
                data: 'icono',
                render: (data) => data || '-'
            },
            {
                data: 'color_hex',
                render: (color) => {
                    if (!color) return '-';
                    return `
                        <span class="badge rounded-pill" style="background-color: ${color};">
                            ${color}
                        </span>
                    `;
                }
            },
            {
                data: 'orden',
                render: (data) => data ?? 0
            },
            {
                data: 'estado',
                render: (estado) => {
                    const map = {
                        0: { text: 'Inactiva', class: 'badge-soft-secondary' },
                        1: { text: 'Activa', class: 'badge-soft-success' }
                    };
                    const info = map[estado] || map[0];
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

function inicializarEventosCategorias() {
    $('#btn_nueva_categoria').on('click', () => {
        abrirModalCategoria();
    });

    $('#btn_filtrar_categorias').on('click', () => cargarCategoriasPremios());
    $('#btn_recargar_categorias').on('click', () => {
        $('#filtro_estado').val('');
        cargarCategoriasPremios();
    });

    $('#tabla_categorias tbody').on('click', '.btn-editar-categoria', async function () {
        const id = $(this).data('id');
        await editarCategoria(id);
    });

    $('#tabla_categorias tbody').on('click', '.btn-eliminar-categoria', function () {
        const id = $(this).data('id');
        eliminarCategoria(id);
    });

    $('#form_categoria').on('submit', async function (event) {
        event.preventDefault();
        await guardarCategoria();
    });

    $('#color_categoria_picker').on('input', function () {
        $('#color_categoria').val($(this).val());
    });

    $('#color_categoria').on('input', function () {
        const value = $(this).val();
        if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
            $('#color_categoria_picker').val(value);
        }
    });

    $('#nombre_categoria').on('blur', () => Utils.validarCampo('nombre_categoria', 'El nombre de la categoría es obligatorio'));

    $('#form_categoria').on('input change', 'input, select, textarea', function () {
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

async function cargarCategoriasPremios() {
    if (!userInfo) return;

    try {
        Utils.showLoading('Cargando categorías...');

        const sedeId = $('#filtro_sede').val() || userInfo.sede_id;
        const estado = $('#filtro_estado').val();

        const params = { sede_id: sedeId };
        if (estado !== '') {
            params.estado = estado;
        }

        const respuesta = await API.get('categorias/getAll', params);

        Utils.closeLoading();

        if (respuesta && respuesta.ok) {
            categoriasData = respuesta.data || [];
            tablaCategorias.clear().rows.add(categoriasData).draw();
        } else {
            categoriasData = [];
            tablaCategorias.clear().draw();
            Utils.showToast(respuesta?.msj || 'No se pudo obtener categorías', 'warning');
        }
    } catch (error) {
        Utils.closeLoading();
        console.error('Error al cargar categorías:', error);
        categoriasData = [];
        tablaCategorias.clear().draw();
        Utils.showToast('Error de conexión al cargar las categorías', 'error');
    }
}

function abrirModalCategoria(categoria = null) {
    limpiarFormularioCategoria();

    $('#sede_id_categoria').val(userInfo?.sede_id || '');

    if (categoria) {
        $('#modal_categoria_title').text('Editar Categoría');
        $('#categoria_id').val(categoria.id);
        $('#nombre_categoria').val(categoria.nombre || '');
        $('#descripcion_categoria').val(categoria.descripcion || '');
        $('#icono_categoria').val(categoria.icono || '');
        $('#color_categoria').val(categoria.color_hex || '');
        $('#orden_categoria').val(categoria.orden ?? 0);
        $('#estado_categoria').val(categoria.estado ?? 1);

        if (categoria.color_hex && /^#[0-9A-Fa-f]{6}$/.test(categoria.color_hex)) {
            $('#color_categoria_picker').val(categoria.color_hex);
        } else {
            $('#color_categoria_picker').val('#ffffff');
        }
    } else {
        $('#modal_categoria_title').text('Nueva Categoría');
        $('#estado_categoria').val(1);
        $('#orden_categoria').val(0);
        $('#color_categoria_picker').val('#ffffff');
    }

    modalCategoria.show();
}

async function editarCategoria(id) {
    try {
        Utils.showLoading('Cargando categoría...');
        const respuesta = await API.get('categorias/getById', {
            id: id,
            sede_id: userInfo?.sede_id
        });
        Utils.closeLoading();

        if (respuesta && respuesta.ok && respuesta.data) {
            abrirModalCategoria(respuesta.data);
        } else {
            Utils.showToast(respuesta?.msj || 'No se pudo obtener la categoría', 'error');
        }
    } catch (error) {
        console.error('Error al obtener categoría:', error);
        Utils.closeLoading();
        Utils.showToast('Ocurrió un problema al obtener la categoría', 'error');
    }
}

async function guardarCategoria() {
    if (!validarFormularioCategoria()) {
        return;
    }

    const categoriaId = $('#categoria_id').val();
    const esEdicion = categoriaId !== '';

    const payload = {
        sede_id: userInfo?.sede_id || '',
        nombre: $('#nombre_categoria').val().trim(),
        descripcion: $('#descripcion_categoria').val()?.trim() || '',
        icono: $('#icono_categoria').val()?.trim() || '',
        color_hex: $('#color_categoria').val()?.trim() || '',
        orden: $('#orden_categoria').val() || '',
    };

    if (esEdicion) {
        payload.id = parseInt(categoriaId, 10);
        payload.estado = $('#estado_categoria').val();
        payload.modificado_por = userInfo?.nombre_completo || 'SYSTEM';
    } else {
        payload.creado_por = userInfo?.nombre_completo || 'SYSTEM';
    }

    try {
        Utils.showLoading(esEdicion ? 'Actualizando categoría...' : 'Registrando categoría...');
        const endpoint = esEdicion ? 'categorias/update' : 'categorias/register';
        const respuesta = await API.post(endpoint, payload);
        Utils.closeLoading();

        if (respuesta && respuesta.ok) {
            Utils.showToast(respuesta.msj, 'success');
            modalCategoria.hide();
            await cargarCategoriasPremios();
        } else {
            Utils.showToast(respuesta?.msj || 'No se pudo guardar la categoría', 'error');
        }
    } catch (error) {
        console.error('Error al guardar categoría:', error);
        Utils.closeLoading();
        Utils.showToast('Ocurrió un problema al guardar la categoría', 'error');
    }
}

function limpiarFormularioCategoria() {
    const form = document.getElementById('form_categoria');
    form.reset();
    Utils.limpiarValidaciones('form_categoria');
    $('#categoria_id').val('');
    $('#sede_id_categoria').val(userInfo?.sede_id || '');
    $('#color_categoria_picker').val('#ffffff');
}

function validarFormularioCategoria() {
    let esValido = true;
    Utils.limpiarValidaciones('form_categoria');

    if (!Utils.validarCampo('nombre_categoria', 'El nombre de la categoría es obligatorio')) {
        esValido = false;
    }

    const orden = $('#orden_categoria').val();
    if (orden !== '' && !Number.isInteger(Number(orden))) {
        $('#orden_categoria').addClass('is-invalid');
        $('#orden_categoria_error').text('El orden debe ser un número entero');
        esValido = false;
    }

    const color = $('#color_categoria').val().trim();
    if (color && !/^#[0-9A-Fa-f]{6}$/.test(color)) {
        $('#color_categoria').addClass('is-invalid');
        $('#color_categoria_error').text('Ingrese un color en formato hexadecimal (#RRGGBB)');
        esValido = false;
    }

    return esValido;
}

function eliminarCategoria(id) {
    Swal.fire({
        title: '¿Eliminar categoría?',
        text: 'Esta acción desactivará la categoría seleccionada.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                Utils.showLoading('Eliminando categoría...');
                const respuesta = await API.post('categorias/delete', {
                    id: id,
                    sede_id: userInfo?.sede_id,
                    modificado_por: userInfo?.nombre_completo || 'SYSTEM'
                });
                Utils.closeLoading();

                if (respuesta && respuesta.ok) {
                    Utils.showToast(respuesta.msj, 'success');
                    await cargarCategoriasPremios();
                } else {
                    Utils.showToast(respuesta?.msj || 'No se pudo eliminar la categoría', 'error');
                }
            } catch (error) {
                console.error('Error al eliminar categoría:', error);
                Utils.closeLoading();
                Utils.showToast('Ocurrió un problema al eliminar la categoría', 'error');
            }
        }
    });
}


