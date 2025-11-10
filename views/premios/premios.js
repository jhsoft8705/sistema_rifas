/**
 * Gestión de Premios
 */

let tablaPremios;
let premiosData = [];
let categoriasPremioData = [];
let userInfo = null;
let modalPremio;
let simboloMoneda = 'S/.';
let codigoMoneda = 'PEN';
let fechaInicioFiltro = '';
let fechaFinFiltro = '';
let filtroFechaPicker = null;

$(document).ready(async function () {
    if (!Auth.requireAuth()) {
        return;
    }

    userInfo = Auth.getUserInfo();
    modalPremio = new bootstrap.Modal(document.getElementById('modal_premio'));

    simboloMoneda = userInfo?.simbolo_moneda || 'S/.';
    codigoMoneda = userInfo?.codigo_moneda || 'PEN';
    $('#valor_estimado_simbolo').text(simboloMoneda);
    $('#valor_estimado_simbolo_prefix').text(simboloMoneda);

    await inicializarSelects();
    inicializarTabla();
    inicializarEventos();

    cargarPremios();
});

async function inicializarSelects() {
    if (!userInfo) {
        return;
    }

    $('#sede_id').val(userInfo.sede_id);
    inicializarRangoFechas();

    await cargarCategoriasSelect();
}

function inicializarTabla() {
    tablaPremios = $('#tabla_premios').DataTable({
        data: [],
        language: {
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
        },
        columns: [
            {
                data: null,
                className: 'text-center',
                orderable: false,
                width: '80px',
                render: (_, __, row) => `
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary btn-editar" data-id="${row.id}" title="Editar">
                            <i class="ri-edit-2-line"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${row.id}" title="Eliminar">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                    </div>
                `
            },
            { data: 'codigo' },
            { data: 'nombre' },
            {
                data: 'categoria_nombre',
                render: (data) => data || '-'
            },
            {
                data: 'valor_estimado',
                render: (data) => formatearMonedaLocal(data)
            },
            {
                data: 'estado',
                render: (estado) => {
                    const map = {
                        0: { text: 'Inactivo', class: 'badge-soft-secondary' },
                        1: { text: 'Activo', class: 'badge-soft-success' },
                        2: { text: 'Agotado', class: 'badge-soft-warning' }
                    };
                    const info = map[estado] || map[0];
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

function inicializarEventos() {
    $('#btn_nuevo_premio').on('click', () => {
        abrirModalPremio();
    });

    $('#btn_filtrar_premios').on('click', () => filtrarPremios());
    $('#btn_recargar_premios').on('click', () => {
        reiniciarFiltrosPremios();
        cargarPremios();
    });

    $('#imagen_principal').on('change', function () {
        actualizarPreviewArchivoUnico(this, '#imagen_principal_preview', 'Sin imagen seleccionada');
    });

    $('#imagen_secundaria').on('change', function () {
        actualizarPreviewArchivoUnico(this, '#imagen_secundaria_preview', 'Sin imagen seleccionada');
    });

    $('#galeria_imagenes').on('change', function () {
        actualizarPreviewGaleriaNueva(this, '#galeria_nuevas_preview', 'Sin archivos seleccionados');
    });

    $('#tabla_premios tbody').on('click', '.btn-editar', async function () {
        const id = $(this).data('id');
        await editarPremio(id);
    });

    $('#tabla_premios tbody').on('click', '.btn-eliminar', function () {
        const id = $(this).data('id');
        eliminarPremio(id);
    });

    $('#form_premio').on('submit', async function (event) {
        event.preventDefault();
        await guardarPremio();
    });

    $('#color_picker').on('input', function () {
        $('#color').val($(this).val());
    });

    $('#color').on('input', function () {
        const value = $(this).val();
        if (esHexColor(value)) {
            $('#color_picker').val(value);
        }
    });

    $('#codigo').on('blur', () => Utils.validarCampo('codigo', 'El código del premio es obligatorio'));
    $('#nombre').on('blur', () => Utils.validarCampo('nombre', 'El nombre del premio es obligatorio'));

    $('#form_premio').on('input change', 'input, select, textarea', function () {
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

async function cargarPremios() {
    if (!userInfo) return;

    try {
        Utils.showLoading('Cargando premios...');
        const estado = $('#filtro_estado').val();

        const params = { sede_id: userInfo.sede_id };
        if (estado !== '') {
            params.estado = estado;
        }
        if (fechaInicioFiltro) {
            params.fecha_inicio = fechaInicioFiltro;
        }
        if (fechaFinFiltro) {
            params.fecha_fin = fechaFinFiltro;
        }

        const respuesta = await API.get('premios/getAll', params);
        Utils.closeLoading();

        if (respuesta && respuesta.ok) {
            premiosData = respuesta.data || [];
            tablaPremios.clear().rows.add(premiosData).draw();
        } else {
            premiosData = [];
            tablaPremios.clear().draw();
            Utils.showToast(respuesta?.msj || 'No se pudo obtener premios', 'warning');
        }
    } catch (error) {
        console.error('Error al cargar premios:', error);
        Utils.closeLoading();
        Utils.showToast('Ocurrió un problema al cargar los premios', 'error');
    }
}

function abrirModalPremio(premio = null) {
    limpiarFormularioPremio();

    $('#sede_id').val(userInfo?.sede_id || '');
    $('#valor_estimado_simbolo').text(simboloMoneda);
    $('#valor_estimado_simbolo_prefix').text(simboloMoneda);

    if (premio) {
        $('#modal_premio_title').text('Editar Premio');
        $('#premio_id').val(premio.id);
        $('#categoria_id').val(premio.categoria_id || '');
        $('#codigo').val(premio.codigo || '');
        $('#nombre').val(premio.nombre || '');
        $('#descripcion').val(premio.descripcion || '');
        $('#valor_estimado').val(premio.valor_estimado || '');
        $('#marca').val(premio.marca || '');
        $('#modelo').val(premio.modelo || '');
        $('#color').val(premio.color || '');
        $('#video_url').val(premio.video_url || '');
        $('#especificaciones').val(premio.especificaciones || '');
        $('#terminos_condiciones').val(premio.terminos_condiciones || '');
        $('#restricciones').val(premio.restricciones || '');
        $('#es_destacado').val(premio.es_destacado || 0);
        $('#orden_visualizacion').val(premio.orden_visualizacion || 0);
        $('#estado').val(premio.estado ?? 1);

        $('#imagen_principal_actual').val(premio.imagen_principal || '');
        actualizarTextoImagen('#imagen_principal_actual_text', premio.imagen_principal);
        renderPreviewPersistente('#imagen_principal_preview', premio.imagen_principal, 'Sin imagen seleccionada');

        $('#imagen_secundaria_actual').val(premio.imagen_secundaria || '');
        actualizarTextoImagen('#imagen_secundaria_actual_text', premio.imagen_secundaria);
        renderPreviewPersistente('#imagen_secundaria_preview', premio.imagen_secundaria, 'Sin imagen seleccionada');

        const galeria = parseGaleria(premio.galeria_imagenes);
        actualizarListaGaleria(galeria);

        const colorHex = premio.color || '';
        if (esHexColor(colorHex)) {
            $('#color_picker').val(colorHex);
        } else {
            $('#color_picker').val('#ffffff');
        }
    } else {
        $('#modal_premio_title').text('Nuevo Premio');
        $('#estado').val(1);
        $('#es_destacado').val(0);
        $('#orden_visualizacion').val(0);
        actualizarListaGaleria([]);
        renderPreviewPersistente('#imagen_principal_preview', null, 'Sin imagen seleccionada');
        renderPreviewPersistente('#imagen_secundaria_preview', null, 'Sin imagen seleccionada');
        actualizarTextoImagen('#imagen_principal_actual_text', null);
        actualizarTextoImagen('#imagen_secundaria_actual_text', null);
        $('#color_picker').val('#ffffff');
    }

    $('#imagen_principal').val('');
    $('#imagen_secundaria').val('');
    $('#galeria_imagenes').val('');
    setPreviewTexto('#galeria_nuevas_preview', 'Sin archivos seleccionados');

    modalPremio.show();
}

async function editarPremio(id) {
    try {
        Utils.showLoading('Cargando premio...');
        const respuesta = await API.get('premios/getById', {
            id: id,
            sede_id: userInfo?.sede_id
        });
        Utils.closeLoading();

        if (respuesta && respuesta.ok && respuesta.data) {
            abrirModalPremio(respuesta.data);
        } else {
            Utils.showToast(respuesta?.msj || 'No se pudo obtener el premio', 'error');
        }
    } catch (error) {
        console.error('Error al obtener premio:', error);
        Utils.closeLoading();
        Utils.showToast('Ocurrió un problema al obtener el premio', 'error');
    }
}

async function guardarPremio() {
    if (!validarFormularioPremio()) {
        return;
    }

    const form = document.getElementById('form_premio');
    const premioId = $('#premio_id').val();
    const esEdicion = premioId !== '';

    const formData = new FormData(form);
    formData.set('sede_id', userInfo?.sede_id || '');
    formData.set('categoria_id', $('#categoria_id').val() || '');
    formData.set('codigo', $('#codigo').val().trim());
    formData.set('nombre', $('#nombre').val().trim());
    formData.set('descripcion', $('#descripcion').val()?.trim() || '');
    formData.set('valor_estimado', $('#valor_estimado').val() || '');
    formData.set('marca', $('#marca').val()?.trim() || '');
    formData.set('modelo', $('#modelo').val()?.trim() || '');
    formData.set('color', $('#color').val()?.trim() || '');
    formData.set('video_url', $('#video_url').val()?.trim() || '');
    formData.set('especificaciones', $('#especificaciones').val()?.trim() || '');
    formData.set('terminos_condiciones', $('#terminos_condiciones').val()?.trim() || '');
    formData.set('restricciones', $('#restricciones').val()?.trim() || '');
    formData.set('es_destacado', $('#es_destacado').val() || '0');
    formData.set('orden_visualizacion', $('#orden_visualizacion').val() || '0');
    formData.set('galeria_imagenes_actual', $('#galeria_imagenes_actual').val() || '[]');
    formData.set('imagen_principal_actual', $('#imagen_principal_actual').val() || '');
    formData.set('imagen_secundaria_actual', $('#imagen_secundaria_actual').val() || '');

    if (esEdicion) {
        formData.set('id', premioId);
        formData.set('estado', $('#estado').val());
        formData.set('modificado_por', userInfo?.nombre_completo || 'SYSTEM');
    } else {
        formData.set('creado_por', userInfo?.nombre_completo || 'SYSTEM');
    }

    try {
        Utils.showLoading(esEdicion ? 'Actualizando premio...' : 'Registrando premio...');
        const endpoint = esEdicion ? 'premios/update' : 'premios/register';
        const respuesta = await API.postFormData(endpoint, formData);
        Utils.closeLoading();

        if (respuesta && respuesta.ok) {
            Utils.showToast(respuesta.msj, 'success');
            modalPremio.hide();
            await cargarPremios();
        } else {
            Utils.showToast(respuesta?.msj || 'No se pudo guardar el premio', 'error');
        }
    } catch (error) {
        console.error('Error al guardar premio:', error);
        Utils.closeLoading();
        Utils.showToast('Ocurrió un problema al guardar el premio', 'error');
    }
}

function limpiarFormularioPremio() {
    const form = document.getElementById('form_premio');
    form.reset();
    Utils.limpiarValidaciones('form_premio');
    $('#premio_id').val('');
    $('#sede_id').val(userInfo?.sede_id || '');
    $('#categoria_id').val('');
    $('#imagen_principal_actual').val('');
    actualizarTextoImagen('#imagen_principal_actual_text', null);
    renderPreviewPersistente('#imagen_principal_preview', null, 'Sin imagen seleccionada');

    $('#imagen_secundaria_actual').val('');
    actualizarTextoImagen('#imagen_secundaria_actual_text', null);
    renderPreviewPersistente('#imagen_secundaria_preview', null, 'Sin imagen seleccionada');

    actualizarListaGaleria([]);
    $('#galeria_imagenes').val('');
    setPreviewTexto('#galeria_nuevas_preview', 'Sin archivos seleccionados');
    $('#color_picker').val('#ffffff');
}

async function cargarCategoriasSelect() {
    if (!userInfo) return;

    const $select = $('#categoria_id');
    if (!$select.length) return;

    const valorSeleccionado = $select.val();

    $select.prop('disabled', true);
    $select.html('<option value="">Cargando categorías...</option>');

    try {
        const respuesta = await API.get('categorias/getAll', {
            sede_id: userInfo.sede_id
        });

        if (respuesta && respuesta.ok) {
            categoriasPremioData = respuesta.data || [];
            if (categoriasPremioData.length) {
                const opciones = ['<option value="">Sin categoría</option>'].concat(
                    categoriasPremioData.map(cat => `<option value="${cat.id}">${cat.nombre}</option>`)
                );
                $select.html(opciones.join(''));
            } else {
                categoriasPremioData = [];
                $select.html('<option value="">Sin categorías registradas</option>');
            }
        } else {
            categoriasPremioData = [];
            $select.html('<option value="">No se pudieron cargar categorías</option>');
            if (respuesta?.msj) {
                Utils.showToast(respuesta.msj, 'warning');
            }
        }
    } catch (error) {
        console.error('Error al cargar categorías:', error);
        categoriasPremioData = [];
        $select.html('<option value="">Error al cargar categorías</option>');
        Utils.showToast('Ocurrió un problema al cargar las categorías', 'error');
    } finally {
        if (valorSeleccionado) {
            $select.val(valorSeleccionado);
        }
        $select.prop('disabled', false);
    }
}

function inicializarRangoFechas(reset = false) {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const primerDiaMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);

    const formatear = (fecha) => {
        const iso = fecha.toISOString();
        return iso.split('T')[0];
    };

    fechaInicioFiltro = formatear(primerDiaMes);
    fechaFinFiltro = formatear(hoy);

    if (!reset) {
        if (typeof flatpickr !== 'undefined') {
            filtroFechaPicker = flatpickr("#filtro_fecha", {
                mode: "range",
                dateFormat: "Y-m-d",
                locale: "es",
                defaultDate: [fechaInicioFiltro, fechaFinFiltro]
            });
        } else {
            $('#filtro_fecha').val(`${fechaInicioFiltro} to ${fechaFinFiltro}`);
        }
    } else {
        if (filtroFechaPicker) {
            filtroFechaPicker.setDate([fechaInicioFiltro, fechaFinFiltro], true);
        } else {
            $('#filtro_fecha').val(`${fechaInicioFiltro} to ${fechaFinFiltro}`);
        }
    }
}

function filtrarPremios() {
    if (!actualizarFechasDesdeInput()) {
        Utils.showToast('Por favor selecciona un rango de fechas', 'warning');
        return;
    }

    cargarPremios();
}

function actualizarFechasDesdeInput() {
    const valor = $('#filtro_fecha').val();
    if (!valor || valor.trim() === '') {
        fechaInicioFiltro = '';
        fechaFinFiltro = '';
        return false;
    }

    const partes = valor.split(' to ');
    if (partes.length === 2) {
        fechaInicioFiltro = partes[0].trim();
        fechaFinFiltro = partes[1].trim();
    } else {
        const fecha = valor.trim();
        fechaInicioFiltro = fecha;
        fechaFinFiltro = fecha;
    }

    return true;
}

function reiniciarFiltrosPremios() {
    $('#filtro_estado').val('');
    inicializarRangoFechas(true);
    actualizarFechasDesdeInput();
}

function validarFormularioPremio() {
    let esValido = true;
    Utils.limpiarValidaciones('form_premio');

    const camposObligatorios = [
        { id: 'codigo', mensaje: 'El código del premio es obligatorio' },
        { id: 'nombre', mensaje: 'El nombre del premio es obligatorio' }
    ];

    camposObligatorios.forEach((campo) => {
        if (!Utils.validarCampo(campo.id, campo.mensaje)) {
            esValido = false;
        }
    });

    return esValido;
}

function parseIntOrNull(value) {
    if (value === null || value === undefined || value === '') {
        return null;
    }
    const parsed = parseInt(value, 10);
    return isNaN(parsed) ? null : parsed;
}

function parseFloatOrNull(value) {
    if (value === null || value === undefined || value === '') {
        return null;
    }
    const parsed = parseFloat(value);
    return isNaN(parsed) ? null : parsed;
}

function esHexColor(value) {
    if (!value) return false;
    return /^#([0-9A-Fa-f]{3}|[0-9A-Fa-f]{6})$/.test(value.trim());
}

function parseGaleria(raw) {
    if (!raw) return [];
    if (Array.isArray(raw)) return raw;
    try {
        const parsed = JSON.parse(raw);
        return Array.isArray(parsed) ? parsed : [];
    } catch (error) {
        return [];
    }
}

function actualizarTextoImagen(selector, path) {
    const texto = path ? `Actual: ${path}` : 'Sin imagen cargada';
    $(selector).text(texto);
}

function actualizarListaGaleria(lista) {
    if (!Array.isArray(lista)) {
        lista = [];
    }

    $('#galeria_imagenes_actual').val(JSON.stringify(lista));

    const $list = $('#galeria_actual_list');

    if (!lista.length) {
        $list.html('<li class="text-muted">Sin imágenes cargadas</li>');
        return;
    }

    const items = lista.map((item, index) => `
        <li class="d-flex justify-content-between align-items-center gap-2">
            <div class="d-flex align-items-center gap-2">
                ${construirUrlImagen(item) ? `<img src="${construirUrlImagen(item)}" alt="Imagen ${index + 1}" class="rounded border" style="width:48px;height:48px;object-fit:cover;">` : ''}
                <span>${index + 1}. ${item}</span>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-galeria" data-index="${index}">
                <i class="ri-close-line"></i>
            </button>
        </li>
    `).join('');

    $list.html(items);

    $list.find('.btn-remove-galeria').on('click', function () {
        const idx = parseInt($(this).data('index'), 10);
        if (Number.isNaN(idx)) return;
        const current = parseGaleria($('#galeria_imagenes_actual').val());
        current.splice(idx, 1);
        actualizarListaGaleria(current);
    });
}

function formatearMonedaLocal(valor) {
    if (valor === null || valor === undefined || valor === '') {
        return `${simboloMoneda} 0.00`;
    }
    const formatter = new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: codigoMoneda || 'PEN',
        minimumFractionDigits: 2
    });
    let resultado = formatter.format(valor);
    if (simboloMoneda && !resultado.startsWith(simboloMoneda)) {
        resultado = resultado.replace(/^[^\d-]+/, simboloMoneda + ' ');
    }
    return resultado;
}

function construirUrlImagen(path) {
    if (!path) return null;
    if (/^https?:\/\//i.test(path)) {
        return path;
    }
    const base = (window.BASE_URL || (window.Utils?.getBaseUrl?.() ?? '')).replace(/\/$/, '');
    return base ? `${base}/${path.replace(/^\/+/, '')}` : path;
}

function renderPreviewPersistente(selector, path, textoDefault) {
    if (path) {
        const src = construirUrlImagen(path);
        if (src) {
            $(selector).html(`<img src="${src}" alt="Imagen" class="rounded border" style="width:120px;height:120px;object-fit:cover;">`);
            return;
        }
    }
    setPreviewTexto(selector, textoDefault);
}

function actualizarPreviewArchivoUnico(input, selector, textoDefault) {
    const files = input.files;
    if (!files || files.length === 0) {
        setPreviewTexto(selector, textoDefault);
        return;
    }

    const file = files[0];
    const reader = new FileReader();
    reader.onload = function (e) {
        $(selector).html(`<img src="${e.target.result}" alt="${file.name}" class="rounded border" style="width:120px;height:120px;object-fit:cover;">`);
    };
    reader.readAsDataURL(file);
}

function actualizarPreviewGaleriaNueva(input, selector, textoDefault) {
    const files = input.files;
    if (!files || !files.length) {
        setPreviewTexto(selector, textoDefault);
        return;
    }

    const $container = $(selector);
    $container.empty();

    Array.from(files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function (e) {
            const item = $(`
                <div class="d-inline-flex flex-column align-items-center me-2 mb-2">
                    <img src="${e.target.result}" alt="${file.name}" class="rounded border" style="width:80px;height:80px;object-fit:cover;">
                    <small class="text-muted">${index + 1}</small>
                </div>
            `);
            $container.append(item);
        };
        reader.readAsDataURL(file);
    });
}

function setPreviewTexto(selector, texto) {
    $(selector).html(`<span class="text-muted small">${texto}</span>`);
}

function eliminarPremio(id) {
    Swal.fire({
        title: '¿Eliminar premio?',
        text: 'Esta acción desactivará el premio seleccionado.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                Utils.showLoading('Eliminando premio...');
                const respuesta = await API.post('premios/delete', {
                    id: id,
                    sede_id: userInfo?.sede_id,
                    modificado_por: userInfo?.nombre_completo || 'SYSTEM'
                });
                Utils.closeLoading();

                if (respuesta && respuesta.ok) {
                    Utils.showToast(respuesta.msj, 'success');
                    await cargarPremios();
                } else {
                    Utils.showToast(respuesta?.msj || 'No se pudo eliminar el premio', 'error');
                }
            } catch (error) {
                console.error('Error al eliminar premio:', error);
                Utils.closeLoading();
                Utils.showToast('Ocurrió un problema al eliminar el premio', 'error');
            }
        }
    });
}


