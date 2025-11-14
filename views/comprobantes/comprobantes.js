/**
 * Validación de Comprobantes de Pago
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

let tablaComprobantes = null;
let userInfo = null;
let modalValidar = null;

$(document).ready(async () => {
    if (!Auth.requireAuth()) return;

    userInfo = Auth.getUserInfo();
    modalValidar = new bootstrap.Modal(document.getElementById('modal_validar_comprobante'));
    
    inicializarSelects();
    inicializarTabla();
    inicializarEventos();
    await cargarComprobantes();
});

function inicializarSelects() {
    if (!userInfo) return;
    const option = `<option value="${userInfo.sede_id}">${userInfo.sede_nombre || 'Sede principal'}</option>`;
    $('#filtro_sede_comprobante').html(option).val(userInfo.sede_id);
    $('#sede_id_comprobante_validar').val(userInfo.sede_id);
}

function inicializarTabla() {
    tablaComprobantes = $('#tabla_comprobantes').DataTable({
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
                width: '120px',
                render: (_, __, row) => {
                    if (row.estado === 'PENDIENTE' || row.estado === 'VALIDANDO') {
                        return `
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-primary btn-validar" data-id="${row.id}" title="Validar">
                                    <i class="ri-checkbox-circle-line"></i>
                                </button>
                            </div>
                        `;
                    }
                    return '-';
                }
            },
            { data: 'codigo_ticket' },
            {
                data: null,
                render: (row) => `${row.nombres || ''} ${row.apellidos || ''}`
            },
            {
                data: null,
                render: (row) => `${row.rifa_codigo || ''} - ${row.rifa_nombre || ''}`
            },
            {
                data: null,
                render: (row) => {
                    if (row.numero_boleto || row.numero_boleto_entero) {
                        return `<span class="badge bg-warning text-dark">${row.numero_boleto || row.numero_boleto_entero}</span>`;
                    }
                    return '<span class="text-muted">-</span>';
                }
            },
            {
                data: 'monto',
                render: SafeUtils.formatCurrency
            },
            {
                data: 'numero_operacion',
                render: (value) => value || '-'
            },
            {
                data: 'fecha_pago',
                render: SafeUtils.formatDate
            },
            {
                data: 'dias_esperando',
                render: (value) => value ? `${value} días` : '-'
            },
            {
                data: 'estado',
                render: (estado) => obtenerBadgeEstadoComprobante(estado)
            }
        ]
    });
}

function inicializarEventos() {
    $('#btn_filtrar_comprobantes').on('click', () => cargarComprobantes());
    $('#btn_recargar_comprobantes').on('click', () => {
        $('#filtro_estado_comprobante').val('');
        cargarComprobantes();
    });

    $('#tabla_comprobantes tbody').on('click', '.btn-validar', function () {
        const id = $(this).data('id');
        abrirModalValidar(id);
    });

    $('#accion_comprobante').on('change', function () {
        if ($(this).val() === 'RECHAZADO') {
            $('#contenedor_motivo_rechazo').removeClass('d-none');
            $('#motivo_rechazo_comprobante').prop('required', true);
        } else {
            $('#contenedor_motivo_rechazo').addClass('d-none');
            $('#motivo_rechazo_comprobante').prop('required', false).val('');
        }
    });

    $('#btn_guardar_validacion').on('click', async () => {
        await guardarValidacion();
    });
}

async function cargarComprobantes() {
    if (!userInfo) return;

    try {
        SafeUtils.showLoading('Cargando comprobantes...');
        const params = { sede_id: userInfo.sede_id };
        
        const estado = $('#filtro_estado_comprobante').val();
        if (estado) params.estado = estado;

        const respuesta = await API.get('tickets/getComprobantes', params);
        SafeUtils.closeLoading();

        if (respuesta?.ok) {
            tablaComprobantes.clear().rows.add(respuesta.data || []).draw();
        } else {
            SafeUtils.showToast(respuesta?.msj || 'Error al cargar comprobantes', 'error');
            tablaComprobantes.clear().draw();
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al cargar comprobantes', 'error');
        console.error(error);
    }
}

async function abrirModalValidar(id) {
    try {
        SafeUtils.showLoading('Cargando comprobante...');
        const respuesta = await API.get('tickets/getComprobantes', { sede_id: userInfo.sede_id });
        SafeUtils.closeLoading();

        if (respuesta?.ok) {
            const comprobante = respuesta.data.find(c => c.id == id);
            if (comprobante) {
                $('#comprobante_id_validar').val(comprobante.id);
                
                // Información del ticket
                let infoTicket = `
                    <div class="row g-2">
                        <div class="col-md-6"><strong>Código:</strong> <span class="badge bg-primary">${comprobante.codigo_ticket}</span></div>
                        <div class="col-md-6"><strong>Estado Ticket:</strong> ${obtenerBadgeEstadoTicket(comprobante.estado_ticket || 'PENDIENTE_PAGO')}</div>
                        <div class="col-md-12"><strong>Participante:</strong> ${comprobante.nombres} ${comprobante.apellidos}</div>
                        <div class="col-md-6"><strong>Documento:</strong> ${comprobante.tipo_documento || 'DNI'} ${comprobante.numero_documento}</div>
                        <div class="col-md-6"><strong>Email:</strong> ${comprobante.email}</div>
                        <div class="col-md-12"><strong>Rifa:</strong> ${comprobante.rifa_codigo} - ${comprobante.rifa_nombre}</div>
                        <div class="col-md-6"><strong>Precio:</strong> ${SafeUtils.formatCurrency(comprobante.precio_pagado)}</div>
                        <div class="col-md-6"><strong>Días esperando:</strong> ${comprobante.dias_esperando || 0} días</div>
                `;
                
                // Mostrar número reservado si existe
                if (comprobante.numero_boleto || comprobante.numero_boleto_entero) {
                    infoTicket += `
                        <div class="col-md-12 mt-2">
                            <div class="alert alert-success mb-0 py-2">
                                <strong><i class="ri-number-1 me-1"></i>Número Asignado:</strong> 
                                <span class="badge bg-success fs-6">${comprobante.numero_boleto || comprobante.numero_boleto_entero}</span>
                            </div>
                        </div>
                    `;
                    $('#alert_numero_reservado').show();
                    $('#info_numero_reservado').html(`
                        <span class="badge bg-warning text-dark fs-6">${comprobante.numero_boleto || comprobante.numero_boleto_entero}</span>
                        <span class="ms-2">Estado: <span class="badge bg-info">RESERVADO</span></span>
                    `);
                } else {
                    $('#alert_numero_reservado').hide();
                }
                
                infoTicket += `</div>`;
                $('#info_ticket_comprobante').html(infoTicket);
                
                $('#monto_comprobante').val(SafeUtils.formatCurrency(comprobante.monto));
                $('#numero_operacion_comprobante').val(comprobante.numero_operacion || '-');
                
                // Mostrar imagen del comprobante
                if (comprobante.archivo_comprobante) {
                    const baseUrl = window.BASE_URL || '';
                    const imageUrl = baseUrl + comprobante.archivo_comprobante;
                    $('#preview_comprobante').html(`
                        <img src="${imageUrl}" class="img-fluid" style="max-height: 400px;" alt="Comprobante">
                        <br><a href="${imageUrl}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="ri-external-link-line"></i> Ver en nueva ventana
                        </a>
                    `);
                } else {
                    $('#preview_comprobante').html('<p class="text-muted">No hay imagen disponible</p>');
                }
                
                $('#accion_comprobante').val('');
                $('#contenedor_motivo_rechazo').addClass('d-none');
                $('#motivo_rechazo_comprobante').val('');
                modalValidar.show();
            }
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al cargar comprobante', 'error');
        console.error(error);
    }
}

async function guardarValidacion() {
    const accion = $('#accion_comprobante').val();
    if (!accion) {
        SafeUtils.showToast('Seleccione una acción', 'warning');
        return;
    }

    if (accion === 'RECHAZADO' && !$('#motivo_rechazo_comprobante').val().trim()) {
        SafeUtils.showToast('El motivo de rechazo es obligatorio', 'warning');
        $('#motivo_rechazo_comprobante').addClass('is-invalid');
        return;
    }

    const confirmar = await Swal.fire({
        title: accion === 'APROBADO' ? 'Aprobar comprobante' : 'Rechazar comprobante',
        text: accion === 'APROBADO' 
            ? '¿Está seguro de aprobar este comprobante? El ticket pasará a estado APROBADO.'
            : '¿Está seguro de rechazar este comprobante?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmar.isConfirmed) return;

    try {
        SafeUtils.showLoading('Procesando validación...');
        const payload = {
            comprobante_id: parseInt($('#comprobante_id_validar').val()),
            sede_id: userInfo.sede_id,
            estado: accion,
            validado_por: userInfo.nombre_completo || 'SYSTEM',
            motivo_rechazo: accion === 'RECHAZADO' ? $('#motivo_rechazo_comprobante').val().trim() : null
        };

        const respuesta = await API.post('tickets/validarComprobante', payload);
        SafeUtils.closeLoading();

        if (respuesta?.ok) {
            SafeUtils.showToast(respuesta.msj, 'success');
            modalValidar.hide();
            await cargarComprobantes();
        } else {
            SafeUtils.showToast(respuesta?.msj || 'Error al validar comprobante', 'error');
        }
    } catch (error) {
        SafeUtils.closeLoading();
        SafeUtils.showToast('Error al validar comprobante', 'error');
        console.error(error);
    }
}

function obtenerBadgeEstadoComprobante(estado) {
    const map = {
        'PENDIENTE': { text: 'Pendiente', class: 'badge-soft-warning' },
        'VALIDANDO': { text: 'Validando', class: 'badge-soft-primary' },
        'APROBADO': { text: 'Aprobado', class: 'badge-soft-success' },
        'RECHAZADO': { text: 'Rechazado', class: 'badge-soft-danger' },
        'INVALIDO': { text: 'Inválido', class: 'badge-soft-danger' }
    };
    const info = map[estado] || { text: estado, class: 'badge-soft-secondary' };
    return `<span class="badge ${info.class}">${info.text}</span>`;
}

function obtenerBadgeEstadoTicket(estado) {
    const map = {
        'PENDIENTE_PAGO': { text: 'Pendiente Pago', class: 'badge-soft-warning' },
        'PAGO_SUBIDO': { text: 'Pago Subido', class: 'badge-soft-info' },
        'VALIDANDO': { text: 'Validando', class: 'badge-soft-primary' },
        'APROBADO': { text: 'Aprobado', class: 'badge-soft-success' },
        'RECHAZADO': { text: 'Rechazado', class: 'badge-soft-danger' },
        'PARTICIPANDO': { text: 'Participando', class: 'badge-soft-purple' },
        'GANADOR': { text: 'Ganador', class: 'badge-soft-success' },
        'EXPIRADO': { text: 'Expirado', class: 'badge-soft-secondary' }
    };
    const info = map[estado] || { text: estado, class: 'badge-soft-secondary' };
    return `<span class="badge ${info.class}">${info.text}</span>`;
}

