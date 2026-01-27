/**
 * Gestión de Tickets
 */

let tablaTickets;
let ticketsData = [];
let rifasData = [];
let userInfo = null;
let modalValidarTicket;

$(document).ready(function () {
    if (!Auth.requireAuth()) {
        return;
    }

    userInfo = Auth.getUserInfo();
    modalValidarTicket = new bootstrap.Modal(document.getElementById('modal_validar_ticket'));

    inicializarTabla();
    inicializarEventos();
});

function inicializarTabla() {
    tablaTickets = $('#tabla_tickets').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.API_BASE_URL + '/tickets/getAll',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            },
            data: function (d) {
                d.sede_id = userInfo?.sede_id || '';
                const rifaId = $('#filtro_rifa_ticket').val();
                if (rifaId && rifaId !== '') {
                    d.rifa_id = rifaId;
                }
                const estado = $('#filtro_estado_ticket').val();
                if (estado && estado !== '') {
                    d.estado = estado;
                }
                return d;
            },
            dataSrc: function (json) {
                if (json && json.ok) {
                    ticketsData = json.data || [];
                    return ticketsData;
                } else {
                    ticketsData = [];
                    return [];
                }
            },
            error: function (xhr, error, thrown) {
                console.error('Error al cargar tickets:', error);
                ticketsData = [];
                if (xhr.status === 401) {
                    Auth.logout();
                } else {
                    Utils.showAlert('Error de conexión al cargar los tickets', 'error');
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
                width: '120px',
                render: (_, __, row) => {
                    let botones = `
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-info btn-ver-detalle btn-action-table" data-id="${row.id}" title="Ver detalle">
                                <i class="ri-eye-line"></i>
                            </button>
                    `;
                    
                    // Mostrar botón de validar solo si el estado lo permite
                    if (row.estado === 'PENDIENTE_PAGO' || row.estado === 'PAGO_SUBIDO' || row.estado === 'VALIDANDO') {
                        botones += `
                            <button class="btn btn-sm btn-primary btn-validar-ticket btn-action-table" data-id="${row.id}" title="Validar ticket">
                                <i class="ri-checkbox-circle-line"></i>
                            </button>
                        `;
                    }
                    
                    botones += `</div>`;
                    return botones;
                }
            },
            { data: 'codigo_ticket' },
            {
                data: null,
                render: (row) => `${row.rifa_codigo || ''} - ${row.rifa_nombre || ''}`
            },
            {
                data: null,
                render: (row) => `${row.nombres || ''} ${row.apellidos || ''}`.trim() || '-'
            },
            {
                data: null,
                render: (row) => `${row.tipo_documento || 'DNI'}: ${row.numero_documento || ''}`
            },
            {
                data: 'numero_boleto',
                render: (value) => {
                    if (!value) return '-';
                    // Si hay múltiples números separados por coma, mostrar como badges
                    if (value.includes(',')) {
                        const numeros = value.split(',').map(n => n.trim());
                        return numeros.map(n => `<span class="badge bg-primary me-1">${n}</span>`).join('');
                    }
                    return `<span class="badge bg-primary">${value}</span>`;
                }
            },
            {
                data: 'precio_pagado',
                render: (data) => Utils.formatearMoneda(data)
            },
            {
                data: 'estado',
                render: (estado) => obtenerBadgeEstadoTicket(estado)
            },
            {
                data: 'fecha_compra',
                render: (fecha) => fecha ? Utils.formatearFechaHora(fecha) : '-'
            }
        ]
    });
}

function inicializarEventos() {
    // Cargar rifas para el filtro
    cargarRifas();

    // Botones de filtro
    $('#btn_filtrar_tickets').on('click', function () {
        tablaTickets.ajax.reload();
    });

    $('#btn_recargar_tickets').on('click', function () {
        $('#filtro_rifa_ticket').val('');
        $('#filtro_estado_ticket').val('');
        tablaTickets.ajax.reload();
    });

    // Eventos de tabla
    $('#tabla_tickets tbody').on('click', '.btn-ver-detalle', function () {
        const id = $(this).data('id');
        verDetalleTicket(id);
    });

    $('#tabla_tickets tbody').on('click', '.btn-validar-ticket', function () {
        const id = $(this).data('id');
        abrirModalValidarTicket(id);
    });

    // Cambio de acción en modal
    $('#accion_ticket').on('change', function () {
        if ($(this).val() === 'RECHAZADO') {
            $('#contenedor_motivo_rechazo_ticket').removeClass('d-none');
            $('#motivo_rechazo_ticket').prop('required', true);
        } else {
            $('#contenedor_motivo_rechazo_ticket').addClass('d-none');
            $('#motivo_rechazo_ticket').prop('required', false).val('');
        }
    });

    // Submit del formulario
    $('#form_validar_ticket').on('submit', async function (event) {
        event.preventDefault();
        await guardarValidacionTicket();
    });
}

async function cargarRifas() {
    if (!userInfo) return;
    
    try {
        const respuesta = await API.get('rifas/getAll', { sede_id: userInfo.sede_id });
        if (respuesta?.ok) {
            rifasData = respuesta.data || [];
            const options = '<option value="">Todas las rifas</option>' +
                rifasData.map(r => `<option value="${r.id}">${r.codigo} - ${r.nombre}</option>`).join('');
            $('#filtro_rifa_ticket').html(options);
        }
    } catch (error) {
        console.error('Error al cargar rifas:', error);
    }
}

function obtenerBadgeEstadoTicket(estado) {
    const map = {
        'PENDIENTE_PAGO': { text: 'Pendiente de pago', class: 'badge-soft-warning' },
        'PAGO_SUBIDO': { text: 'Pago subido', class: 'badge-soft-info' },
        'VALIDANDO': { text: 'Validando', class: 'badge-soft-primary' },
        'APROBADO': { text: 'Aprobado', class: 'badge-soft-success' },
        'RECHAZADO': { text: 'Rechazado', class: 'badge-soft-danger' },
        'PARTICIPANDO': { text: 'Participando', class: 'badge-soft-success' },
        'GANADOR': { text: 'Ganador', class: 'badge-soft-success' },
        'EXPIRADO': { text: 'Expirado', class: 'badge-soft-secondary' }
    };
    const info = map[estado] || { text: estado, class: 'badge-soft-secondary' };
    return `<span class="badge ${info.class}">${info.text}</span>`;
}

async function verDetalleTicket(id) {
    try {
        const ticket = ticketsData.find(t => t.id == id);
        if (!ticket) {
            // Si no está en los datos cargados, obtenerlo
            const respuesta = await API.get('tickets/getAll', { sede_id: userInfo.sede_id });
            if (respuesta?.ok) {
                const ticketEncontrado = respuesta.data.find(t => t.id == id);
                if (ticketEncontrado) {
                    mostrarDetalleTicket(ticketEncontrado);
                    return;
                }
            }
            Utils.showAlert('Ticket no encontrado', 'error');
            return;
        }
        mostrarDetalleTicket(ticket);
    } catch (error) {
        console.error('Error al obtener detalle:', error);
        Utils.showAlert('Error al obtener el detalle del ticket', 'error');
    }
}

function mostrarDetalleTicket(ticket) {
    const detalle = `
        <div class="row g-3">
            <div class="col-md-6">
                <p><strong>Rifa:</strong> ${ticket.rifa_codigo || ''} - ${ticket.rifa_nombre || ''}</p>
                <p><strong>Cliente:</strong> ${ticket.nombres || ''} ${ticket.apellidos || ''}</p>
                <p><strong>Documento:</strong> ${ticket.tipo_documento || 'DNI'} ${ticket.numero_documento || ''}</p>
                <p><strong>Email:</strong> ${ticket.email || '-'}</p>
                <p><strong>Teléfono:</strong> ${ticket.telefono || '-'}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Números:</strong> ${ticket.numero_boleto || 'No asignado'}</p>
                <p><strong>Precio:</strong> ${Utils.formatearMoneda(ticket.precio_pagado)}</p>
                <p><strong>Estado:</strong> ${obtenerBadgeEstadoTicket(ticket.estado)}</p>
                <p><strong>Fecha Compra:</strong> ${ticket.fecha_compra ? Utils.formatearFechaHora(ticket.fecha_compra) : '-'}</p>
                ${ticket.motivo_rechazo ? `<p><strong>Motivo Rechazo:</strong> ${ticket.motivo_rechazo}</p>` : ''}
            </div>
        </div>
    `;
    
    Swal.fire({
        title: 'Detalle del Ticket',
        html: detalle,
        width: '800px',
        confirmButtonText: 'Cerrar'
    });
}

async function abrirModalValidarTicket(id) {
    // Deshabilitar botón mientras carga
    const $btn = $(`button.btn-validar-ticket[data-id="${id}"]`);
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<i class="ri-loader-4-line"></i>');

    try {
        // Obtener datos del ticket y comprobantes en paralelo
        const [respuestaTicket, respuestaComprobantes] = await Promise.all([
            API.get('tickets/getAll', { sede_id: userInfo.sede_id }),
            API.get('tickets/getComprobantes', { sede_id: userInfo.sede_id })
        ]);

        // Restaurar botón
        $btn.prop('disabled', false).html(originalHtml);

        if (!respuestaTicket?.ok) {
            Utils.showAlert(respuestaTicket?.msj || 'No se pudo obtener la información del ticket', 'error');
            return;
        }

        const ticket = respuestaTicket.data.find(t => t.id == id);
        if (!ticket) {
            Utils.showAlert('Ticket no encontrado', 'error');
            return;
        }

        // Llenar información del ticket
        $('#ticket_id_validar').val(ticket.id);
        $('#sede_id_ticket_validar').val(userInfo.sede_id);
        
        let infoTicket = `
            <div class="row g-2">
                <div class="col-md-6"><strong>Código:</strong> <span class="badge bg-primary">${ticket.codigo_ticket}</span></div>
                <div class="col-md-6"><strong>Estado:</strong> ${obtenerBadgeEstadoTicket(ticket.estado)}</div>
                <div class="col-md-12"><strong>Cliente:</strong> ${ticket.nombres || ''} ${ticket.apellidos || ''}</div>
                <div class="col-md-6"><strong>Documento:</strong> ${ticket.tipo_documento || 'DNI'} ${ticket.numero_documento || ''}</div>
                <div class="col-md-6"><strong>Email:</strong> ${ticket.email || '-'}</div>
                <div class="col-md-12"><strong>Rifa:</strong> ${ticket.rifa_codigo || ''} - ${ticket.rifa_nombre || ''}</div>
        `;
        
        // Mostrar número reservado si existe
        if (ticket.numero_boleto || ticket.numero_boleto_entero) {
            infoTicket += `
                <div class="col-md-12 mt-2">
                    <div class="alert alert-success mb-0 py-2">
                        <strong><i class="ri-number-1 me-1"></i>Números Asignados:</strong> 
                        <span class="badge bg-success fs-6">${ticket.numero_boleto || ticket.numero_boleto_entero}</span>
                    </div>
                </div>
            `;
            $('#alert_numero_ticket').show();
            $('#info_numero_ticket').html(`
                <span class="badge bg-warning text-dark fs-6">${ticket.numero_boleto || ticket.numero_boleto_entero}</span>
                <span class="ms-2">Estado: <span class="badge bg-info">RESERVADO</span></span>
            `);
        } else {
            $('#alert_numero_ticket').hide();
        }
        
        infoTicket += `</div>`;
        $('#info_ticket_validar').html(infoTicket);
        
        $('#precio_ticket_validar').val(Utils.formatearMoneda(ticket.precio_pagado || 0));
        $('#fecha_compra_ticket_validar').val(ticket.fecha_compra ? Utils.formatearFechaHora(ticket.fecha_compra) : '-');
        
        // Cargar comprobante si existe
        if (respuestaComprobantes?.ok && respuestaComprobantes.data?.length > 0) {
            const comprobante = respuestaComprobantes.data.find(c => c.ticket_id == id);
            
            if (comprobante && comprobante.archivo_comprobante) {
                const baseUrl = window.BASE_URL || '';
                const imageUrl = baseUrl + '/' + comprobante.archivo_comprobante.replace(/^\/+/, '');
                $('#preview_comprobante_ticket').html(`
                    <img src="${imageUrl}" class="img-fluid" style="max-height: 400px;" alt="Comprobante">
                    <br><a href="${imageUrl}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                        <i class="ri-external-link-line"></i> Ver en nueva ventana
                    </a>
                `);
            } else {
                $('#preview_comprobante_ticket').html('<p class="text-muted">No hay comprobante disponible</p>');
            }
        } else {
            $('#preview_comprobante_ticket').html('<p class="text-muted">No hay comprobante disponible</p>');
        }
        
        // Resetear formulario
        $('#accion_ticket').val('');
        $('#contenedor_motivo_rechazo_ticket').addClass('d-none');
        $('#motivo_rechazo_ticket').prop('required', false).val('');
        
        // Abrir modal con todo listo
        modalValidarTicket.show();
    } catch (error) {
        // Restaurar botón en caso de error
        $btn.prop('disabled', false).html(originalHtml);
        console.error('Error al cargar información del ticket:', error);
        Utils.showAlert('Ocurrió un problema al obtener la información del ticket', 'error');
    }
}

async function guardarValidacionTicket() {
    const accion = $('#accion_ticket').val();
    if (!accion) {
        Utils.showAlert('Seleccione una acción', 'warning');
        $('#accion_ticket').addClass('is-invalid');
        return;
    }

    if (accion === 'RECHAZADO' && !$('#motivo_rechazo_ticket').val().trim()) {
        Utils.showAlert('El motivo de rechazo es obligatorio', 'warning');
        $('#motivo_rechazo_ticket').addClass('is-invalid');
        return;
    }

    const confirmar = await Utils.showConfirm(
        accion === 'APROBADO' ? 'Aprobar ticket' : 'Rechazar ticket',
        accion === 'APROBADO' 
            ? '¿Está seguro de aprobar este ticket? El ticket pasará a estado APROBADO y podrá participar en el sorteo.'
            : '¿Está seguro de rechazar este ticket? El número será liberado.',
        'Sí, confirmar',
        'Cancelar'
    );

    if (!confirmar.isConfirmed) return;

    // Deshabilitar botón de guardar para evitar doble clic
    const $btnGuardar = $('#btn_guardar_validacion_ticket');
    const originalBtnHtml = $btnGuardar.html();
    $btnGuardar.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-1"></i>Guardando...');

    try {
        const ticketId = parseInt($('#ticket_id_validar').val());
        
        // Buscar el comprobante asociado al ticket
        const respuestaComprobantes = await API.get('tickets/getComprobantes', { 
            sede_id: userInfo.sede_id 
        });
        
        if (!respuestaComprobantes?.ok) {
            $btnGuardar.prop('disabled', false).html(originalBtnHtml);
            Utils.showAlert('Error al buscar comprobante', 'error');
            return;
        }
        
        const comprobante = respuestaComprobantes.data?.find(c => c.ticket_id == ticketId);
        
        if (!comprobante) {
            $btnGuardar.prop('disabled', false).html(originalBtnHtml);
            Utils.showAlert('No se encontró comprobante asociado a este ticket', 'warning');
            return;
        }
        
        // Validar el comprobante asociado
        const payload = {
            comprobante_id: comprobante.id,
            sede_id: userInfo.sede_id,
            estado: accion,
            validado_por: userInfo.nombre_completo || userInfo.username || 'SYSTEM',
            motivo_rechazo: accion === 'RECHAZADO' ? $('#motivo_rechazo_ticket').val().trim() : null
        };

        const respuesta = await API.post('tickets/validarComprobante', payload);

        // Restaurar botón
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);

        if (respuesta?.ok) {
            Utils.showAlert(respuesta.msj || 'Validación realizada correctamente', 'success');
            modalValidarTicket.hide();
            // Recargar tabla sin mostrar loading manual
            tablaTickets.ajax.reload();
        } else {
            Utils.showAlert(respuesta?.msj || 'Error al validar el ticket', 'error');
        }
    } catch (error) {
        // Restaurar botón en caso de error
        const $btnGuardar = $('#btn_guardar_validacion_ticket');
        $btnGuardar.prop('disabled', false).html('Guardar Validación');
        console.error('Error al validar ticket:', error);
        Utils.showAlert('Ocurrió un problema al validar el ticket', 'error');
    }
}
