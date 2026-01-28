/**
 * Reporte Recaudación por Rifa
 * Filtros: rango de fechas, rifa. Tabla recaudación + ganadores. Descargar Excel.
 */

let userInfo = null;
let tablaGanadores = null;
let rifasData = [];
let reporteActual = { recaudacion: null, ganadores: [] };

$(document).ready(async function () {
    if (!Auth.requireAuth()) return;

    userInfo = Auth.getUserInfo();
    if (!userInfo?.sede_id) {
        Utils.showToast('No se pudo obtener la sede', 'error');
        return;
    }

    await cargarRifas();
    inicializarFechasPorDefecto();
    inicializarTablaGanadores();
    inicializarEventos();
});

function inicializarFechasPorDefecto() {
    const hoy = new Date();
    const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
    const ultimoDia = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
    $('#filtro_fecha_desde').val(primerDia.toISOString().slice(0, 10));
    $('#filtro_fecha_hasta').val(ultimoDia.toISOString().slice(0, 10));
}

async function cargarRifas() {
    try {
        const resp = await API.get('rifas/getAll', { sede_id: userInfo.sede_id });
        if (resp && resp.ok && resp.data) {
            rifasData = resp.data;
            const $sel = $('#filtro_rifa');
            $sel.find('option:not(:first)').remove();
            rifasData.forEach(r => {
                $sel.append(`<option value="${r.id}">${r.nombre || r.codigo} (${r.codigo || ''})</option>`);
            });
        }
    } catch (e) {
        console.error('Error cargando rifas:', e);
        Utils.showToast('Error al cargar rifas', 'error');
    }
}

function inicializarTablaGanadores() {
    tablaGanadores = $('#tabla_ganadores').DataTable({
        processing: false,
        serverSide: false,
        data: [],
        language: Utils.getDataTableLanguageES(),
        lengthChange: true,
        pageLength: 10,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="ri-file-excel-2-line me-1"></i> Excel',
                className: 'btn btn-success btn-sm',
                title: 'Reporte Ganadores',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5, 6]
                }
            }
        ],
        columns: [
            { data: null, render: (_, __, row, meta) => (meta.row + 1) },
            { data: 'nombre_completo', defaultContent: '-' },
            { data: 'documento_completo', defaultContent: '-' },
            { data: 'premio_titulo', defaultContent: '-', render: (d) => d || '-' },
            { data: 'numero_ganador', defaultContent: '-', render: (d) => d || '-' },
            {
                data: 'fecha_ganador',
                defaultContent: '-',
                render: (d) => d ? Utils.formatearFecha(d.toString().slice(0, 10)) : '-'
            },
            { data: 'tickets_ganadores', defaultContent: '-', render: (d) => d || '-' }
        ]
    });
}

function inicializarEventos() {
    $('#btn_filtrar_reporte').on('click', () => generarReporte());
    $('#btn_recargar_reporte').on('click', () => {
        inicializarFechasPorDefecto();
        $('#filtro_rifa').val('');
        $('#resumen_recaudacion').hide();
        tablaGanadores.clear().draw();
        reporteActual = { recaudacion: null, ganadores: [] };
    });
    $('#btn_excel_reporte').on('click', () => {
        if (reporteActual.ganadores && reporteActual.ganadores.length) {
            tablaGanadores.button('.buttons-excel').trigger();
        } else {
            Utils.showToast('No hay datos de ganadores para exportar. Aplique filtros primero.', 'warning');
        }
    });
}

async function generarReporte() {
    const rifaId = $('#filtro_rifa').val();
    const fechaDesde = $('#filtro_fecha_desde').val();
    const fechaHasta = $('#filtro_fecha_hasta').val();

    if (!rifaId) {
        Utils.showToast('Seleccione una rifa', 'warning');
        return;
    }
    if (!fechaDesde || !fechaHasta) {
        Utils.showToast('Indique rango de fechas', 'warning');
        return;
    }
    if (fechaDesde > fechaHasta) {
        Utils.showToast('La fecha desde no puede ser mayor que la fecha hasta', 'warning');
        return;
    }

    try {
        Utils.showLoading('Generando reporte...');

        const params = {
            sede_id: userInfo.sede_id,
            rifa_id: rifaId,
            fecha_desde: fechaDesde,
            fecha_hasta: fechaHasta
        };
        const resp = await API.get('reporte/getReporteRecaudacion', params);
        Utils.closeLoading();

        if (resp && resp.ok !== false) {
            reporteActual = {
                recaudacion: resp.recaudacion || null,
                ganadores: Array.isArray(resp.ganadores) ? resp.ganadores : []
            };

            if (reporteActual.recaudacion) {
                $('#resumen_recaudacion').show();
                const r = reporteActual.recaudacion;
                const simbolo = userInfo.simbolo_moneda || 'S/.';
                const fila = `
                    <tr>
                        <td>${r.rifa_nombre || '-'}</td>
                        <td class="text-end fw-semibold">${simbolo} ${parseFloat(r.total_recaudado || 0).toFixed(2)}</td>
                        <td class="text-center">${r.cantidad_tickets || 0}</td>
                        <td>${r.fecha_desde || ''} a ${r.fecha_hasta || ''}</td>
                    </tr>`;
                $('#tbody_resumen').html(fila);
            } else {
                $('#resumen_recaudacion').hide();
            }

            tablaGanadores.clear();
            if (reporteActual.ganadores.length) {
                tablaGanadores.rows.add(reporteActual.ganadores).draw();
            } else {
                tablaGanadores.draw();
            }
        } else {
            Utils.showToast(resp?.msj || 'Error al generar el reporte', 'error');
        }
    } catch (e) {
        Utils.closeLoading();
        console.error('Error reporte:', e);
        Utils.showToast('Error de conexión al generar el reporte', 'error');
    }
}
