/**
 * Dashboard JavaScript
 * CARGA EN CASCADA (de arriba hacia abajo):
 * Cada sección se carga secuencialmente y se muestra al instante.
 * El usuario ve el contenido aparecer progresivamente sin experimentar lentitud.
 *
 * Endpoints optimizados:
 * 1. getKPIsCompletos - Ventas+Tickets + Estado Operativo + Rifas (1 request)
 * 2. getGraficosVentasEstado - Ventas en tiempo + Estado tickets (1 request)
 * 3. getAvanceRifas, getCanalesVenta, getUltimosMovimientos, getUltimosGanadores, getTicketsAprobados
 */

// Variables globales para los gráficos
let chartVentasTiempo = null;
let chartEstadoTickets = null;
let chartAvanceRifas = null;
let chartCanalesVenta = null;
let diasVentas = 30;
let userInfo = null;

$(document).ready(function () {
    if (!Auth.requireAuth()) {
        return;
    }

    userInfo = Auth.getUserInfo();

    // Iniciar carga en cascada (de arriba hacia abajo)
    cargarDashboardEnCascada();
});

// ==========================================================
// CARGA EN CASCADA - Secuencial de arriba hacia abajo
// ==========================================================
async function cargarDashboardEnCascada() {
    try {
        // ----- FASE 1: KPIs completos (1 request) -----
        const kpisCompletos = await API.get('dashboard/getKPIsCompletos');
        if (kpisCompletos && kpisCompletos.ok && kpisCompletos.data) {
            const d = kpisCompletos.data;
            actualizarKPIVentasTickets({ ok: true, data: d.ventas_tickets });
            actualizarKPIEstadoOperativo({ ok: true, data: d.estado_operativo });
            actualizarKPIRifas({ ok: true, data: d.rifas });
        } else {
            // Si falla, mostrar valores por defecto en lugar del spinner
            actualizarKPIVentasTickets({ ok: true, data: {} });
            actualizarKPIEstadoOperativo({ ok: true, data: {} });
            actualizarKPIRifas({ ok: true, data: {} });
        }
        ocultarBannerCarga();

        // ----- FASE 2: Ventas en tiempo + Estado tickets (1 request) -----
        const graficosVentasEstado = await API.get(`dashboard/getGraficosVentasEstado?dias=${diasVentas}`);
        if (graficosVentasEstado && graficosVentasEstado.ok && graficosVentasEstado.data) {
            const d = graficosVentasEstado.data;
            if (d.ventas_tiempo && d.ventas_tiempo.length) {
                inicializarGraficoVentasTiempo(d.ventas_tiempo);
            }
            if (d.estado_tickets && d.estado_tickets.length) {
                inicializarGraficoEstadoTickets(d.estado_tickets);
            }
        }

        // ----- FASE 3: Avance de rifas -----
        const avanceRifas = await API.get('dashboard/getAvanceRifas');
        if (avanceRifas && avanceRifas.ok && avanceRifas.data) {
            inicializarGraficoAvanceRifas(avanceRifas.data);
        }

        // ----- FASE 4: Canales de venta -----
        const canalesVenta = await API.get('dashboard/getCanalesVenta');
        if (canalesVenta && canalesVenta.ok && canalesVenta.data) {
            inicializarGraficoCanalesVenta(canalesVenta.data);
        }

        // ----- FASE 5: Últimos movimientos -----
        const ultimosMovimientos = await API.get('dashboard/getUltimosMovimientos');
        if (ultimosMovimientos && ultimosMovimientos.ok && ultimosMovimientos.data) {
            cargarTablaUltimosMovimientos(ultimosMovimientos.data);
        }

        // ----- FASE 6: Últimos ganadores -----
        const ultimosGanadores = await API.get('dashboard/getUltimosGanadores?limite=10');
        if (ultimosGanadores && ultimosGanadores.ok && ultimosGanadores.data) {
            cargarTablaUltimosGanadores(ultimosGanadores.data);
        }

        // ----- FASE 7: Tickets aprobados -----
        const ticketsAprobados = await API.get('dashboard/getTicketsAprobados?limite=50');
        if (ticketsAprobados && ticketsAprobados.ok && ticketsAprobados.data) {
            cargarTablaTicketsAprobados(ticketsAprobados.data);
        }
    } catch (error) {
        console.error('Error al cargar dashboard:', error);
        ocultarBannerCarga();
        mostrarError('Error al cargar el dashboard');
    }
}

function actualizarKPIVentasTickets(response) {
    const kpis = (response && response.ok && response.data) ? response.data : {};
    const setKpi = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    };
    setKpi('kpi_tickets_vendidos_hoy', kpis.tickets_vendidos_hoy ?? 0);
    setKpi('kpi_ingresos_hoy', formatearMoneda(parseFloat(kpis.ingresos_hoy || 0)));
    setKpi('kpi_ingresos_mes', formatearMoneda(parseFloat(kpis.ingresos_mes || 0)));
    setKpi('kpi_ticket_promedio', formatearMoneda(parseFloat(kpis.ticket_promedio || 0)));
}

function actualizarKPIEstadoOperativo(response) {
    const kpis = (response && response.ok && response.data) ? response.data : {};
    const setKpi = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    };
    setKpi('kpi_tickets_pendientes', kpis.tickets_pendientes_validacion ?? 0);
    setKpi('kpi_pagos_rechazados', kpis.pagos_rechazados_hoy ?? 0);
    setKpi('kpi_tickets_expirar', kpis.tickets_por_expirar ?? 0);
    setKpi('kpi_personas_unicas', kpis.personas_unicas_participantes ?? 0);
}

function actualizarKPIRifas(response) {
    const kpis = (response && response.ok && response.data) ? response.data : {};
    const setKpi = (id, val) => {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    };
    setKpi('kpi_rifas_activas', kpis.rifas_activas ?? 0);
    setKpi('kpi_rifa_mas_vendida', kpis.rifa_mas_vendida || '-');
    setKpi('kpi_rifa_menor_avance', kpis.rifa_menor_avance || '-');
}


// ==========================================================
// FUNCIONES DE UTILIDAD
// ==========================================================

// Función para formatear moneda
function formatearMoneda(valor) {
    return new Intl.NumberFormat('es-PE', {
        style: 'currency',
        currency: 'PEN',
        minimumFractionDigits: 2
    }).format(valor);
}

// Función para formatear fecha
function formatearFecha(fecha) {
    if (!fecha) return '-';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-PE', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Función para obtener colores del CSS
function getChartColorsArray(chartId) {
    const element = document.getElementById(chartId);
    if (!element) return null;
    
    const colors = element.getAttribute("data-colors");
    if (!colors) return null;
    
    try {
        const colorsArray = JSON.parse(colors);
        return colorsArray.map(function(color) {
            const trimmedColor = color.replace(" ", "");
            if (trimmedColor.indexOf(",") === -1) {
                const cssColor = getComputedStyle(document.documentElement).getPropertyValue(trimmedColor);
                return cssColor || trimmedColor;
            }
            const colorParts = trimmedColor.split(",");
            if (colorParts.length === 2) {
                return "rgba(" + getComputedStyle(document.documentElement).getPropertyValue(colorParts[0]) + "," + colorParts[1] + ")";
            }
            return trimmedColor;
        });
    } catch (e) {
        console.error('Error parsing chart colors:', e);
        return null;
    }
}

// ==========================================================
// INICIALIZACIÓN DE GRÁFICOS
// ==========================================================

// Inicializar gráfico de ventas en el tiempo
function inicializarGraficoVentasTiempo(datos) {
    const colors = getChartColorsArray("chart_ventas_tiempo");
    if (!colors) return;

    const fechas = datos.map(d => d.fecha);
    const ingresos = datos.map(d => parseFloat(d.ingresos || 0));
    const tickets = datos.map(d => parseInt(d.tickets_aprobados || 0));

    const options = {
        series: [
            {
                name: 'Ingresos',
                type: 'line',
                data: ingresos
            },
            {
                name: 'Tickets',
                type: 'column',
                data: tickets
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            toolbar: {
                show: true
            }
        },
        stroke: {
            width: [3, 0],
            curve: 'smooth'
        },
        colors: colors,
        dataLabels: {
            enabled: false
        },
        legend: {
            position: 'top'
        },
        xaxis: {
            categories: fechas
        },
        yaxis: [
            {
                title: {
                    text: 'Ingresos (S/)'
                },
                labels: {
                    formatter: function(val) {
                        return formatearMoneda(val);
                    }
                }
            },
            {
                opposite: true,
                title: {
                    text: 'Tickets'
                }
            }
        ],
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(val, { seriesIndex }) {
                    if (seriesIndex === 0) {
                        return formatearMoneda(val);
                    }
                    return val;
                }
            }
        }
    };

    if (chartVentasTiempo) {
        chartVentasTiempo.updateOptions(options);
    } else {
        chartVentasTiempo = new ApexCharts(document.querySelector("#chart_ventas_tiempo"), options);
        chartVentasTiempo.render();
    }
}

// Inicializar gráfico de estado de tickets (Donut)
function inicializarGraficoEstadoTickets(datos) {
    const colors = getChartColorsArray("chart_estado_tickets");
    if (!colors) return;

    const labels = datos.map(d => d.estado);
    const valores = datos.map(d => parseInt(d.cantidad || 0));

    const options = {
        series: valores,
        chart: {
            height: 300,
            type: 'donut'
        },
        labels: labels,
        colors: colors,
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + "%";
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%'
                }
            }
        }
    };

    if (chartEstadoTickets) {
        chartEstadoTickets.updateSeries(valores);
    } else {
        chartEstadoTickets = new ApexCharts(document.querySelector("#chart_estado_tickets"), options);
        chartEstadoTickets.render();
    }
}

// Inicializar gráfico de avance de rifas (Bar)
function inicializarGraficoAvanceRifas(datos) {
    const colors = getChartColorsArray("chart_avance_rifas");
    if (!colors) return;

    const nombres = datos.map(d => d.nombre);
    const vendidos = datos.map(d => parseInt(d.vendidos || 0));
    const disponibles = datos.map(d => parseInt(d.total_disponible || 0));
    const porcentajes = datos.map(d => parseFloat(d.porcentaje_avance || 0));

    const options = {
        series: [
            {
                name: 'Vendidos',
                data: vendidos
            },
            {
                name: 'Disponibles',
                data: disponibles
            }
        ],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: true
            }
        },
        plotOptions: {
            bar: {
                horizontal: true,
                dataLabels: {
                    position: 'top'
                }
            }
        },
        colors: colors,
        dataLabels: {
            enabled: true,
            formatter: function(val, opts) {
                const idx = opts.dataPointIndex;
                return porcentajes[idx].toFixed(1) + '%';
            }
        },
        xaxis: {
            categories: nombres
        },
        legend: {
            position: 'top'
        },
        tooltip: {
            y: {
                formatter: function(val, opts) {
                    const idx = opts.dataPointIndex;
                    return val + ' (' + porcentajes[idx].toFixed(1) + '%)';
                }
            }
        }
    };

    if (chartAvanceRifas) {
        chartAvanceRifas.updateOptions(options);
    } else {
        chartAvanceRifas = new ApexCharts(document.querySelector("#chart_avance_rifas"), options);
        chartAvanceRifas.render();
    }
}

// Inicializar gráfico de canales de venta (Donut)
function inicializarGraficoCanalesVenta(datos) {
    const colors = getChartColorsArray("chart_canales_venta");
    if (!colors) return;

    const labels = datos.map(d => d.canal);
    const valores = datos.map(d => parseInt(d.cantidad || 0));

    const options = {
        series: valores,
        chart: {
            height: 300,
            type: 'donut'
        },
        labels: labels,
        colors: colors,
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true,
            formatter: function(val) {
                return val + "%";
            }
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '70%'
                }
            }
        }
    };

    if (chartCanalesVenta) {
        chartCanalesVenta.updateSeries(valores);
    } else {
        chartCanalesVenta = new ApexCharts(document.querySelector("#chart_canales_venta"), options);
        chartCanalesVenta.render();
    }
}

// ==========================================================
// CARGAR TABLAS
// ==========================================================

// Cargar tabla de últimos movimientos
function cargarTablaUltimosMovimientos(datos) {
    const tbody = document.getElementById('tbody_ultimos_movimientos');
    if (!tbody) return;

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No hay movimientos recientes</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(item => {
        const tipoBadge = {
            'TICKET_COMPRADO': '<span class="badge bg-primary">Compra</span>',
            'COMPROBANTE_SUBIDO': '<span class="badge bg-info">Comprobante</span>',
            'PAGO_RECHAZADO': '<span class="badge bg-danger">Rechazado</span>'
        }[item.tipo] || '<span class="badge bg-secondary">' + item.tipo + '</span>';

        const estadoBadge = {
            'APROBADO': '<span class="badge bg-success">Aprobado</span>',
            'PENDIENTE_PAGO': '<span class="badge bg-warning">Pendiente</span>',
            'PAGO_SUBIDO': '<span class="badge bg-info">Pago Subido</span>',
            'RECHAZADO': '<span class="badge bg-danger">Rechazado</span>',
            'VALIDANDO': '<span class="badge bg-warning">Validando</span>'
        }[item.estado] || '<span class="badge bg-secondary">' + item.estado + '</span>';

        return `
            <tr>
                <td>${tipoBadge}</td>
                <td>${item.codigo_ticket || '-'}</td>
                <td>${item.persona_nombre || '-'}</td>
                <td>${item.rifa_nombre || '-'}</td>
                <td>${formatearMoneda(parseFloat(item.precio_pagado || 0))}</td>
                <td>${estadoBadge}</td>
                <td>${formatearFecha(item.fecha)}</td>
            </tr>
        `;
    }).join('');
}

// Cargar tabla de últimos ganadores
function cargarTablaUltimosGanadores(datos) {
    const tbody = document.getElementById('tbody_ultimos_ganadores');
    if (!tbody) return;

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay ganadores registrados</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(item => {
        return `
            <tr>
                <td>${item.rifa_nombre || '-'}</td>
                <td>${item.premio_nombre || '-'}</td>
                <td>${item.persona_nombre || '-'}</td>
                <td><strong>${item.numero_ganador || '-'}</strong></td>
                <td>${formatearFecha(item.fecha)}</td>
            </tr>
        `;
    }).join('');
}

// Cargar tabla de tickets aprobados
function cargarTablaTicketsAprobados(datos) {
    const tbody = document.getElementById('tbody_tickets_aprobados');
    if (!tbody) return;

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay tickets aprobados</td></tr>';
        return;
    }

    tbody.innerHTML = datos.map(item => {
        const canalBadge = {
            'WEB': '<span class="badge bg-primary">Web</span>',
            'WHATSAPP': '<span class="badge bg-success">WhatsApp</span>',
            'FISICO': '<span class="badge bg-info">Físico</span>',
            'ADMINISTRATIVO': '<span class="badge bg-warning">Admin</span>'
        }[item.canal_venta] || '<span class="badge bg-secondary">' + (item.canal_venta || 'WEB') + '</span>';

        return `
            <tr>
                <td><strong>${item.codigo_ticket || '-'}</strong></td>
                <td>${item.persona_nombre || '-'}</td>
                <td>${item.rifa_nombre || '-'}</td>
                <td>${formatearMoneda(parseFloat(item.precio_pagado || 0))}</td>
                <td>${canalBadge}</td>
                <td>${formatearFecha(item.fecha_compra)}</td>
            </tr>
        `;
    }).join('');
}

// ==========================================================
// FUNCIONES ADICIONALES
// ==========================================================

// Recargar ventas en el tiempo con diferentes días (botones 7/30 días)
async function cargarVentasTiempo(dias) {
    diasVentas = dias;
    try {
        const response = await API.get(`dashboard/getGraficosVentasEstado?dias=${dias}`);
        if (response && response.ok && response.data) {
            const d = response.data;
            if (d.ventas_tiempo && d.ventas_tiempo.length) {
                inicializarGraficoVentasTiempo(d.ventas_tiempo);
            }
        }
    } catch (error) {
        console.error('Error al cargar ventas en el tiempo:', error);
    }
}

// Ocultar banner de carga cuando los KPIs estén listos
function ocultarBannerCarga() {
    const banner = document.getElementById('dashboard-loading-banner');
    if (banner) {
        banner.classList.add('fade');
        setTimeout(() => banner.remove(), 300);
    }
}

// Mostrar error
function mostrarError(mensaje) {
    if (typeof Utils !== 'undefined' && Utils.showAlert) {
        Utils.showAlert(mensaje, 'error');
    } else if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje
        });
    } else {
        alert(mensaje);
    }
}
