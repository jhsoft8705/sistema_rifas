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

$(document).ready(async () => {
    if (!Auth.requireAuth()) return;

    userInfo = Auth.getUserInfo();
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
                width: '100px',
                render: (_, __, row) => `
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-info btn-ver-detalle" data-id="${row.id}" title="Ver detalle">
                            <i class="ri-eye-line"></i>
                        </button>
                    </div>
                `
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

