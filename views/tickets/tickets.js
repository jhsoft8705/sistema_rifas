/**
 * Gestión de Tickets
 */

const SafeUtils = {
    showLoading(message = 'Procesando...') {
        if (window.Utils?.showLoading) {
            Utils.showLoading(message);
        }
    },
    closeLoading() {
        if (window.Utils?.closeLoading) {
            Utils.closeLoading();
        }
    },
    showToast(message, type = 'info') {
        if (window.Utils?.showToast) {
            Utils.showToast(message, type);
        } else {
            const logFn = type === 'error' ? console.error : console.log;
            logFn(message);
        }
    },
    formatCurrency(value) {
        if (window.Utils?.formatearMoneda) {
            return Utils.formatearMoneda(value);
        }
        const amount = parseFloat(value || 0);
        return `S/. ${amount.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    },
    formatDate(value) {
        if (window.Utils?.formatearFecha) {
            return Utils.formatearFecha(value);
        }
        return value ? new Date(value).toLocaleString('es-PE') : '-';
    }
};

let tablaTickets = null;
let userInfo = null;
let rifasData = [];
let modalValidarTicket = null;

$(document).ready(async () => {
    if (!Auth.requireAuth()) return;

    userInfo = Auth.getUserInfo();
    modalValidarTicket = new bootstrap.Modal(document.getElementById('modal_validar_ticket'));
    inicializarSelects();
    inicializarTabla();
    inicializarEventos();
    await cargarRifas();
    await cargarTickets();
});

function inicializarSelects() {
    if (!userInfo) return;
    const option = `<option value="${userInfo.sede_id}">${userInfo.sede_nombre || 'Sede principal'}</option>`;
    $('#filtro_sede_ticket').html(option).val(userInfo.sede_id);
}

async function cargarRifas() {
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

function inicializarTabla() {
    tablaTickets = $('#tabla_tickets').DataTable({
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
                width: '150px',
                render: (_, __, row) => {
                    let botones = `
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-info btn-ver-detalle" data-id="${row.id}" title="Ver detalle">
                                <i class="ri-eye-line"></i>
                            </button>
                    `;
                    
                    // Mostrar botón de validar solo si el estado lo permite
                    if (row.estado === 'PENDIENTE_PAGO' || row.estado === 'PAGO_SUBIDO' || row.estado === 'VALIDANDO') {
                        botones += `
                            <button class="btn btn-primary btn-validar-ticket" data-id="${row.id}" title="Validar ticket">
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
                render: (row) => `${row.nombres || ''} ${row.apellidos || ''}`
            },
            { data: 'numero_documento' },
            {
                data: 'numero_boleto',
                render: (value) => value || '-'
            },
            {
                data: 'precio_pagado',
                render: SafeUtils.formatCurrency
            },
            {
                data: 'estado',
                render: (estado) => obtenerBadgeEstadoTicket(estado)
            },
            {
                data: 'fecha_compra',
                render: SafeUtils.formatDate
            }
        ]
    });
}

function inicializarEventos() {
    $('#btn_filtrar_tickets').on('click', () => cargarTickets());
    $('#btn_recargar_tickets').on('click', () => {
        $('#filtro_rifa_ticket').val('');
        $('#filtro_estado_ticket').val('');
        cargarTickets();
    });

    $('#tabla_tickets tbody').on('click', '.btn-ver-detalle', function () {
        const id = $(this).data('id');
        verDetalleTicket(id);
    });

    $('#tabla_tickets tbody').on('click', '.btn-validar-ticket', function () {
        const id = $(this).data('id');
        abrirModalValidarTicket(id);
    });

    $('#accion_ticket').on('change', function () {
        if ($(this).val() === 'RECHAZADO') {
            $('#contenedor_motivo_rechazo_ticket').removeClass('d-none');
            $('#motivo_rechazo_ticket').prop('required', true);
        } else {
            $('#contenedor_motivo_rechazo_ticket').addClass('d-none');
            $('#motivo_rechazo_ticket').prop('required', false).val('');
        }
    });

    $('#form_validar_ticket').on('submit', async function(e) {
        e.preventDefault();
        await guardarValidacionTicket();
    });
}

async function cargarTickets() {
    if (!userInfo) return;

    try {
        SafeUtils.showLoading('Cargando tickets...');
        const params = { sede_id: userInfo.sede_id };
        
        const rifaId = $('#filtro_rifa_ticket').val();
        if (rifaId) params.rifa_id = rifaId;
        
        const estado = $('#filtro_estado_ticket').val();
        if (estado) params.estado = estado;

        const respuesta = await API.get('tickets/getAll', params);
        SafeUtils.closeLoading();

        if (respuesta?.ok) {
            tablaTickets.clear().rows.add(respuesta.data || []).draw();
        } else {
            SafeUtils.showToast(respuesta?.msj || 'Error al cargar tickets', 'error');
            tablaTickets.clear().draw();
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al cargar tickets', 'error');
        console.error(error);
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
        SafeUtils.showLoading('Cargando detalle...');
        const respuesta = await API.get('tickets/getAll', { sede_id: userInfo.sede_id });
        SafeUtils.closeLoading();

        if (respuesta?.ok) {
            const ticket = respuesta.data.find(t => t.id == id);
            if (ticket) {
                const detalle = `
                    <div class="card">
                        <div class="card-header">
                            <h5>Detalle del Ticket: ${ticket.codigo_ticket}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Rifa:</strong> ${ticket.rifa_codigo} - ${ticket.rifa_nombre}</p>
                                    <p><strong>Participante:</strong> ${ticket.nombres} ${ticket.apellidos}</p>
                                    <p><strong>Documento:</strong> ${ticket.tipo_documento} ${ticket.numero_documento}</p>
                                    <p><strong>Email:</strong> ${ticket.email}</p>
                                    <p><strong>Teléfono:</strong> ${ticket.telefono}</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Número Boleto:</strong> ${ticket.numero_boleto || 'No asignado'}</p>
                                    <p><strong>Precio:</strong> ${SafeUtils.formatCurrency(ticket.precio_pagado)}</p>
                                    <p><strong>Estado:</strong> ${obtenerBadgeEstadoTicket(ticket.estado)}</p>
                                    <p><strong>Fecha Compra:</strong> ${SafeUtils.formatDate(ticket.fecha_compra)}</p>
                                    ${ticket.motivo_rechazo ? `<p><strong>Motivo Rechazo:</strong> ${ticket.motivo_rechazo}</p>` : ''}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                await Swal.fire({
                    title: 'Detalle del Ticket',
                    html: detalle,
                    width: '800px',
                    confirmButtonText: 'Cerrar'
                });
            }
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al obtener detalle', 'error');
        console.error(error);
    }
}

async function abrirModalValidarTicket(id) {
    try {
        SafeUtils.showLoading('Cargando información del ticket...');
        const respuesta = await API.get('tickets/getAll', { sede_id: userInfo.sede_id });
        SafeUtils.closeLoading();

        if (respuesta?.ok) {
            const ticket = respuesta.data.find(t => t.id == id);
            if (ticket) {
                $('#ticket_id_validar').val(ticket.id);
                $('#sede_id_ticket_validar').val(userInfo.sede_id);
                
                // Información del ticket
                let infoTicket = `
                    <div class="row g-2">
                        <div class="col-md-6"><strong>Código:</strong> <span class="badge bg-primary">${ticket.codigo_ticket}</span></div>
                        <div class="col-md-6"><strong>Estado:</strong> ${obtenerBadgeEstadoTicket(ticket.estado)}</div>
                        <div class="col-md-12"><strong>Participante:</strong> ${ticket.nombres} ${ticket.apellidos}</div>
                        <div class="col-md-6"><strong>Documento:</strong> ${ticket.tipo_documento || 'DNI'} ${ticket.numero_documento}</div>
                        <div class="col-md-6"><strong>Email:</strong> ${ticket.email}</div>
                        <div class="col-md-12"><strong>Rifa:</strong> ${ticket.rifa_codigo} - ${ticket.rifa_nombre}</div>
                `;
                
                // Mostrar número reservado si existe
                if (ticket.numero_boleto || ticket.numero_boleto_entero) {
                    infoTicket += `
                        <div class="col-md-12 mt-2">
                            <div class="alert alert-success mb-0 py-2">
                                <strong><i class="ri-number-1 me-1"></i>Número Asignado:</strong> 
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
                
                $('#precio_ticket_validar').val(SafeUtils.formatCurrency(ticket.precio_pagado));
                $('#fecha_compra_ticket_validar').val(SafeUtils.formatDate(ticket.fecha_compra));
                
                // Cargar comprobante si existe
                try {
                    const respuestaComprobantes = await API.get('tickets/getComprobantes', { 
                        sede_id: userInfo.sede_id
                    });
                    
                    if (respuestaComprobantes?.ok && respuestaComprobantes.data?.length > 0) {
                        // Filtrar comprobante por ticket_id
                        const comprobante = respuestaComprobantes.data.find(c => c.ticket_id == ticket.id);
                        
                        if (comprobante && comprobante.archivo_comprobante) {
                            const baseUrl = window.BASE_URL || '';
                            const imageUrl = baseUrl + comprobante.archivo_comprobante;
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
                } catch (error) {
                    console.error('Error al cargar comprobante:', error);
                    $('#preview_comprobante_ticket').html('<p class="text-muted">Error al cargar comprobante</p>');
                }
                
                $('#accion_ticket').val('');
                $('#contenedor_motivo_rechazo_ticket').addClass('d-none');
                $('#motivo_rechazo_ticket').val('');
                modalValidarTicket.show();
            }
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al cargar información del ticket', 'error');
        console.error(error);
    }
}

async function guardarValidacionTicket() {
    const accion = $('#accion_ticket').val();
    if (!accion) {
        SafeUtils.showToast('Seleccione una acción', 'warning');
        $('#accion_ticket').addClass('is-invalid');
        return;
    }

    if (accion === 'RECHAZADO' && !$('#motivo_rechazo_ticket').val().trim()) {
        SafeUtils.showToast('El motivo de rechazo es obligatorio', 'warning');
        $('#motivo_rechazo_ticket').addClass('is-invalid');
        return;
    }

    const confirmar = await Swal.fire({
        title: accion === 'APROBADO' ? 'Aprobar ticket' : 'Rechazar ticket',
        text: accion === 'APROBADO' 
            ? '¿Está seguro de aprobar este ticket? El ticket pasará a estado APROBADO y podrá participar en el sorteo.'
            : '¿Está seguro de rechazar este ticket? El número será liberado.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmar.isConfirmed) return;

    try {
        SafeUtils.showLoading('Procesando validación...');
        
        // Buscar el comprobante asociado al ticket
        const ticketId = parseInt($('#ticket_id_validar').val());
        const respuestaComprobantes = await API.get('tickets/getComprobantes', { 
            sede_id: userInfo.sede_id 
        });
        
        if (respuestaComprobantes?.ok) {
            const comprobante = respuestaComprobantes.data.find(c => c.ticket_id == ticketId);
            
            if (comprobante) {
                // Validar el comprobante asociado
                const payload = {
                    comprobante_id: comprobante.id,
                    sede_id: userInfo.sede_id,
                    estado: accion,
                    validado_por: userInfo.nombre_completo || 'SYSTEM',
                    motivo_rechazo: accion === 'RECHAZADO' ? $('#motivo_rechazo_ticket').val().trim() : null
                };

                const respuesta = await API.post('tickets/validarComprobante', payload);
                SafeUtils.closeLoading();

                if (respuesta?.ok) {
                    SafeUtils.showToast(respuesta.msj, 'success');
                    modalValidarTicket.hide();
                    await cargarTickets();
                } else {
                    SafeUtils.showToast(respuesta?.msj || 'Error al validar ticket', 'error');
                }
            } else {
                SafeUtils.closeLoading();
                SafeUtils.showToast('No se encontró comprobante asociado a este ticket', 'warning');
            }
        } else {
            SafeUtils.closeLoading();
            SafeUtils.showToast('Error al buscar comprobante', 'error');
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al validar ticket', 'error');
        console.error(error);
    }
}

