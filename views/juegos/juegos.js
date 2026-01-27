/**
 * Gestión de Proceso de Juego de Rifas
 */

let tablaJuegos;
let juegosData = [];
let userInfo = null;
let modalJugarRifa;
let participantesData = [];
let premiosData = [];
let infoJuegoData = null;
let rifaSeleccionada = null;
let premioSeleccionado = null;
let resultadoJuego = null;

$(document).ready(function () {
    if (!Auth.requireAuth()) {
        return;
    }

    userInfo = Auth.getUserInfo();
    modalJugarRifa = new bootstrap.Modal(document.getElementById('modal_jugar_rifa'));

    inicializarTabla();
    inicializarEventos();
});

function inicializarTabla() {
    tablaJuegos = $('#tabla_juegos').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.API_BASE_URL + '/juegos/getRifasParaJugar',
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
                    juegosData = json.data || [];
                    return juegosData;
                } else {
                    juegosData = [];
                    return [];
                }
            },
            error: function (xhr, error, thrown) {
                console.error('Error al cargar rifas para jugar:', error);
                console.error('Response:', xhr.responseText);
                juegosData = [];
                if (xhr.status === 401) {
                    Auth.logout();
                } else {
                    const errorMsg = xhr.responseText ? JSON.parse(xhr.responseText)?.msj || 'Error de conexión' : 'Error de conexión al cargar las rifas';
                    Utils.showAlert(errorMsg, 'error');
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
                    return `
                        <button class="btn btn-sm btn-primary btn-jugar btn-action-table" data-id="${row.id}" title="Jugar">
                            <i class="ri-gamepad-line"></i>
                        </button>
                    `;
                }
            },
            { data: 'codigo' },
            { data: 'nombre' },
            {
                data: null,
                className: 'text-center',
                render: (_, __, row) => {
                    const total = row.total_premios || 0;
                    const ganados = row.premios_ganados || 0;
                    const badgeClass = ganados === total && total > 0 ? 'bg-success' : 'bg-info';
                    return `<span class="badge ${badgeClass}">${ganados}/${total}</span>`;
                }
            },
            {
                data: 'total_participantes',
                className: 'text-center',
                render: (data) => `<span class="badge bg-secondary">${data || 0}</span>`
            },
            {
                data: null,
                className: 'text-center',
                render: (_, __, row) => {
                    const vendidos = row.numeros_vendidos || 0;
                    const total = row.total_numeros || 0;
                    return `<span class="badge bg-warning">${vendidos} / ${total}</span>`;
                }
            },
            {
                data: 'estado_juego',
                render: (estado, type, row) => {
                    const vendidos = row.numeros_vendidos || 0;
                    const total = row.total_numeros || 0;
                    const todosVendidos = vendidos === total && total > 0;
                    
                    const map = {
                        'LISTA_PARA_JUGAR': { text: 'Lista para Jugar', class: 'badge-soft-success' },
                        'GANADOR_DEFINIDO': { text: 'Ganador Definido', class: 'badge-soft-info' },
                        'EN_VENTA': { text: todosVendidos ? 'Lista para Jugar' : 'En Venta', class: todosVendidos ? 'badge-soft-success' : 'badge-soft-warning' }
                    };
                    const info = map[estado] || map['EN_VENTA'];
                    return `<span class="badge ${info.class}">${info.text}</span>`;
                }
            },
            {
                data: 'fecha_sorteo',
                render: (fecha) => Utils.formatearFecha(fecha)
            }
        ]
    });
}

function inicializarEventos() {
    // Botón recargar
    $('#btn_recargar_juegos').on('click', function () {
        tablaJuegos.ajax.reload();
    });

    // Eventos de tabla
    $('#tabla_juegos tbody').on('click', '.btn-jugar', function () {
        const id = $(this).data('id');
        abrirModalJugar(id);
    });

    // Botón jugar premio
    $('#btn_jugar_rifa').on('click', async function () {
        await jugarPremio();
    });

    // Botón registrar ganador
    $('#btn_registrar_ganador').on('click', async function () {
        await registrarGanador();
    });

    // Botón volver a seleccionar premio
    $('#btn_volver_seleccionar').on('click', function () {
        mostrarPasoSeleccionarPremio();
    });

    // Cargar premios cuando se abre el modal
    $('#modal_jugar_rifa').on('shown.bs.modal', function () {
        const rifaId = $('#juego_rifa_id').val();
        if (rifaId) {
            cargarPremiosRifa(rifaId);
        }
    });

    // Limpiar al cerrar
    $('#modal_jugar_rifa').on('hidden.bs.modal', function () {
        limpiarModalJugar();
    });
}

async function abrirModalJugar(rifaId) {
    const rifa = juegosData.find(r => r.id == rifaId);
    if (!rifa) {
        Utils.showAlert('No se encontró la información de la rifa', 'error');
        return;
    }

    rifaSeleccionada = rifa;

    // Llenar información básica
    $('#juego_rifa_id').val(rifa.id);
    $('#juego_sede_id').val(userInfo?.sede_id || '');
    $('#juego_codigo_rifa').text(rifa.codigo || '-');
    $('#juego_nombre_rifa').text(rifa.nombre || '-');
    $('#juego_intento_ganador').text(rifa.intento_ganador || 5);
    $('#juego_numeros_vendidos').text(rifa.numeros_vendidos || 0);
    $('#juego_total_numeros').text(rifa.total_numeros || 0);
    $('#juego_total_participantes').text(rifa.total_participantes || 0);

    // Mostrar paso de selección de premio
    mostrarPasoSeleccionarPremio();

    // Abrir modal
    modalJugarRifa.show();
}

function mostrarPasoSeleccionarPremio() {
    $('#juego_paso_seleccionar_premio').show();
    $('#juego_paso_jugar').hide();
    $('#juego_paso_registrar_ganador').hide();
    $('#btn_jugar_rifa').hide();
    $('#btn_registrar_ganador').hide();
    $('#btn_volver_seleccionar').hide();
    $('#btn_cerrar_modal').show();
    
    // Limpiar estado del juego al volver a selección de premio
    limpiarEstadoJuego();
}

function mostrarPasoJugar() {
    $('#juego_paso_seleccionar_premio').hide();
    $('#juego_paso_jugar').show();
    $('#juego_paso_registrar_ganador').hide();
    $('#btn_jugar_rifa').show();
    $('#btn_registrar_ganador').hide();
    $('#btn_volver_seleccionar').show();
    $('#btn_cerrar_modal').show();
}

function mostrarPasoRegistrarGanador() {
    $('#juego_paso_seleccionar_premio').hide();
    $('#juego_paso_jugar').hide();
    $('#juego_paso_registrar_ganador').show();
    $('#btn_jugar_rifa').hide();
    $('#btn_registrar_ganador').show();
    $('#btn_volver_seleccionar').hide();
    $('#btn_cerrar_modal').show();
}

async function cargarPremiosRifa(rifaId) {
    const $lista = $('#juego_lista_premios');
    $lista.html('<p class="text-muted text-center">Cargando premios...</p>');

    try {
        const respuesta = await API.get('juegos/getPremiosRifa', {
            rifa_id: rifaId,
            sede_id: userInfo?.sede_id
        });

        if (respuesta && respuesta.ok && respuesta.data) {
            premiosData = respuesta.data || [];
            renderizarPremios(premiosData);
        } else {
            $lista.html('<p class="text-danger text-center">No se pudieron cargar los premios</p>');
        }
    } catch (error) {
        console.error('Error al cargar premios:', error);
        $lista.html('<p class="text-danger text-center">Error al cargar premios</p>');
    }
}

function renderizarPremios(premios) {
    const $lista = $('#juego_lista_premios');
    
    if (!premios || premios.length === 0) {
        $lista.html('<p class="text-muted text-center">No hay premios configurados para esta rifa</p>');
        return;
    }

    const items = premios.map((p) => {
        const tieneGanador = p.tiene_ganador > 0;
        const badgeClass = tieneGanador ? 'bg-success' : 'bg-primary';
        const iconClass = tieneGanador ? 'ri-trophy-fill' : 'ri-gamepad-line';
        const textoEstado = tieneGanador ? 'Ganador: ' + (p.ganador_info || 'Definido') : 'Jugar';
        
        return `
            <div class="card border mb-2 ${tieneGanador ? 'border-success' : 'border-primary'}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <i class="${iconClass} me-2"></i>${p.premio_nombre || p.titulo || 'Premio'}
                                ${p.es_principal ? '<span class="badge bg-warning ms-2">Principal</span>' : ''}
                            </h6>
                            <small class="text-muted">Código: ${p.premio_codigo || '-'}</small>
                            ${p.valor_estimado ? `<br><small class="text-muted">Valor: S/. ${parseFloat(p.valor_estimado).toFixed(2)}</small>` : ''}
                            ${tieneGanador ? `<br><small class="text-success"><strong>${textoEstado}</strong></small>` : ''}
                        </div>
                        <div>
                            ${!tieneGanador ? `
                                <button class="btn btn-sm btn-primary btn-seleccionar-premio" 
                                        data-rifa-premio-id="${p.rifa_premio_id}" 
                                        data-premio-id="${p.premio_id}"
                                        data-premio-nombre="${p.premio_nombre || p.titulo || 'Premio'}"
                                        data-premio-codigo="${p.premio_codigo || '-'}">
                                    <i class="ri-gamepad-line me-1"></i>Jugar
                                </button>
                            ` : `
                                <span class="badge ${badgeClass}">${textoEstado}</span>
                            `}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');

    $lista.html(items);

    // Evento para seleccionar premio
    $lista.find('.btn-seleccionar-premio').on('click', function () {
        const rifaPremioId = $(this).data('rifa-premio-id');
        const premioId = $(this).data('premio-id');
        const premioNombre = $(this).data('premio-nombre');
        const premioCodigo = $(this).data('premio-codigo');
        
        seleccionarPremio(rifaPremioId, premioId, premioNombre, premioCodigo);
    });
}

async function seleccionarPremio(rifaPremioId, premioId, premioNombre, premioCodigo) {
    premioSeleccionado = {
        rifa_premio_id: rifaPremioId,
        premio_id: premioId,
        nombre: premioNombre,
        codigo: premioCodigo
    };

    // Limpiar estado anterior del juego
    limpiarEstadoJuego();

    // Llenar información del premio
    $('#juego_premio_nombre').text(premioNombre);
    $('#juego_premio_codigo').text(premioCodigo);

    // Cargar información del juego para este premio
    await cargarInfoJuegoPremio(rifaPremioId);
    await cargarParticipantes(rifaSeleccionada.id);

    // Mostrar paso de jugar
    mostrarPasoJugar();
}

async function cargarInfoJuegoPremio(rifaPremioId) {
    try {
        const respuesta = await API.get('juegos/getInfoJuego', {
            rifa_id: rifaSeleccionada.id,
            rifa_premio_id: rifaPremioId,
            sede_id: userInfo?.sede_id
        });

        if (respuesta && respuesta.ok && respuesta.data) {
            infoJuegoData = respuesta.data;
            actualizarInfoJuego(infoJuegoData);
        }
    } catch (error) {
        console.error('Error al cargar información del juego:', error);
    }
}

function actualizarInfoJuego(info) {
    $('#juego_intento_actual').text(info.intentos_actuales || 0);
    $('#juego_intento_ganador_premio').text(info.intento_ganador || 5);
    $('#juego_total_intentos').text(info.numero_intentos || 5);
    
    // Si este premio ya tiene ganador, ocultar el resultado del juego anterior
    if (info.tiene_ganador > 0) {
        $('#juego_resultado_container').hide();
    }
}

async function cargarParticipantes(rifaId) {
    const $lista = $('#juego_lista_participantes');
    $lista.html('<p class="text-muted text-center">Cargando participantes...</p>');

    try {
        const respuesta = await API.get('juegos/getParticipantes', {
            rifa_id: rifaId,
            sede_id: userInfo?.sede_id
        });

        if (respuesta && respuesta.ok && respuesta.data) {
            participantesData = respuesta.data || [];
            renderizarParticipantes(participantesData);
        } else {
            $lista.html('<p class="text-muted text-center">No se pudieron cargar los participantes</p>');
        }
    } catch (error) {
        console.error('Error al cargar participantes:', error);
        $lista.html('<p class="text-danger text-center">Error al cargar participantes</p>');
    }
}

function renderizarParticipantes(participantes) {
    const $lista = $('#juego_lista_participantes');
    
    if (!participantes || participantes.length === 0) {
        $lista.html('<p class="text-muted text-center">No hay participantes</p>');
        return;
    }

    const items = participantes.map((p, index) => `
        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
            <div>
                <strong>${p.nombre_completo}</strong>
                <div class="small text-muted">
                    ${p.documento_completo} | 
                    ${p.cantidad_tickets} ticket(s) | 
                    Números: ${p.numeros_comprados || '-'}
                </div>
            </div>
        </div>
    `).join('');

    $lista.html(items);
}

async function jugarPremio() {
    const rifaId = $('#juego_rifa_id').val();
    const rifaPremioId = premioSeleccionado?.rifa_premio_id;
    const sedeId = $('#juego_sede_id').val();

    if (!rifaId || !rifaPremioId || !sedeId) {
        Utils.showAlert('Error: No se encontró la información necesaria', 'error');
        return;
    }

    // Deshabilitar botón
    const $btn = $('#btn_jugar_rifa');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-1"></i>Jugando...');

    try {
        const respuesta = await API.post('juegos/jugar', {
            rifa_id: parseInt(rifaId),
            rifa_premio_id: parseInt(rifaPremioId),
            sede_id: parseInt(sedeId),
            jugado_por: userInfo?.nombre_completo || 'SYSTEM'
        });

        // Restaurar botón
        $btn.prop('disabled', false).html(originalHtml);

        if (respuesta && respuesta.ok) {
            resultadoJuego = respuesta;
            
            // Mostrar resultado (esto mostrará el contenedor)
            mostrarResultadoJuego(respuesta);
            
            // Si es ganador, mostrar formulario de registro
            if (respuesta.es_ganador) {
                await llenarFormularioGanador(respuesta);
                mostrarPasoRegistrarGanador();
            } else {
                // Recargar información del juego para actualizar contadores
                await cargarInfoJuegoPremio(rifaPremioId);
                // El resultado ya está visible, no ocultarlo
            }
            
            // Recargar tabla
            tablaJuegos.ajax.reload();
        } else {
            Utils.showAlert(respuesta?.msj || 'No se pudo ejecutar el juego', 'error');
        }
    } catch (error) {
        // Restaurar botón
        $btn.prop('disabled', false).html(originalHtml);
        console.error('Error al jugar:', error);
        Utils.showAlert('Ocurrió un problema al ejecutar el juego', 'error');
    }
}

function mostrarResultadoJuego(resultado) {
    const $container = $('#juego_resultado_container');
    const $titulo = $('#juego_resultado_titulo');
    const $mensaje = $('#juego_resultado_mensaje');
    const $info = $('#juego_resultado_info');

    if (resultado.es_ganador) {
        // Es ganador
        $info.removeClass('alert-info').addClass('alert-success');
        $titulo.html('<i class="ri-trophy-fill me-2"></i>¡GANADOR!');
        
        $mensaje.html(`
            <strong>Número ${resultado.numero_formateado}</strong> ha ganado el premio en el intento ${resultado.intento_actual}!<br>
            <small class="text-muted"><strong>Ganador:</strong> ${resultado.nombre_completo}</small><br>
            <small class="text-muted">Complete los datos del ganador para finalizar.</small>
        `);
    } else {
        // Número seleccionado
        $info.removeClass('alert-success').addClass('alert-info');
        $titulo.html('<i class="ri-number-1 me-2"></i>Número Seleccionado');
        $mensaje.html(`
            <strong>Número ${resultado.numero_formateado}</strong> ha sido seleccionado en el intento ${resultado.intento_actual}.<br>
            <small class="text-muted"><strong>Propietario:</strong> ${resultado.nombre_completo}</small><br>
            <small class="text-muted">Continúa el proceso de juego.</small>
        `);
    }

    $container.show();
}

async function llenarFormularioGanador(resultado) {
    $('#ganador_rifa_id').val(rifaSeleccionada.id);
    $('#ganador_rifa_premio_id').val(premioSeleccionado.rifa_premio_id);
    $('#ganador_premio_id').val(premioSeleccionado.premio_id);
    $('#ganador_persona_id').val(resultado.persona_id);
    $('#ganador_numero_id').val(resultado.numero_id);
    $('#ganador_ticket_id').val(resultado.ticket_id);
    $('#ganador_intento_ganador').val(resultado.intento_actual);
    $('#ganador_nombre_completo').text(resultado.nombre_completo);
    
    // Buscar documento del ganador
    const ganador = participantesData.find(p => p.persona_id == resultado.persona_id);
    if (ganador) {
        $('#ganador_documento').text(ganador.documento_completo);
    }
    
    // Mostrar SOLO el número ganador específico (no todos los números que compró)
    if (resultado.numero_formateado) {
        $('#ganador_numeros').show();
        $('#ganador_numeros_lista').html(`<strong>${resultado.numero_formateado}</strong>`);
    } else {
        $('#ganador_numeros').hide();
    }
    
    // Limpiar formulario
    $('#ganador_direccion_envio').val('');
    $('#ganador_ciudad_envio').val('');
    $('#ganador_pais_envio').val('Perú');
    $('#ganador_publicar_web').prop('checked', false);
}

async function registrarGanador() {
    if (!validarFormularioGanador()) {
        return;
    }

    // Deshabilitar botón
    const $btn = $('#btn_registrar_ganador');
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-1"></i>Registrando...');

    // Obtener el primer ticket de la persona ganadora
    const ganador = participantesData.find(p => p.persona_id == $('#ganador_persona_id').val());
    let ticketId = null;
    if (ganador && ganador.cantidad_tickets > 0) {
        // Intentar obtener el ticket_id desde los datos del participante
        // Por ahora se deja null, el backend puede manejarlo
    }

    const payload = {
        sede_id: parseInt($('#juego_sede_id').val()),
        rifa_id: parseInt($('#ganador_rifa_id').val()),
        rifa_premio_id: parseInt($('#ganador_rifa_premio_id').val()),
        premio_id: parseInt($('#ganador_premio_id').val()),
        persona_id: parseInt($('#ganador_persona_id').val()),
        ticket_id: parseInt($('#ganador_ticket_id').val()) || null,
        numero_id: parseInt($('#ganador_numero_id').val()) || null,
        direccion_envio: $('#ganador_direccion_envio').val()?.trim() || null,
        ciudad_envio: $('#ganador_ciudad_envio').val()?.trim() || null,
        pais_envio: $('#ganador_pais_envio').val()?.trim() || 'Perú',
        publicar_web: $('#ganador_publicar_web').is(':checked'),
        intento_ganador: parseInt($('#ganador_intento_ganador').val()),
        jugado_por: userInfo?.nombre_completo || 'SYSTEM',
        creado_por: userInfo?.nombre_completo || 'SYSTEM'
    };

    try {
        const respuesta = await API.post('juegos/registrarGanador', payload);

        // Restaurar botón
        $btn.prop('disabled', false).html(originalHtml);

        if (respuesta && respuesta.ok) {
            Utils.showAlert(respuesta.msj || 'Ganador registrado correctamente', 'success');
            
            // Verificar si todos los premios tienen ganador
            await verificarRifaCompleta();
            
            // Recargar premios
            await cargarPremiosRifa(rifaSeleccionada.id);
            
            // Volver a selección de premio
            mostrarPasoSeleccionarPremio();
            
            // Recargar tabla
            tablaJuegos.ajax.reload();
        } else {
            Utils.showAlert(respuesta?.msj || 'No se pudo registrar el ganador', 'error');
        }
    } catch (error) {
        // Restaurar botón
        $btn.prop('disabled', false).html(originalHtml);
        console.error('Error al registrar ganador:', error);
        Utils.showAlert('Ocurrió un problema al registrar el ganador', 'error');
    }
}

async function verificarRifaCompleta() {
    try {
        const respuesta = await API.get('juegos/verificarRifaCompleta', {
            rifa_id: rifaSeleccionada.id,
            sede_id: userInfo?.sede_id
        });

        if (respuesta && respuesta.ok && respuesta.todos_premios_ganados) {
            Utils.showAlert(
                `¡Todos los premios han sido jugados! La rifa puede ser cerrada. (${respuesta.premios_ganados}/${respuesta.total_premios} premios)`,
                'success'
            );
        }
    } catch (error) {
        console.error('Error al verificar rifa completa:', error);
    }
}

function validarFormularioGanador() {
    // No hay campos obligatorios, todos son opcionales
    return true;
}

function limpiarEstadoJuego() {
    // Limpiar resultado del juego anterior
    $('#juego_resultado_container').hide();
    $('#juego_resultado_titulo').html('');
    $('#juego_resultado_mensaje').html('');
    $('#juego_resultado_info').removeClass('alert-success alert-info').addClass('alert-info');
    
    // Limpiar datos del resultado anterior
    resultadoJuego = null;
    
    // Limpiar información del juego anterior
    $('#juego_intento_actual').text('0');
    $('#juego_intento_ganador_premio').text('5');
    $('#juego_total_intentos').text('5');
}

function limpiarModalJugar() {
    $('#juego_rifa_id').val('');
    $('#juego_sede_id').val('');
    limpiarEstadoJuego();
    $('#juego_lista_participantes').html('<p class="text-muted text-center">Cargando participantes...</p>');
    $('#juego_lista_premios').html('<p class="text-muted text-center">Cargando premios...</p>');
    participantesData = [];
    premiosData = [];
    infoJuegoData = null;
    rifaSeleccionada = null;
    premioSeleccionado = null;
    resultadoJuego = null;
    mostrarPasoSeleccionarPremio();
}
