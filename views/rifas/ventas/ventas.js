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
let tablaVentasRealizadas = null;
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
    modalAprobarVenta = new bootstrap.Modal(document.getElementById('modal_aprobar_venta'));

    inicializarTablas();
    inicializarEventosUI();
    inicializarTablaVentas();
    cargarRifasParaFiltro();
});

function inicializarTablas() {
    tablaRifasVentas = $('#tabla_rifas_ventas').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.API_BASE_URL + '/rifas/getAll',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            },
            data: function (d) {
                d.sede_id = userInfo?.sede_id || '';
                const estado = $('#filtro_estado_venta').val();
                if (estado !== '') {
                    d.estado = estado;
                }
                return d;
            },
            dataSrc: function (json) {
                if (json && json.ok) {
                    rifasData = json.data || [];
                    return rifasData;
                } else {
                    rifasData = [];
                    return [];
                }
            },
            error: function (xhr, error, thrown) {
                console.error('Error al cargar rifas:', error);
                rifasData = [];
                if (xhr.status === 401) {
                    Auth.logout();
                } else {
                    Utils.showAlert('Error de conexión al cargar las rifas', 'error');
                }
            }
        },
        language: Utils.getDataTableLanguageES(),
        lengthChange: false,
        dom: 'frtip',
        pageLength: 10,
        responsive: true,
        columns: [
            {
                data: null,
                orderable: false,
                className: 'text-center',
                width: '140px',
                render: function(data, type, row) {
                    // Si la rifa está cerrada o finalizada, NO mostrar botón (ya está cerrada)
                    if (row.estado === 'CERRADA' || row.estado === 'FINALIZADA') {
                        return `<span class="text-muted small">Cerrada</span>`;
                    }
                    // Si está abierta, mostrar botón "Vender"
                    return `
                        <button class="btn btn-sm btn-success btn-registrar-venta" 
                                data-rifa-id="${row.id}" 
                                title="Registrar venta"
                                style="min-width: 100px;">
                            <i class="ri-shopping-cart-line me-1"></i>Vender
                        </button>
                    `;
                }
            },
            { data: 'codigo' },
            { data: 'nombre' },
            {
                data: 'premio_principal_nombre',
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
        order: [[1, 'desc']]
    });
}

function inicializarEventosUI() {
    // Botón filtrar
    $('#btn_filtrar_ventas').on('click', () => {
        tablaRifasVentas.ajax.reload();
    });

    // Botón recargar
    $('#btn_recargar_ventas').on('click', () => {
        $('#filtro_estado_venta').val('');
        tablaRifasVentas.ajax.reload();
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

    // Botón recargar ventas listado
    $('#btn_recargar_ventas_listado').on('click', () => {
        $('#filtro_rifa_ventas').val('');
        $('#filtro_estado_ventas').val('');
        if (tablaVentasRealizadas) {
            tablaVentasRealizadas.ajax.reload();
        }
    });

    // Botón filtrar ventas listado
    $('#btn_filtrar_ventas_listado').on('click', () => {
        if (tablaVentasRealizadas) {
            tablaVentasRealizadas.ajax.reload();
        }
    });

    // Cambio de acción en modal de aprobar
    $('#venta_accion_aprobar').on('change', function() {
        if ($(this).val() === 'RECHAZADO') {
            $('#venta_contenedor_motivo_rechazo').removeClass('d-none');
            $('#venta_motivo_rechazo').prop('required', true);
        } else {
            $('#venta_contenedor_motivo_rechazo').addClass('d-none');
            $('#venta_motivo_rechazo').prop('required', false).val('');
        }
    });

    // Formulario de aprobar venta
    $('#form_aprobar_venta').on('submit', async function(e) {
        e.preventDefault();
        await guardarAprobacionVenta();
    });

    // Cambio de tab para inicializar tabla de ventas
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        if (e.target.getAttribute('href') === '#ventas-realizadas') {
            if (!tablaVentasRealizadas) {
                inicializarTablaVentas();
            }
        }
    });
}

async function abrirModalVenta(rifaId) {
    // Deshabilitar botón mientras carga
    const $btn = $(`.btn-registrar-venta[data-rifa-id="${rifaId}"]`);
    const originalHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>');

    try {
        // Recargar datos frescos de la rifa y números disponibles en paralelo
        const [respuestaRifa, cantidadDisponible] = await Promise.all([
            API.get('rifas/getById', { id: rifaId, sede_id: userInfo.sede_id }),
            cargarNumerosDisponibles(rifaId)
        ]);
        
        // Restaurar botón
        $btn.prop('disabled', false).html(originalHtml);
        
        if (!respuestaRifa?.ok || !respuestaRifa.data) {
            Utils.showAlert(respuestaRifa?.msj || 'No se pudo obtener la información de la rifa', 'error');
            return;
        }
        
        const rifa = respuestaRifa.data;
        
        // Validar que la rifa no esté cerrada o finalizada
        if (rifa.estado === 'CERRADA' || rifa.estado === 'FINALIZADA') {
            Utils.showAlert('Esta rifa está cerrada. No se pueden realizar más ventas.', 'warning');
            return;
        }
        
        rifaSeleccionadaVenta = rifa;
        
        // Actualizar la cantidad disponible basada en los números realmente disponibles
        ticketsDisponiblesVenta = cantidadDisponible;
        
        // Inicializar modal con los datos actualizados
        inicializarModalConRifa(rifa);
        
        // Mostrar modal con todo listo
        modalVenta.show();
    } catch (error) {
        // Restaurar botón en caso de error
        $btn.prop('disabled', false).html(originalHtml);
        console.error('Error al abrir modal de venta:', error);
        SafeUtils.showToast('Error al cargar la información', 'error');
    }
}

function inicializarModalConRifa(rifa) {
    rifaNombreGlobalVenta = rifa.nombre || '';
    precioUnitarioVenta = parseFloat(rifa.precio_ticket) || 0;
    // Usar la cantidad real de números disponibles cargados, no el valor de la rifa que puede estar desactualizado
    const cantidadRealDisponible = numerosDisponiblesVenta.length;
    ticketsDisponiblesVenta = cantidadRealDisponible;
    
    $('#modal_titulo_rifa_venta').text(`Registrar Venta - ${rifa.nombre}`);
    $('#venta_rifa_id').val(rifa.id);
    $('#venta_precio_ticket').text(precioUnitarioVenta.toFixed(2));
    $('#venta_tickets_disponibles').text(cantidadRealDisponible);
    $('#venta_tickets_disponibles_resumen').text(cantidadRealDisponible);
    $('#venta_cantidad_tickets').attr('max', cantidadRealDisponible || 999);
    
    actualizarResumenVenta();
}

async function cargarNumerosDisponibles(rifaId) {
    try {
        const resultado = await API.get('rifas/numeros/disponibles', {
            rifa_id: rifaId,
            estado: 'DISPONIBLE'
        });
        
        if (resultado?.ok && resultado.data) {
            numerosDisponiblesVenta = resultado.data;
            return resultado.data.length;
        } else {
            numerosDisponiblesVenta = [];
            return 0;
        }
    } catch (error) {
        console.error('Error al cargar números:', error);
        numerosDisponiblesVenta = [];
        return 0;
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
        
        // Obtener números seleccionados - convertir a array de números enteros
        let numerosSeleccionados = null;
        const numerosReservadosJSON = $('#venta_numeros_reservados').val();
        
        if (numerosReservadosJSON && numerosReservadosJSON !== '' && numerosReservadosJSON !== '[]') {
            try {
                const numerosArray = JSON.parse(numerosReservadosJSON);
                // Asegurar que sea un array de números enteros
                if (Array.isArray(numerosArray)) {
                    numerosSeleccionados = numerosArray.map(n => {
                        // Si es un objeto, tomar el entero; si es un número, usarlo directamente
                        return typeof n === 'object' && n !== null ? n.numero_entero || n.entero || n : parseInt(n, 10);
                    }).filter(n => !isNaN(n) && n > 0);
                }
            } catch (e) {
                console.error('Error parsing números reservados:', e);
            }
        }
        
        // Si no hay números en el campo oculto pero hay números en memoria, usarlos
        if ((!numerosSeleccionados || numerosSeleccionados.length === 0) && 
            window.numerosSeleccionadosVenta && window.numerosSeleccionadosVenta.length > 0) {
            numerosSeleccionados = window.numerosSeleccionadosVenta.map(n => {
                return typeof n === 'object' && n !== null ? n.numero_entero || n.entero || n : parseInt(n, 10);
            }).filter(n => !isNaN(n) && n > 0);
        }
        
        console.log('🔵 [DEBUG VENTAS] Números a enviar al backend:', numerosSeleccionados);
        
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
            numeros_seleccionados: numerosSeleccionados, // Enviar como array de números enteros
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
            
            // Cerrar modal y recargar tablas
            modalVenta.hide();
            tablaRifasVentas.ajax.reload();
            if (tablaVentasRealizadas) {
                tablaVentasRealizadas.ajax.reload();
            }
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

// ==========================================================
// FUNCIONES PARA TABLA DE VENTAS REALIZADAS
// ==========================================================

function inicializarTablaVentas() {
    if (tablaVentasRealizadas) {
        return; // Ya está inicializada
    }

    tablaVentasRealizadas = $('#tabla_ventas_realizadas').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.API_BASE_URL + '/tickets/listVentas',
            type: 'GET',
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            },
            data: function (d) {
                d.sede_id = userInfo?.sede_id || '';
                const rifaId = $('#filtro_rifa_ventas').val();
                if (rifaId && rifaId !== '') {
                    d.rifa_id = rifaId;
                }
                const estado = $('#filtro_estado_ventas').val();
                if (estado && estado !== '') {
                    d.estado = estado;
                }
                return d;
            },
            dataSrc: function (json) {
                if (json && json.ok) {
                    return json.data || [];
                } else {
                    return [];
                }
            },
            error: function (xhr, error, thrown) {
                console.error('Error al cargar ventas:', error);
                if (xhr.status === 401) {
                    Auth.logout();
                } else {
                    Utils.showAlert('Error de conexión al cargar las ventas', 'error');
                }
            }
        },
        language: Utils.getDataTableLanguageES(),
        lengthChange: true,
        dom: 'frtip',
        pageLength: 25,
        responsive: true,
        columns: [
            {
                data: null,
                orderable: false,
                className: 'text-center',
                width: '120px',
                render: function(data) {
                    let acciones = '';
                    // Botón para ver/imprimir comprobante
                    acciones += `<button class="btn btn-sm btn-info btn-comprobante me-1 btn-action-table" data-ticket-id="${data.id}" title="Ver comprobante">
                        <i class="ri-file-3-line"></i>
                    </button>`;
                    
                    // Botón para aprobar/rechazar si el estado lo permite
                    if (data.estado === 'PENDIENTE_PAGO' || data.estado === 'PAGO_SUBIDO' || data.estado === 'VALIDANDO') {
                        acciones += `<button class="btn btn-sm btn-success btn-aprobar-venta me-1 btn-action-table" data-ticket-id="${data.id}" title="Aprobar pago">
                            <i class="ri-checkbox-circle-line"></i>
                        </button>`;
                    }
                    
                    return acciones || '-';
                }
            },
            { data: 'codigo_ticket' },
            {
                data: null,
                title: 'Números Comprados',
                render: function(data) {
                    // Mostrar todos los números comprados si existen, sino mostrar el número del boleto
                    if (data.numeros_comprados && data.numeros_comprados.trim() !== '') {
                        const cantidad = data.cantidad_numeros || 1;
                        const numeros = data.numeros_comprados.split(', ').map(n => `<span class="badge bg-primary me-1">${n}</span>`).join('');
                        const badgeCantidad = cantidad > 1 ? `<span class="badge bg-info ms-1" title="Cantidad de números">${cantidad}</span>` : '';
                        return `<div>${numeros}${badgeCantidad}</div>`;
                    } else if (data.numero_boleto) {
                        return `<span class="badge bg-primary">${data.numero_boleto}</span>`;
                    }
                    return '-';
                }
            },
            {
                data: null,
                render: function(data) {
                    return `${data.nombres || ''} ${data.apellidos || ''}`.trim() || '-';
                }
            },
            {
                data: null,
                render: function(data) {
                    const tipoDoc = data.tipo_documento || 'DNI';
                    const numDoc = data.numero_documento || '-';
                    return `${tipoDoc}: ${numDoc}`;
                }
            },
            { data: 'rifa_nombre' },
            {
                data: 'precio_pagado',
                render: function(data) {
                    return SafeUtils.formatCurrency(data || 0);
                }
            },
            {
                data: 'estado',
                render: function(data) {
                    const estados = {
                        'PENDIENTE_PAGO': '<span class="badge bg-warning">Pendiente Pago</span>',
                        'PAGO_SUBIDO': '<span class="badge bg-info">Pago Subido</span>',
                        'VALIDANDO': '<span class="badge bg-primary">Validando</span>',
                        'APROBADO': '<span class="badge bg-success">Aprobado</span>',
                        'RECHAZADO': '<span class="badge bg-danger">Rechazado</span>',
                        'PARTICIPANDO': '<span class="badge bg-success">Participando</span>',
                        'GANADOR': '<span class="badge bg-success">Ganador</span>',
                        'EXPIRADO': '<span class="badge bg-secondary">Expirado</span>'
                    };
                    return estados[data] || `<span class="badge bg-secondary">${data}</span>`;
                }
            },
            {
                data: 'fecha_creacion',
                render: function(data) {
                    if (!data) return '-';
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-PE', {
                        year: 'numeric',
                        month: '2-digit',
                        day: '2-digit',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }
        ],
        order: [[8, 'desc']]
    });
    
    // Evento para botón de comprobante
    $('#tabla_ventas_realizadas tbody').on('click', '.btn-comprobante', async function() {
        const $btn = $(this);
        const ticketId = $btn.data('ticket-id');
        const originalHtml = $btn.html();
        
        // Deshabilitar botón y mostrar spinner
        $btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin"></i>');
        
        try {
            await mostrarComprobante(ticketId);
        } finally {
            // Restaurar botón
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
    
    // Evento para botón de aprobar venta
    $('#tabla_ventas_realizadas tbody').on('click', '.btn-aprobar-venta', async function() {
        const $btn = $(this);
        const ticketId = $btn.data('ticket-id');
        const originalHtml = $btn.html();
        
        // Deshabilitar botón y mostrar spinner
        $btn.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin"></i>');
        
        try {
            await abrirModalAprobarVenta(ticketId);
        } finally {
            // Restaurar botón
            $btn.prop('disabled', false).html(originalHtml);
        }
    });
}

async function cargarRifasParaFiltro() {
    try {
        const response = await fetch(`${window.API_BASE_URL}/rifas/getAll?sede_id=${userInfo?.sede_id || ''}`, {
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            }
        });
        
        const resultado = await response.json();
        
        if (resultado.ok && resultado.data) {
            const select = $('#filtro_rifa_ventas');
            select.html('<option value="">Todas las rifas</option>');
            resultado.data.forEach(rifa => {
                select.append(`<option value="${rifa.id}">${rifa.codigo} - ${rifa.nombre}</option>`);
            });
        }
    } catch (error) {
        console.error('Error al cargar rifas para filtro:', error);
    }
}

// ==========================================================
// FUNCIONES PARA COMPROBANTE
// ==========================================================

let modalComprobante = null;
let modalAprobarVenta = null;
let datosComprobante = null;

async function mostrarComprobante(ticketId) {
    try {
        const response = await fetch(`${window.API_BASE_URL}/tickets/getComprobante?ticket_id=${ticketId}&sede_id=${userInfo.sede_id}`, {
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            }
        });
        
        const resultado = await response.json();
        
        if (resultado.ok && resultado.data) {
            datosComprobante = resultado.data;
            llenarModalComprobante(resultado.data);
            if (!modalComprobante) {
                modalComprobante = new bootstrap.Modal(document.getElementById('modal_comprobante'));
            }
            modalComprobante.show();
        } else {
            SafeUtils.showToast(resultado.msj || 'No se pudo cargar el comprobante', 'error');
        }
    } catch (error) {
        console.error('Error al cargar comprobante:', error);
        SafeUtils.showToast('Error al cargar el comprobante', 'error');
    }
}

function llenarModalComprobante(datos) {
    // Formatear fecha
    const fecha = datos.fecha_creacion ? new Date(datos.fecha_creacion) : new Date();
    const fechaFormateada = fecha.toLocaleDateString('es-PE', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
    
    // Llenar datos del comprobante
    $('#comprobante_codigo').text(datos.codigo_ticket || '-');
    $('#comprobante_fecha').text(fechaFormateada);
    $('#comprobante_cliente').text(`${datos.nombres || ''} ${datos.apellidos || ''}`.trim() || '-');
    $('#comprobante_documento').text(`${datos.tipo_documento || ''}: ${datos.numero_documento || ''}`.trim() || '-');
    $('#comprobante_telefono').text(datos.telefono || '-');
    $('#comprobante_email').text(datos.email || '-');
    $('#comprobante_rifa').text(datos.rifa_nombre || '-');
    
    // Formatear números comprados como badges
    let numerosHtml = '-';
    if (datos.numeros_comprados && datos.numeros_comprados.trim() !== '') {
        const numeros = datos.numeros_comprados.split(', ').map(n => `<span class="badge bg-primary me-1">${n}</span>`).join('');
        numerosHtml = `<div class="d-flex flex-wrap gap-1">${numeros}</div>`;
    } else if (datos.numero_boleto) {
        numerosHtml = `<span class="badge bg-primary">${datos.numero_boleto}</span>`;
    }
    $('#comprobante_numeros').html(numerosHtml);
    
    $('#comprobante_cantidad').text(`${datos.cantidad_numeros || 1} ticket(s)`);
    $('#comprobante_precio_unitario').text(SafeUtils.formatCurrency(datos.precio_ticket || 0));
    $('#comprobante_total').text(SafeUtils.formatCurrency(datos.precio_pagado || 0));
    $('#comprobante_estado').html(obtenerBadgeEstado(datos.estado || ''));
    $('#comprobante_sede').text(datos.sede_nombre || '-');
    
    // Guardar ticket_id para acciones
    $('#modal_comprobante').data('ticket-id', datos.id);
}

function obtenerBadgeEstado(estado) {
    const estados = {
        'PENDIENTE_PAGO': '<span class="badge bg-warning">Pendiente Pago</span>',
        'PAGO_SUBIDO': '<span class="badge bg-info">Pago Subido</span>',
        'VALIDANDO': '<span class="badge bg-primary">Validando</span>',
        'APROBADO': '<span class="badge bg-success">Aprobado</span>',
        'RECHAZADO': '<span class="badge bg-danger">Rechazado</span>',
        'PARTICIPANDO': '<span class="badge bg-success">Participando</span>',
        'GANADOR': '<span class="badge bg-success">Ganador</span>',
        'EXPIRADO': '<span class="badge bg-secondary">Expirado</span>'
    };
    return estados[estado] || `<span class="badge bg-secondary">${estado}</span>`;
}

function copiarComprobante() {
    const contenido = generarTextoComprobante();
    navigator.clipboard.writeText(contenido).then(() => {
        SafeUtils.showToast('Comprobante copiado al portapapeles', 'success');
    }).catch(() => {
        SafeUtils.showToast('Error al copiar el comprobante', 'error');
    });
}

function compartirComprobante() {
    const texto = generarTextoComprobante();
    const url = window.location.href;
    
    if (navigator.share) {
        navigator.share({
            title: 'Comprobante de Compra - ' + datosComprobante.codigo_ticket,
            text: texto,
            url: url
        }).catch(() => {
            SafeUtils.showToast('Error al compartir', 'error');
        });
    } else {
        // Fallback: copiar al portapapeles
        copiarComprobante();
    }
}

function generarTextoComprobante() {
    if (!datosComprobante) return '';
    
    return `COMPROBANTE DE COMPRA
Código: ${datosComprobante.codigo_ticket}
Fecha: ${new Date(datosComprobante.fecha_creacion).toLocaleDateString('es-PE')}
Cliente: ${datosComprobante.nombres} ${datosComprobante.apellidos}
Documento: ${datosComprobante.tipo_documento}: ${datosComprobante.numero_documento}
Rifa: ${datosComprobante.rifa_nombre}
Números: ${datosComprobante.numeros_comprados || datosComprobante.numero_boleto}
Total: ${SafeUtils.formatCurrency(datosComprobante.precio_pagado)}`;
}

async function imprimirComprobantePDF() {
    try {
        // Cargar html2pdf desde CDN si no está disponible
        if (typeof html2pdf === 'undefined') {
            await cargarLibreriaPDF();
        }
        
        const elemento = document.getElementById('contenido_comprobante_imprimir');
        const opt = {
            margin: 0.5,
            filename: `comprobante_${datosComprobante.codigo_ticket}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2 },
            jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
        };
        
        await html2pdf().set(opt).from(elemento).save();
        SafeUtils.showToast('PDF generado exitosamente', 'success');
    } catch (error) {
        console.error('Error al generar PDF:', error);
        SafeUtils.showToast('Error al generar el PDF', 'error');
    }
}

function cargarLibreriaPDF() {
    return new Promise((resolve, reject) => {
        if (typeof html2pdf !== 'undefined') {
            resolve();
            return;
        }
        
        const script = document.createElement('script');
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
        script.onload = resolve;
        script.onerror = reject;
        document.head.appendChild(script);
    });
}

// ==========================================================
// FUNCIONES PARA APROBAR/RECHAZAR VENTA
// ==========================================================

async function abrirModalAprobarVenta(ticketId) {
    try {
        // Obtener información del ticket
        const responseTicket = await fetch(`${window.API_BASE_URL}/tickets/getAll?sede_id=${userInfo.sede_id}`, {
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            }
        });
        
        const resultadoTicket = await responseTicket.json();
        
        if (!resultadoTicket?.ok) {
            SafeUtils.showToast('Error al cargar información del ticket', 'error');
            return;
        }
        
        const ticket = resultadoTicket.data.find(t => t.id == ticketId);
        if (!ticket) {
            SafeUtils.showToast('Ticket no encontrado', 'error');
            return;
        }
        
        // Llenar información del ticket
        $('#venta_ticket_id_aprobar').val(ticket.id);
        $('#venta_sede_id_aprobar').val(userInfo.sede_id);
        
        let infoTicket = `
            <div class="row g-2">
                <div class="col-md-6"><strong>Código:</strong> <span class="badge bg-primary">${ticket.codigo_ticket}</span></div>
                <div class="col-md-6"><strong>Estado:</strong> ${obtenerBadgeEstado(ticket.estado)}</div>
                <div class="col-md-12"><strong>Cliente:</strong> ${ticket.nombres || ''} ${ticket.apellidos || ''}</div>
                <div class="col-md-6"><strong>Documento:</strong> ${ticket.tipo_documento || 'DNI'} ${ticket.numero_documento || ''}</div>
                <div class="col-md-6"><strong>Email:</strong> ${ticket.email || '-'}</div>
                <div class="col-md-12"><strong>Rifa:</strong> ${ticket.rifa_codigo || ''} - ${ticket.rifa_nombre || ''}</div>
        `;
        
        if (ticket.numero_boleto) {
            infoTicket += `
                <div class="col-md-12 mt-2">
                    <div class="alert alert-success mb-0 py-2">
                        <strong><i class="ri-number-1 me-1"></i>Números:</strong> 
                        <span class="badge bg-success fs-6">${ticket.numero_boleto}</span>
                    </div>
                </div>
            `;
        }
        
        infoTicket += `</div>`;
        $('#venta_info_ticket_aprobar').html(infoTicket);
        
        $('#venta_precio_aprobar').val(SafeUtils.formatCurrency(ticket.precio_pagado || 0));
        $('#venta_fecha_compra_aprobar').val(ticket.fecha_compra ? new Date(ticket.fecha_compra).toLocaleString('es-PE') : '-');
        
        // Mostrar mensaje de carga mientras se obtiene el comprobante
        $('#venta_preview_comprobante_aprobar').html('<p class="text-muted"><i class="ri-loader-4-line animate-spin me-1"></i>Cargando comprobante...</p>');
        
        // Cargar comprobante si existe
        try {
            const respuestaComprobantes = await fetch(`${window.API_BASE_URL}/tickets/getComprobantes?sede_id=${userInfo.sede_id}`, {
                headers: {
                    'Authorization': 'Bearer ' + Auth.getToken(),
                    'Content-Type': 'application/json'
                }
            });
            
            const resultadoComprobantes = await respuestaComprobantes.json();
            
            if (resultadoComprobantes?.ok && resultadoComprobantes.data?.length > 0) {
                const comprobante = resultadoComprobantes.data.find(c => c.ticket_id == ticketId);
                
                if (comprobante && comprobante.archivo_comprobante) {
                    // Construir URL correctamente
                    let archivoPath = comprobante.archivo_comprobante.trim();
                    let imageUrl = '';
                    
                    // Si ya es una URL completa (http/https), usarla directamente
                    if (archivoPath.startsWith('http://') || archivoPath.startsWith('https://')) {
                        imageUrl = archivoPath;
                    } else {
                        // Construir URL relativa
                        const baseUrl = (window.BASE_URL || '').replace(/\/$/, ''); // Remover barra final si existe
                        if (archivoPath.startsWith('/')) {
                            imageUrl = baseUrl + archivoPath;
                        } else {
                            imageUrl = baseUrl + '/' + archivoPath;
                        }
                    }
                    
                    // Verificar si es imagen o PDF
                    const esImagen = /\.(jpg|jpeg|png|gif|webp)$/i.test(archivoPath);
                    const esPDF = /\.pdf$/i.test(archivoPath);
                    
                    let previewHtml = '';
                    if (esImagen) {
                        previewHtml = `
                            <img src="${imageUrl}" class="img-fluid rounded border" style="max-height: 400px; width: auto;" alt="Comprobante" onerror="this.onerror=null; this.parentElement.innerHTML='<p class=\\'text-danger\\'>Error al cargar la imagen</p>'">
                            <br><a href="${imageUrl}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                <i class="ri-external-link-line"></i> Ver en nueva ventana
                            </a>
                        `;
                    } else if (esPDF) {
                        previewHtml = `
                            <div class="alert alert-info">
                                <i class="ri-file-pdf-line me-2"></i>Archivo PDF
                            </div>
                            <a href="${imageUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="ri-external-link-line"></i> Abrir PDF en nueva ventana
                            </a>
                        `;
                    } else {
                        previewHtml = `
                            <div class="alert alert-warning">
                                <i class="ri-file-line me-2"></i>Tipo de archivo no soportado para vista previa
                            </div>
                            <a href="${imageUrl}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="ri-external-link-line"></i> Ver archivo
                            </a>
                        `;
                    }
                    
                    $('#venta_preview_comprobante_aprobar').html(previewHtml);
                } else {
                    $('#venta_preview_comprobante_aprobar').html('<p class="text-muted">No hay comprobante disponible</p>');
                }
            } else {
                $('#venta_preview_comprobante_aprobar').html('<p class="text-muted">No hay comprobante disponible</p>');
            }
        } catch (error) {
            console.error('Error al cargar comprobante:', error);
            $('#venta_preview_comprobante_aprobar').html('<p class="text-muted">Error al cargar comprobante</p>');
        }
        
        // Resetear formulario
        $('#venta_accion_aprobar').val('');
        $('#venta_contenedor_motivo_rechazo').addClass('d-none');
        $('#venta_motivo_rechazo').val('');
        
        modalAprobarVenta.show();
    } catch (error) {
        SafeUtils.showToast('Error al cargar información del ticket', 'error');
        console.error(error);
    }
}

async function guardarAprobacionVenta() {
    const accion = $('#venta_accion_aprobar').val();
    if (!accion) {
        SafeUtils.showToast('Seleccione una acción', 'warning');
        $('#venta_accion_aprobar').addClass('is-invalid');
        return;
    }

    if (accion === 'RECHAZADO' && !$('#venta_motivo_rechazo').val().trim()) {
        SafeUtils.showToast('El motivo de rechazo es obligatorio', 'warning');
        $('#venta_motivo_rechazo').addClass('is-invalid');
        return;
    }

    const confirmar = await Swal.fire({
        title: accion === 'APROBADO' ? 'Aprobar pago' : 'Rechazar pago',
        text: accion === 'APROBADO' 
            ? '¿Está seguro de aprobar este pago? El ticket pasará a estado APROBADO y podrá participar en el sorteo.'
            : '¿Está seguro de rechazar este pago? El número será liberado.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar'
    });

    if (!confirmar.isConfirmed) return;

    // Obtener botón y guardar HTML original
    const $btnGuardar = $('#btn_guardar_aprobacion_venta');
    const originalBtnHtml = $btnGuardar.html();
    
    // Deshabilitar botón y mostrar spinner
    $btnGuardar.prop('disabled', true).html('<i class="ri-loader-4-line animate-spin me-1"></i>Procesando...');

    try {
        const ticketId = parseInt($('#venta_ticket_id_aprobar').val());
        
        // Buscar el comprobante asociado al ticket
        const respuestaComprobantes = await fetch(`${window.API_BASE_URL}/tickets/getComprobantes?sede_id=${userInfo.sede_id}`, {
            headers: {
                'Authorization': 'Bearer ' + Auth.getToken(),
                'Content-Type': 'application/json'
            }
        });
        
        const resultadoComprobantes = await respuestaComprobantes.json();
        
        if (!resultadoComprobantes?.ok) {
            // Restaurar botón
            $btnGuardar.prop('disabled', false).html(originalBtnHtml);
            SafeUtils.showToast('Error al buscar comprobante', 'error');
            return;
        }
        
        const comprobante = resultadoComprobantes.data?.find(c => c.ticket_id == ticketId);
        
        if (!comprobante) {
            // Restaurar botón
            $btnGuardar.prop('disabled', false).html(originalBtnHtml);
            SafeUtils.showToast('No se encontró comprobante asociado a este ticket', 'warning');
            return;
        }
        
        // Validar el comprobante
        const payload = {
            comprobante_id: comprobante.id,
            sede_id: userInfo.sede_id,
            estado: accion,
            validado_por: userInfo.nombre_completo || userInfo.username || 'SYSTEM',
            motivo_rechazo: accion === 'RECHAZADO' ? $('#venta_motivo_rechazo').val().trim() : null
        };

        const respuesta = await fetch(`${window.API_BASE_URL}/tickets/validarComprobante`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + Auth.getToken()
            },
            body: JSON.stringify(payload)
        });
        
        const resultado = await respuesta.json();
        
        // Restaurar botón
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);

        if (resultado?.ok) {
            SafeUtils.showToast(resultado.msj || 'Validación realizada correctamente', 'success');
            modalAprobarVenta.hide();
            
            // Recargar tabla de ventas
            if (tablaVentasRealizadas) {
                tablaVentasRealizadas.ajax.reload();
            }
        } else {
            SafeUtils.showToast(resultado?.msj || 'Error al validar el pago', 'error');
        }
    } catch (error) {
        // Restaurar botón en caso de error
        $btnGuardar.prop('disabled', false).html(originalBtnHtml);
            SafeUtils.showToast('Error al validar el pago', 'error');
        console.error(error);
    }
}
