/**
 * Visualización de Números de Rifa
 * Página pública para ver números disponibles de una rifa
 */

let rifaData = null;
let numerosData = [];
let numerosFiltrados = [];
let rifaId = null;

$(document).ready(async () => {
    // Obtener ID encriptado de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const encryptedId = urlParams.get('id') || window.location.pathname.split('/').pop();
    
    if (!encryptedId) {
        mostrarError('No se proporcionó un ID válido');
        return;
    }

    // Desencriptar ID
    rifaId = Utils.decryptId(encryptedId);
    
    if (!rifaId) {
        mostrarError('ID inválido o corrupto');
        return;
    }

    // Cargar datos de la rifa y números
    await cargarDatosRifa();
    await cargarNumeros();
    inicializarEventos();
});

async function cargarDatosRifa() {
    try {
        const respuesta = await fetch(`${window.API_BASE_URL}/rifas/getById?id=${rifaId}&sede_id=1`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await respuesta.json();

        if (data.ok && data.data) {
            rifaData = data.data;
            mostrarInfoRifa();
        } else {
            mostrarError(data.msj || 'No se pudo obtener la información de la rifa');
        }
    } catch (error) {
        console.error('Error al cargar datos de la rifa:', error);
        mostrarError('Error al conectar con el servidor');
    }
}

async function cargarNumeros() {
    try {
        $('#loading_numeros').removeClass('d-none');
        $('#error_numeros').addClass('d-none');
        $('#grid_numeros').addClass('d-none');

        const respuesta = await fetch(`${window.API_BASE_URL}/rifas/numeros/get?rifa_id=${rifaId}&estado=`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await respuesta.json();

        if (data.ok && Array.isArray(data.data)) {
            numerosData = data.data;
            numerosFiltrados = [...numerosData];
            renderizarNumeros();
            actualizarEstadisticas();
        } else {
            mostrarError(data.msj || 'No se pudieron obtener los números');
        }
    } catch (error) {
        console.error('Error al cargar números:', error);
        mostrarError('Error al conectar con el servidor');
    } finally {
        $('#loading_numeros').addClass('d-none');
    }
}

function mostrarInfoRifa() {
    if (!rifaData) return;

    $('#rifa_nombre').text(rifaData.nombre || 'Rifa sin nombre');
    $('#rifa_descripcion').text(rifaData.descripcion || '');
    
    if (rifaData.precio_ticket) {
        const precio = parseFloat(rifaData.precio_ticket);
        $('#rifa_precio').text(`S/. ${precio.toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`);
    }
    
    if (rifaData.fecha_sorteo) {
        const fecha = new Date(rifaData.fecha_sorteo.replace(' ', 'T'));
        $('#rifa_fecha_sorteo').text(fecha.toLocaleDateString('es-PE', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        }));
    }
}

function renderizarNumeros() {
    const grid = $('#grid_numeros');
    grid.empty();

    if (numerosFiltrados.length === 0) {
        grid.html('<div class="col-12 text-center text-muted py-5"><i class="ri-inbox-line fs-1"></i><p class="mt-3">No se encontraron números</p></div>');
        grid.removeClass('d-none');
        return;
    }

    numerosFiltrados.forEach(numero => {
        const estado = numero.estado || 'DISPONIBLE';
        const estadoLower = estado.toLowerCase();
        const numeroFormateado = numero.numero_formateado || numero.numero_entero || '';
        
        const card = $(`
            <div class="numero-card ${estadoLower}" data-numero-id="${numero.id}" data-estado="${estado}" data-numero="${numero.numero_entero}">
                <span class="badge-estado badge bg-${obtenerColorBadge(estado)}">${estado}</span>
                <div class="numero-texto">${numeroFormateado}</div>
            </div>
        `);

        // Agregar tooltip con información adicional
        if (numero.nombres && numero.apellidos) {
            card.attr('title', `Participante: ${numero.nombres} ${numero.apellidos}`);
        }

        grid.append(card);
    });

    grid.removeClass('d-none');
}

function obtenerColorBadge(estado) {
    const map = {
        'DISPONIBLE': 'success',
        'RESERVADO': 'warning',
        'VENDIDO': 'primary',
        'BLOQUEADO': 'danger'
    };
    return map[estado] || 'secondary';
}

function actualizarEstadisticas() {
    const total = numerosData.length;
    const disponibles = numerosData.filter(n => n.estado === 'DISPONIBLE').length;
    const reservados = numerosData.filter(n => n.estado === 'RESERVADO').length;
    const vendidos = numerosData.filter(n => n.estado === 'VENDIDO').length;

    $('#stat_total').text(total);
    $('#stat_disponibles').text(disponibles);
    $('#stat_reservados').text(reservados);
    $('#stat_vendidos').text(vendidos);
}

function aplicarFiltros() {
    const estadoFiltro = $('#filtro_estado').val();
    const buscarTexto = $('#buscar_numero').val().trim().toLowerCase();

    numerosFiltrados = numerosData.filter(numero => {
        // Filtro por estado
        if (estadoFiltro && numero.estado !== estadoFiltro) {
            return false;
        }

        // Filtro por búsqueda de número
        if (buscarTexto) {
            const numeroTexto = (numero.numero_formateado || numero.numero_entero || '').toString().toLowerCase();
            const numeroEntero = (numero.numero_entero || '').toString();
            if (!numeroTexto.includes(buscarTexto) && !numeroEntero.includes(buscarTexto)) {
                return false;
            }
        }

        return true;
    });

    renderizarNumeros();
}

function limpiarFiltros() {
    $('#filtro_estado').val('');
    $('#buscar_numero').val('');
    numerosFiltrados = [...numerosData];
    renderizarNumeros();
}

function imprimirNumeros() {
    window.print();
}

function inicializarEventos() {
    $('#btn_aplicar_filtros').on('click', aplicarFiltros);
    $('#btn_limpiar_filtros').on('click', limpiarFiltros);
    $('#btn_imprimir').on('click', imprimirNumeros);
    
    $('#buscar_numero').on('keypress', function(e) {
        if (e.which === 13) {
            aplicarFiltros();
        }
    });

    // Click en número para mostrar información
    $(document).on('click', '.numero-card', function() {
        const numeroId = $(this).data('numero-id');
        const estado = $(this).data('estado');
        const numero = $(this).data('numero');
        
        if (estado === 'DISPONIBLE') {
            Utils.showToast(`Número ${numero} está disponible para compra`, 'info');
        } else if (estado === 'VENDIDO') {
            Utils.showToast(`Número ${numero} ya fue vendido`, 'info');
        } else if (estado === 'RESERVADO') {
            Utils.showToast(`Número ${numero} está reservado`, 'warning');
        } else {
            Utils.showToast(`Número ${numero} está bloqueado`, 'error');
        }
    });
}

function mostrarError(mensaje) {
    $('#loading_numeros').addClass('d-none');
    $('#grid_numeros').addClass('d-none');
    $('#error_numeros').removeClass('d-none');
    $('#error_mensaje').text(mensaje);
}

// Estilos para impresión
if (typeof window !== 'undefined') {
    const style = document.createElement('style');
    style.textContent = `
        @media print {
            .filtros-container, .btn, .info-rifa {
                display: none !important;
            }
            .numero-card {
                break-inside: avoid;
                page-break-inside: avoid;
            }
            .grid-numeros {
                grid-template-columns: repeat(5, 1fr) !important;
            }
        }
    `;
    document.head.appendChild(style);
}
