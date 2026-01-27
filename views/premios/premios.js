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

$(document).ready(function () {
    if (!Auth.requireAuth()) {
        return;
    }

    userInfo = Auth.getUserInfo();
    modalPremio = new bootstrap.Modal(document.getElementById('modal_premio'));

    simboloMoneda = userInfo?.simbolo_moneda || 'S/.';
    codigoMoneda = userInfo?.codigo_moneda || 'PEN';
    $('#valor_estimado_simbolo').text(simboloMoneda);
    $('#valor_estimado_simbolo_prefix').text(simboloMoneda);

    $('#sede_id').val(userInfo?.sede_id || '');
    
    inicializarTabla();
    inicializarEventos();
});

function inicializarTabla() {
    tablaPremios = $('#tabla_premios').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.API_BASE_URL + '/premios/getAll',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            },
            data: function (d) {
                d.sede_id = userInfo?.sede_id || '';
                const estado = $('#filtro_estado').val();
                if (estado !== '') {
                    d.estado = estado;
                }
                return d;
            },
            dataSrc: function (json) {
                if (json && json.ok) {
                    premiosData = json.data || [];
                    return premiosData;
                } else {
                    premiosData = [];
                    return [];
                }
            },
            error: function (xhr, error, thrown) {
                console.error('Error al cargar premios:', error);
                premiosData = [];
                if (xhr.status === 401) {
                    Auth.logout();
                } else {
                    Utils.showAlert('Error de conexión al cargar los premios', 'error');
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
                        <button class="btn btn-sm btn-danger btn-eliminar btn-action-table" data-id="${row.id}" title="Inactivar">
                            <i class="ri-close-circle-line"></i>
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
    // Botón nuevo premio - abrir modal primero
    $('#btn_nuevo_premio').on('click', function () {
        abrirModalPremio();
    });

    // Cargar categorías cuando el modal esté completamente abierto (solo para nuevo registro)
    $('#modal_premio').on('shown.bs.modal', function () {
        // Solo cargar si es nuevo registro (no tiene premio_id)
        if (!$('#premio_id').val()) {
            cargarCategoriasSelect();
        }
    });

    // Botones de filtro
    $('#btn_filtrar_premios').on('click', function () {
        tablaPremios.ajax.reload();
    });

    $('#btn_recargar_premios').on('click', function () {
        $('#filtro_estado').val('');
        tablaPremios.ajax.reload();
    });

    // Eventos de imágenes
    $('#imagen_principal').on('change', function () {
        actualizarPreviewArchivoUnico(this, '#imagen_principal_preview', 'Sin imagen seleccionada');
    });

    $('#imagen_secundaria').on('change', function () {
        actualizarPreviewArchivoUnico(this, '#imagen_secundaria_preview', 'Sin imagen seleccionada');
    });

    $('#galeria_imagenes').on('change', function () {
        actualizarPreviewGaleriaNueva(this, '#galeria_nuevas_preview', 'Sin archivos seleccionados');
    });

    // Eventos de tabla
    $('#tabla_premios tbody').on('click', '.btn-editar', function () {
        const id = $(this).data('id');
        editarPremio(id);
    });

    $('#tabla_premios tbody').on('click', '.btn-eliminar', function () {
        const id = $(this).data('id');
        eliminarPremio(id);
    });

    // Submit del formulario
    $('#form_premio').on('submit', async function (event) {
        event.preventDefault();
        await guardarPremio();
    });

    // Eventos de color
    $('#color_picker').on('input', function () {
        $('#color').val($(this).val());
    });

    $('#color').on('input', function () {
        const value = $(this).val();
        if (esHexColor(value)) {
            $('#color_picker').val(value);
        }
    });

    // Validación
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

function abrirModalPremio(premio = null) {
    limpiarFormularioPremio();

    $('#sede_id').val(userInfo?.sede_id || '');
    $('#valor_estimado_simbolo').text(simboloMoneda);
    $('#valor_estimado_simbolo_prefix').text(simboloMoneda);

    if (premio) {
        $('#modal_premio_title').text('Editar Premio');
        $('#premio_id').val(premio.id);
        $('#categoria_id').val(premio.categoria_id || '');
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

    // Abrir modal primero - el select se cargará cuando el modal esté completamente abierto
    modalPremio.show();
}

async function editarPremio(id) {
    // Deshabilitar botón mientras carga
    const $btn = $(`button.btn-editar[data-id="${id}"]`);
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

    try {
        // Precargar datos y categorías en paralelo antes de abrir el modal
        const [respuestaPremio, respuestaCategorias] = await Promise.all([
            API.get('premios/getById', {
                id: id,
                sede_id: userInfo?.sede_id
            }),
            API.get('categorias/getAll', {
                sede_id: userInfo.sede_id
            })
        ]);

        // Restaurar botón
        $btn.prop('disabled', false).html(originalHtml);

        if (!respuestaPremio || !respuestaPremio.ok || !respuestaPremio.data) {
            Utils.showAlert(respuestaPremio?.msj || 'No se pudo obtener el premio', 'error');
            return;
        }

        // Cargar categorías en el select
        if (respuestaCategorias && respuestaCategorias.ok) {
            categoriasPremioData = respuestaCategorias.data || [];
            const $select = $('#categoria_id');
            if (categoriasPremioData.length) {
                const opciones = ['<option value="">Sin categoría</option>'].concat(
                    categoriasPremioData.map(cat => `<option value="${cat.id}">${cat.nombre}</option>`)
                );
                $select.html(opciones.join(''));
            } else {
                $select.html('<option value="">Sin categorías registradas</option>');
            }
        }

        // Llenar formulario con los datos
        const premio = respuestaPremio.data;
        limpiarFormularioPremio();
        
        $('#modal_premio_title').text('Editar Premio');
        $('#premio_id').val(premio.id);
        $('#categoria_id').val(premio.categoria_id || '');
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

        $('#sede_id').val(userInfo?.sede_id || '');
        $('#valor_estimado_simbolo').text(simboloMoneda);
        $('#valor_estimado_simbolo_prefix').text(simboloMoneda);

        $('#imagen_principal').val('');
        $('#imagen_secundaria').val('');
        $('#galeria_imagenes').val('');
        setPreviewTexto('#galeria_nuevas_preview', 'Sin archivos seleccionados');

        // Abrir modal con todo listo
        modalPremio.show();
    } catch (error) {
        // Restaurar botón en caso de error
        $btn.prop('disabled', false).html(originalHtml);
        console.error('Error al obtener premio:', error);
        Utils.showAlert('Ocurrió un problema al obtener el premio', 'error');
    }
}

async function guardarPremio() {
    if (!validarFormularioPremio()) {
        return;
    }

    const form = document.getElementById('form_premio');
    const premioId = $('#premio_id').val();
    const esEdicion = premioId !== '';

    // Deshabilitar botón de guardar para evitar doble clic
    const $btnGuardar = $('#form_premio button[type="submit"]');
    const originalBtnHtml = $btnGuardar.html();
    $btnGuardar.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-1"></i>Guardando...');

    const formData = new FormData(form);
    formData.set('sede_id', userInfo?.sede_id || '');
    formData.set('categoria_id', $('#categoria_id').val() || '');
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
        const premioExistente = premiosData.find(p => p.id == premioId);
        if (premioExistente && premioExistente.codigo) {
            formData.set('codigo', premioExistente.codigo);
        }
        formData.set('estado', $('#estado').val());
        formData.set('modificado_por', userInfo?.nombre_completo || 'SYSTEM');
    } else {
        formData.set('creado_por', userInfo?.nombre_completo || 'SYSTEM');
    }

    try {
        const endpoint = esEdicion ? 'premios/update' : 'premios/register';
        const respuesta = await API.postFormData(endpoint, formData);

        // Restaurar botón
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);

        if (respuesta && respuesta.ok) {
            Utils.showAlert(respuesta.msj || (esEdicion ? 'Premio actualizado correctamente' : 'Premio registrado correctamente'), 'success');
            modalPremio.hide();
            // Recargar tabla sin mostrar loading manual
            tablaPremios.ajax.reload();
        } else {
            // Mostrar el mensaje del servidor o uno genérico
            const mensajeError = respuesta?.msj || 'No se pudo guardar el premio';
            Utils.showAlert(mensajeError, 'error');
            console.error('Error al guardar:', respuesta);
        }
    } catch (error) {
        // Restaurar botón en caso de error
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);
        console.error('Error al guardar premio:', error);
        Utils.showAlert('Ocurrió un problema al guardar el premio', 'error');
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
                Utils.showAlert(respuesta.msj, 'warning');
            }
        }
    } catch (error) {
        console.error('Error al cargar categorías:', error);
        categoriasPremioData = [];
        $select.html('<option value="">Error al cargar categorías</option>');
        Utils.showAlert('Ocurrió un problema al cargar las categorías', 'error');
    } finally {
        if (valorSeleccionado) {
            $select.val(valorSeleccionado);
        }
        $select.prop('disabled', false);
    }
}

function validarFormularioPremio() {
    let esValido = true;
    Utils.limpiarValidaciones('form_premio');

    const camposObligatorios = [
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
    Utils.showConfirm(
        '¿Inactivar premio?',
        'Esta acción desactivará el premio seleccionado.',
        'Sí, inactivar',
        'Cancelar'
    ).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const respuesta = await API.post('premios/delete', {
                    id: id,
                    sede_id: userInfo?.sede_id,
                    modificado_por: userInfo?.nombre_completo || 'SYSTEM'
                });

                if (respuesta && respuesta.ok) {
                    Utils.showAlert(respuesta.msj, 'success');
                    // Recargar tabla sin mostrar loading manual
                    tablaPremios.ajax.reload();
                } else {
                    Utils.showAlert(respuesta?.msj || 'No se pudo inactivar el premio', 'error');
                }
            } catch (error) {
                console.error('Error al inactivar premio:', error);
                Utils.showAlert('Ocurrió un problema al inactivar el premio', 'error');
            }
        }
    });
}
