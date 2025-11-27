/**
 * Módulo de Ventas Administrativas
 * Permite a los administradores registrar ventas de tickets directamente
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
    showAlert(message, type = 'info') {
        if (window.Utils?.showAlert) {
            return Utils.showAlert(message, type);
        } else if (typeof Swal !== 'undefined') {
            return Swal.fire({
                icon: type === 'error' ? 'error' : type === 'success' ? 'success' : 'info',
                title: type === 'error' ? 'Error' : type === 'success' ? 'Éxito' : 'Información',
                text: message,
                confirmButtonText: 'Aceptar'
            });
        } else {
            alert(message);
            return Promise.resolve({ isConfirmed: true });
        }
    },
    formatCurrency(value) {
        if (window.Utils?.formatearMoneda) {
            return Utils.formatearMoneda(value);
        }
        const amount = parseFloat(value || 0);
        return `S/. ${amount.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    }
};

let tablaRifasVentas = null;
let userInfo = null;
let rifasData = [];
let modalVenta = null;
let rifaSeleccionadaVenta = null;

// Variables globales para el proceso de venta
window.numerosSeleccionadosVenta = [];
window.cantidadTicketsRequeridaVenta = 1;
let precioUnitarioVenta = 0;
let ticketsDisponiblesVenta = 0;
let numerosDisponiblesVenta = [];
let rifaNombreGlobalVenta = '';

$(document).ready(async () => {
    if (!Auth.requireAuth()) return;

    userInfo = Auth.getUserInfo();
    modalVenta = new bootstrap.Modal(document.getElementById('modal_registrar_venta'));

    inicializarSelectSede();
    inicializarTablas();
    inicializarEventosUI();

    await cargarRifasVentas();
});

function inicializarSelectSede() {
    if (!userInfo) return;
    const option = `<option value="${userInfo.sede_id}">${userInfo.sede_nombre || 'Sede principal'}</option>`;
    $('#filtro_sede_venta').html(option).val(userInfo.sede_id);
}

function inicializarTablas() {
    tablaRifasVentas = $('#tabla_rifas_ventas').DataTable({
        processing: false,
        serverSide: false,
        data: [],
        language: Utils.getDataTableLanguageES(),
        columns: [
            {
                data: null,
                orderable: false,
                className: 'text-center',
                render: function(data, type, row) {
                    return `
                        <button class="btn btn-success btn-sm btn-registrar-venta" 
                                data-rifa-id="${row.id}" 
                                title="Registrar venta">
                            <i class="ri-shopping-cart-line"></i> Vender
                        </button>
                    `;
                }
            },
            { data: 'codigo' },
            { data: 'nombre' },
            {
                data: 'premio_principal',
                render: function(data) {
                    return data || '-';
                }
            },
            {
                data: 'precio_ticket',
                render: function(data) {
                    return SafeUtils.formatCurrency(data || 0);
                }
            },
            {
                data: 'numeros_disponibles',
                className: 'text-center',
                render: function(data) {
                    return `<span class="badge bg-success">${data || 0}</span>`;
                }
            },
            {
                data: 'numeros_vendidos',
                className: 'text-center',
                render: function(data) {
                    return `<span class="badge bg-danger">${data || 0}</span>`;
                }
            },
            {
                data: 'estado',
                render: function(data) {
                    const estados = {
                        'EN_VENTA': '<span class="badge bg-success">En Venta</span>',
                        'PUBLICADA': '<span class="badge bg-info">Publicada</span>',
                        'CERRADA': '<span class="badge bg-secondary">Cerrada</span>',
                        'BORRADOR': '<span class="badge bg-warning">Borrador</span>'
                    };
                    return estados[data] || `<span class="badge bg-secondary">${data}</span>`;
                }
            }
        ],
        order: [[1, 'desc']],
        pageLength: 10,
        responsive: true
    });
}

function inicializarEventosUI() {
    // Botón filtrar
    $('#btn_filtrar_ventas').on('click', () => {
        cargarRifasVentas();
    });

    // Botón recargar
    $('#btn_recargar_ventas').on('click', () => {
        $('#filtro_sede_venta').val(userInfo.sede_id);
        $('#filtro_estado_venta').val('');
        cargarRifasVentas();
    });

    // Registrar venta
    $(document).on('click', '.btn-registrar-venta', function() {
        const rifaId = $(this).data('rifa-id');
        abrirModalVenta(rifaId);
    });

    // Navegación de tabs
    $('.nexttab').on('click', function() {
        const nextTabId = $(this).data('nexttab');
        const nextTab = new bootstrap.Tab(document.getElementById(nextTabId));
        nextTab.show();
    });

    $('.previestab').on('click', function() {
        const prevTabId = $(this).data('previous');
        const prevTab = new bootstrap.Tab(document.getElementById(prevTabId));
        prevTab.show();
    });

    // Botones cantidad tickets
    $('#venta_btn_mas').on('click', () => {
        const input = $('#venta_cantidad_tickets');
        const valor = parseInt(input.val()) || 1;
        const max = parseInt(input.attr('max')) || 999;
        if (valor < max) {
            input.val(valor + 1).trigger('change');
        }
    });

    $('#venta_btn_menos').on('click', () => {
        const input = $('#venta_cantidad_tickets');
        const valor = parseInt(input.val()) || 1;
        if (valor > 1) {
            input.val(valor - 1).trigger('change');
        }
    });

    // Cambio de cantidad
    $('#venta_cantidad_tickets').on('change', function() {
        const cantidad = parseInt($(this).val()) || 1;
        window.cantidadTicketsRequeridaVenta = cantidad;
        actualizarResumenVenta();
        validarTabOrdenVenta();
    });

    // Validación de formulario
    $('#form_registrar_venta').on('submit', async function(e) {
        e.preventDefault();
        await confirmarVenta();
    });

    // Validar tab personal
    $('#venta-personal-tab').on('shown.bs.tab', () => {
        validarTabPersonalVenta();
    });

    $('#venta-orden-tab').on('shown.bs.tab', () => {
        validarTabOrdenVenta();
    });

    // Validación en tiempo real
    $('#venta_nombres, #venta_apellidos, #venta_telefono, #venta_tipo_documento, #venta_numero_documento').on('input change', () => {
        validarTabPersonalVenta();
    });

    // Resetear modal al cerrar
    $('#modal_registrar_venta').on('hidden.bs.modal', function() {
        resetearModalVenta();
    });
}

async function cargarRifasVentas() {
    try {
        SafeUtils.showLoading('Cargando rifas...');
        
        const sedeId = $('#filtro_sede_venta').val() || userInfo.sede_id;
        const estado = $('#filtro_estado_venta').val();
        
        const params = new URLSearchParams({ sede_id: sedeId });
        if (estado) params.append('estado', estado);
        
        const response = await fetch(`${API_BASE_URL}/rifas/getAll?${params}`, {
            headers: {
                'Authorization': `Bearer ${Auth.getToken()}`
            }
        });
        
        const resultado = await response.json();
        
        if (resultado.ok && resultado.data) {
            rifasData = resultado.data;
            tablaRifasVentas.clear().rows.add(rifasData).draw();
        } else {
            SafeUtils.showToast('No se pudieron cargar las rifas', 'error');
        }
    } catch (error) {
        console.error('Error al cargar rifas:', error);
        SafeUtils.showToast('Error al cargar las rifas', 'error');
    } finally {
        SafeUtils.closeLoading();
    }
}

async function abrirModalVenta(rifaId) {
    try {
        SafeUtils.showLoading('Cargando información de la rifa...');
        
        const rifa = rifasData.find(r => r.id == rifaId);
        if (!rifa) {
            SafeUtils.showToast('Rifa no encontrada', 'error');
            return;
        }
        
        rifaSeleccionadaVenta = rifa;
        
        // Cargar números disponibles
        await cargarNumerosDisponibles(rifaId);
        
        // Inicializar modal
        inicializarModalConRifa(rifa);
        
        // Mostrar modal
        modalVenta.show();
    } catch (error) {
        console.error('Error al abrir modal de venta:', error);
        SafeUtils.showToast('Error al cargar la información', 'error');
    } finally {
        SafeUtils.closeLoading();
    }
}

function inicializarModalConRifa(rifa) {
    rifaNombreGlobalVenta = rifa.nombre || '';
    precioUnitarioVenta = parseFloat(rifa.precio_ticket) || 0;
    ticketsDisponiblesVenta = parseInt(rifa.numeros_disponibles) || 0;
    
    $('#modal_titulo_rifa_venta').text(`Registrar Venta - ${rifa.nombre}`);
    $('#venta_rifa_id').val(rifa.id);
    $('#venta_precio_ticket').text(precioUnitarioVenta.toFixed(2));
    $('#venta_tickets_disponibles').text(ticketsDisponiblesVenta);
    $('#venta_tickets_disponibles_resumen').text(ticketsDisponiblesVenta);
    $('#venta_cantidad_tickets').attr('max', ticketsDisponiblesVenta || 999);
    
    actualizarResumenVenta();
}

async function cargarNumerosDisponibles(rifaId) {
    try {
        const params = new URLSearchParams({
            rifa_id: rifaId,
            estado: 'DISPONIBLE'
        });
        
        const response = await fetch(`${API_BASE_URL}/rifas/numeros/get?${params}`, {
            headers: {
                'Authorization': `Bearer ${Auth.getToken()}`
            }
        });
        
        const resultado = await response.json();
        
        if (resultado.ok && resultado.data) {
            numerosDisponiblesVenta = resultado.data;
        } else {
            numerosDisponiblesVenta = [];
        }
    } catch (error) {
        console.error('Error al cargar números:', error);
        numerosDisponiblesVenta = [];
    }
}

function validarTabPersonalVenta() {
    const nombres = $('#venta_nombres').val().trim();
    const apellidos = $('#venta_apellidos').val().trim();
    const telefono = $('#venta_telefono').val().trim();
    const tipoDoc = $('#venta_tipo_documento').val();
    const numDoc = $('#venta_numero_documento').val().trim();
    
    const esValido = nombres.length > 0 && apellidos.length > 0 && telefono.length > 0 && tipoDoc && numDoc.length > 0;
    
    $('#btn_continuar_venta_personal').prop('disabled', !esValido);
    
    return esValido;
}

function validarTabOrdenVenta() {
    const cantidad = parseInt($('#venta_cantidad_tickets').val()) || 1;
    const numerosReservados = $('#venta_numeros_reservados').val();
    const displayVisible = $('#venta_numero_seleccionado_display').is(':visible');
    
    let cantidadNumerosSeleccionados = 0;
    
    if (displayVisible && numerosReservados) {
        try {
            const numeros = JSON.parse(numerosReservados);
            cantidadNumerosSeleccionados = Array.isArray(numeros) ? numeros.length : 0;
        } catch (e) {
            cantidadNumerosSeleccionados = window.numerosSeleccionadosVenta.length;
        }
    }
    
    const numerosValidos = cantidadNumerosSeleccionados === cantidad;
    const cantidadValida = cantidad > 0 && cantidad <= ticketsDisponiblesVenta;
    
    $('#btn_continuar_venta_orden').prop('disabled', !(cantidadValida && numerosValidos));
    
    return cantidadValida && numerosValidos;
}

function actualizarResumenVenta() {
    const cantidad = parseInt($('#venta_cantidad_tickets').val()) || 1;
    const total = cantidad * precioUnitarioVenta;
    
    $('#venta_cantidad_display').text(cantidad);
    $('#venta_total_pagar').text(total.toFixed(2));
    $('#venta_resumen_cantidad').text(cantidad);
    $('#venta_resumen_precio').text(SafeUtils.formatCurrency(precioUnitarioVenta));
    $('#venta_resumen_total').text(SafeUtils.formatCurrency(total));
}

// Funciones para selección de números (similares a landing.js)
window.mostrarGridNumerosVenta = function() {
    const cantidad = parseInt($('#venta_cantidad_tickets').val()) || 1;
    const modal = new bootstrap.Modal(document.getElementById('modal_seleccionar_numero_venta'));
    
    mostrarGridNumerosVenta_Render(numerosDisponiblesVenta, cantidad);
    modal.show();
};

function mostrarGridNumerosVenta_Render(numeros, cantidadMaxima) {
    const grid = $('#venta_grid_numeros_disponibles');
    grid.empty();
    
    numeros.forEach(numero => {
        const estaSeleccionado = window.numerosSeleccionadosVenta.some(n => n.numero_entero === numero.numero_entero);
        const puedeSeleccionar = window.numerosSeleccionadosVenta.length < cantidadMaxima;
        
        const btnClass = estaSeleccionado ? 'btn-success' : 
                         numero.estado === 'DISPONIBLE' && puedeSeleccionar ? 'btn-outline-primary' : 
                         'btn-secondary';
        
        const btn = $(`
            <div class="col-6 col-md-3 col-lg-2">
                <button type="button" 
                        class="btn ${btnClass} w-100 mb-2" 
                        data-numero="${numero.numero_entero}"
                        ${estaSeleccionado || !puedeSeleccionar ? 'disabled' : ''}
                        onclick="seleccionarNumeroVenta(${numero.numero_entero}, '${numero.numero_formateado}')">
                    ${numero.numero_formateado}
                </button>
            </div>
        `);
        
        grid.append(btn);
    });
}

window.seleccionarNumeroVenta = function(numeroEntero, numeroFormateado) {
    if (window.numerosSeleccionadosVenta.length >= window.cantidadTicketsRequeridaVenta) {
        SafeUtils.showToast('Ya has seleccionado la cantidad máxima de números', 'warning');
        return;
    }
    
    const existe = window.numerosSeleccionadosVenta.find(n => n.numero_entero === numeroEntero);
    if (existe) {
        SafeUtils.showToast('Este número ya está seleccionado', 'warning');
        return;
    }
    
    window.numerosSeleccionadosVenta.push({
        numero_entero: numeroEntero,
        numero_formateado: numeroFormateado
    });
    
    actualizarDisplayNumerosVenta();
    mostrarGridNumerosVenta_Render(numerosDisponiblesVenta, window.cantidadTicketsRequeridaVenta);
    validarTabOrdenVenta();
};

window.asignarNumerosAleatoriosVenta = async function() {
    const cantidad = parseInt($('#venta_cantidad_tickets').val()) || 1;
    
    if (cantidad > numerosDisponiblesVenta.length) {
        SafeUtils.showToast('No hay suficientes números disponibles', 'error');
        return;
    }
    
    // Filtrar números disponibles que no estén seleccionados
    const disponibles = numerosDisponiblesVenta.filter(n => 
        n.estado === 'DISPONIBLE' && 
        !window.numerosSeleccionadosVenta.some(sel => sel.numero_entero === n.numero_entero)
    );
    
    // Seleccionar aleatoriamente
    const seleccionados = [];
    for (let i = 0; i < cantidad && disponibles.length > 0; i++) {
        const randomIndex = Math.floor(Math.random() * disponibles.length);
        const numero = disponibles.splice(randomIndex, 1)[0];
        seleccionados.push({
            numero_entero: numero.numero_entero,
            numero_formateado: numero.numero_formateado
        });
    }
    
    window.numerosSeleccionadosVenta = seleccionados;
    actualizarDisplayNumerosVenta();
    validarTabOrdenVenta();
};

function actualizarDisplayNumerosVenta() {
    const display = $('#venta_numero_seleccionado_display');
    const lista = $('#venta_lista_numeros_seleccionados');
    const contador = $('#venta_contador_numeros');
    
    if (window.numerosSeleccionadosVenta.length === 0) {
        display.hide();
        $('#venta_numeros_reservados').val('');
        $('#venta_numeros_formateados').val('');
        return;
    }
    
    display.show();
    contador.text(`${window.numerosSeleccionadosVenta.length}/${window.cantidadTicketsRequeridaVenta}`);
    
    lista.empty();
    window.numerosSeleccionadosVenta.forEach(numero => {
        lista.append(`
            <span class="badge bg-primary fs-12 p-2">
                ${numero.numero_formateado}
                <button type="button" class="btn-close btn-close-white ms-1" 
                        onclick="eliminarNumeroVenta(${numero.numero_entero})" 
                        style="font-size: 0.7em;"></button>
            </span>
        `);
    });
    
    // Actualizar campos ocultos
    const enterosArray = window.numerosSeleccionadosVenta.map(n => n.numero_entero);
    const formateadosArray = window.numerosSeleccionadosVenta.map(n => n.numero_formateado);
    
    $('#venta_numeros_reservados').val(JSON.stringify(enterosArray));
    $('#venta_numeros_formateados').val(JSON.stringify(formateadosArray));
    
    // Actualizar resumen
    actualizarResumenNumerosVenta();
}

window.eliminarNumeroVenta = function(numeroEntero) {
    window.numerosSeleccionadosVenta = window.numerosSeleccionadosVenta.filter(
        n => n.numero_entero !== numeroEntero
    );
    actualizarDisplayNumerosVenta();
    mostrarGridNumerosVenta_Render(numerosDisponiblesVenta, window.cantidadTicketsRequeridaVenta);
    validarTabOrdenVenta();
};

window.cancelarTodasLasSeleccionesVenta = function() {
    window.numerosSeleccionadosVenta = [];
    actualizarDisplayNumerosVenta();
    mostrarGridNumerosVenta_Render(numerosDisponiblesVenta, window.cantidadTicketsRequeridaVenta);
    validarTabOrdenVenta();
};

window.buscarNumeroVenta = function() {
    const busqueda = $('#venta_buscar_numero').val().trim();
    if (!busqueda) {
        SafeUtils.showToast('Ingresa un número para buscar', 'warning');
        return;
    }
    
    const numeroEncontrado = numerosDisponiblesVenta.find(n => 
        n.numero_formateado.includes(busqueda) || 
        String(n.numero_entero) === busqueda
    );
    
    if (!numeroEncontrado) {
        SafeUtils.showToast(`No se encontró el número: ${busqueda}`, 'error');
        return;
    }
    
    // Scroll al número
    const btn = $(`#venta_grid_numeros_disponibles button[data-numero="${numeroEncontrado.numero_entero}"]`);
    if (btn.length) {
        btn[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        btn.addClass('animate__animated animate__pulse');
        setTimeout(() => btn.removeClass('animate__animated animate__pulse'), 1000);
    }
};

function actualizarResumenNumerosVenta() {
    const resumenNumeros = $('#venta_resumen_numeros');
    resumenNumeros.empty();
    
    if (window.numerosSeleccionadosVenta.length > 0) {
        $('#venta_resumen_numero_row').show();
        window.numerosSeleccionadosVenta.forEach(numero => {
            resumenNumeros.append(`<span class="badge bg-primary">${numero.numero_formateado}</span>`);
        });
    } else {
        $('#venta_resumen_numero_row').hide();
    }
}

async function confirmarVenta() {
    try {
        // Validar campos requeridos antes de enviar
        const nombres = $('#venta_nombres').val().trim();
        const apellidos = $('#venta_apellidos').val().trim();
        
        if (!nombres || nombres === '') {
            SafeUtils.showToast('El campo Nombres es obligatorio', 'error');
            $('#venta_nombres').focus();
            return;
        }
        
        if (!apellidos || apellidos === '') {
            SafeUtils.showToast('El campo Apellidos es obligatorio', 'error');
            $('#venta_apellidos').focus();
            return;
        }
        
        const btnConfirmar = $('#btn_confirmar_venta');
        const textoOriginal = btnConfirmar.html();
        btnConfirmar.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-1"></i> Procesando...');
        
        // Obtener datos del formulario
        const datosVenta = {
            sede_id: userInfo.sede_id,
            rifa_id: parseInt($('#venta_rifa_id').val()),
            nombres: nombres,
            apellidos: apellidos,
            tipo_documento: $('#venta_tipo_documento').val(),
            numero_documento: $('#venta_numero_documento').val().trim(),
            email: $('#venta_email').val().trim() || null,
            telefono: $('#venta_telefono').val().trim(),
            ciudad: $('#venta_ciudad').val().trim() || null,
            direccion: $('#venta_direccion').val().trim() || null,
            cantidad_tickets: parseInt($('#venta_cantidad_tickets').val()) || 1,
            precio_pagado: parseFloat($('#venta_total_pagar').text().replace('S/.', '').trim()) || 0,
            numeros_reservados: $('#venta_numeros_reservados').val(),
            numeros_formateados: $('#venta_numeros_formateados').val(),
            canal_venta: 'ADMINISTRATIVO',
            estado_inicial: $('input[name="estado_pago"]:checked').val() === 'PAGADO' ? 'APROBADO' : 'PENDIENTE_PAGO'
        };
        
        // Enviar al backend
        const response = await fetch(`${API_BASE_URL}/tickets/create`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${Auth.getToken()}`
            },
            body: JSON.stringify(datosVenta)
        });
        
        const resultado = await response.json();
        
        if (resultado.ok) {
            const codigoTicket = resultado.codigo_ticket || resultado.data?.codigo_ticket || 'N/A';
            
            // Si el estado es APROBADO, el ticket ya fue creado aprobado directamente
            // No necesita validación de comprobante porque es venta administrativa
            
            await SafeUtils.showAlert(
                `Venta registrada exitosamente.\n\nCódigo de ticket: ${codigoTicket}`,
                'success'
            );
            
            // Cerrar modal y recargar tabla
            modalVenta.hide();
            await cargarRifasVentas();
        } else {
            const mensajeError = resultado.msj || resultado.detalle || 'No se pudo procesar la venta.';
            await SafeUtils.showAlert(mensajeError, 'error');
            btnConfirmar.prop('disabled', false).html(textoOriginal);
        }
    } catch (error) {
        console.error('Error al confirmar venta:', error);
        await SafeUtils.showAlert('Error de conexión. Por favor, intenta nuevamente.', 'error');
        $('#btn_confirmar_venta').prop('disabled', false).html('Confirmar Venta');
    }
}

function resetearModalVenta() {
    $('#form_registrar_venta')[0].reset();
    window.numerosSeleccionadosVenta = [];
    window.cantidadTicketsRequeridaVenta = 1;
    
    // Resetear tabs
    $('#venta-personal-tab').tab('show');
    $('#venta-orden-tab').prop('disabled', true);
    $('#venta-confirmar-tab').prop('disabled', true);
    
    // Resetear display
    $('#venta_numero_seleccionado_display').hide();
    $('#venta_numeros_reservados').val('');
    $('#venta_numeros_formateados').val('');
    
    // Resetear resumen
    actualizarResumenVenta();
}

// Actualizar resumen cuando cambia el tab de confirmar
$('#venta-confirmar-tab').on('shown.bs.tab', function() {
    const nombres = $('#venta_nombres').val();
    const apellidos = $('#venta_apellidos').val();
    const nombreCompleto = `${nombres} ${apellidos}`.trim();
    const tipoDoc = $('#venta_tipo_documento').val();
    const numDoc = $('#venta_numero_documento').val();
    const telefono = $('#venta_telefono').val();
    const cantidad = parseInt($('#venta_cantidad_tickets').val()) || 1;
    const total = cantidad * precioUnitarioVenta;
    
    $('#venta_resumen_rifa').text(rifaNombreGlobalVenta);
    $('#venta_resumen_cliente').text(nombreCompleto);
    $('#venta_resumen_documento').text(`${tipoDoc}: ${numDoc}`);
    $('#venta_resumen_telefono').text(telefono);
    $('#venta_resumen_cantidad').text(cantidad);
    $('#venta_resumen_precio').text(SafeUtils.formatCurrency(precioUnitarioVenta));
    $('#venta_resumen_total').text(SafeUtils.formatCurrency(total));
    
    actualizarResumenNumerosVenta();
});

