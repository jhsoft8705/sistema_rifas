/**
 * Dashboard JavaScript
 * Manejo de carga de datos, gráficos y tablas del dashboard
 */

// Variables globales para los gráficos
let chartVentasTiempo = null;
let chartEstadoTickets = null;
let chartAvanceRifas = null;
let chartCanalesVenta = null;
let diasVentas = 30;

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

// Cargar dashboard completo
async function cargarDashboard() {
    try {
        const response = await API.get('dashboard/getDashboardCompleto');
        
        if (response.ok && response.data) {
            const data = response.data;
            
            // Cargar KPIs
            cargarKPIs(data);
            
            // Cargar gráficos
            cargarGraficos(data);
            
            // Cargar tablas
            cargarTablas(data);
        } else {
            console.error('Error al cargar dashboard:', response.msj);
            mostrarError('Error al cargar el dashboard');
        }
    } catch (error) {
        console.error('Error en cargarDashboard:', error);
        mostrarError('Error de conexión al cargar el dashboard');
    }
}

// Cargar KPIs
function cargarKPIs(data) {
    // Ventas & Tickets
    const kpisVentas = data.kpis_ventas_tickets?.data;
    if (kpisVentas) {
        document.getElementById('kpi_tickets_vendidos_hoy').textContent = kpisVentas.tickets_vendidos_hoy || 0;
        document.getElementById('kpi_ingresos_hoy').textContent = formatearMoneda(parseFloat(kpisVentas.ingresos_hoy || 0));
        document.getElementById('kpi_ingresos_mes').textContent = formatearMoneda(parseFloat(kpisVentas.ingresos_mes || 0));
        document.getElementById('kpi_ticket_promedio').textContent = formatearMoneda(parseFloat(kpisVentas.ticket_promedio || 0));
    }

    // Estado Operativo
    const kpisEstado = data.kpis_estado_operativo?.data;
    if (kpisEstado) {
        document.getElementById('kpi_tickets_pendientes').textContent = kpisEstado.tickets_pendientes_validacion || 0;
        document.getElementById('kpi_pagos_rechazados').textContent = kpisEstado.pagos_rechazados_hoy || 0;
        document.getElementById('kpi_tickets_expirar').textContent = kpisEstado.tickets_por_expirar || 0;
        document.getElementById('kpi_personas_unicas').textContent = kpisEstado.personas_unicas_participantes || 0;
    }

    // Rifas
    const kpisRifas = data.kpis_rifas?.data;
    if (kpisRifas) {
        document.getElementById('kpi_rifas_activas').textContent = kpisRifas.rifas_activas || 0;
        document.getElementById('kpi_rifa_mas_vendida').textContent = kpisRifas.rifa_mas_vendida || '-';
        document.getElementById('kpi_rifa_menor_avance').textContent = kpisRifas.rifa_menor_avance || '-';
    }
}

// Cargar gráficos
function cargarGraficos(data) {
    // Ventas en el tiempo
    if (data.ventas_tiempo?.data) {
        inicializarGraficoVentasTiempo(data.ventas_tiempo.data);
    }

    // Estado de tickets
    if (data.estado_tickets?.data) {
        inicializarGraficoEstadoTickets(data.estado_tickets.data);
    }

    // Avance de rifas
    if (data.avance_rifas?.data) {
        inicializarGraficoAvanceRifas(data.avance_rifas.data);
    }

    // Canales de venta
    if (data.canales_venta?.data) {
        inicializarGraficoCanalesVenta(data.canales_venta.data);
    }
}

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

// Cargar tablas
function cargarTablas(data) {
    // Últimos movimientos
    if (data.ultimos_movimientos?.data) {
        cargarTablaUltimosMovimientos(data.ultimos_movimientos.data);
    }

    // Últimos ganadores
    if (data.ultimos_ganadores?.data) {
        cargarTablaUltimosGanadores(data.ultimos_ganadores.data);
    }

    // Tickets aprobados
    if (data.tickets_aprobados?.data) {
        cargarTablaTicketsAprobados(data.tickets_aprobados.data);
    }
}

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

// Recargar ventas en el tiempo con diferentes días
async function cargarVentasTiempo(dias) {
    diasVentas = dias;
    try {
        const response = await API.get(`dashboard/getVentasTiempo?dias=${dias}`);
        if (response.ok && response.data) {
            inicializarGraficoVentasTiempo(response.data.data);
        }
    } catch (error) {
        console.error('Error al cargar ventas en el tiempo:', error);
    }
}

// Mostrar error
function mostrarError(mensaje) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: mensaje
        });
    } else {
        alert(mensaje);
    }
}

// Inicializar dashboard cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    // Verificar autenticación
    if (!Auth.isAuthenticated()) {
        window.location.href = window.BASE_URL;
        return;
    }

    // Cargar dashboard
    cargarDashboard();
});
