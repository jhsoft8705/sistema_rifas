/**
 * Imprimir en Publicidad - Números disponibles para A4 y redes sociales
 * Con paginación para elegir rango de números (evitar repetir los mismos).
 */

const SafeUtils = {
    showLoading(message = 'Procesando...') {
        if (window.Utils?.showLoading) Utils.showLoading(message);
    },
    closeLoading() {
        if (window.Utils?.closeLoading) Utils.closeLoading();
    },
    showToast(message, type = 'info') {
        if (window.Utils?.showToast) Utils.showToast(message, type);
        else console.log(message);
    },
    showAlert(message, type = 'info') {
        if (window.Utils?.showAlert) return Utils.showAlert(message, type);
        if (typeof Swal !== 'undefined') {
            return Swal.fire({ icon: type, title: type === 'error' ? 'Error' : 'Información', text: message });
        }
        alert(message);
        return Promise.resolve({ isConfirmed: true });
    }
};

let userInfo = null;
let rifaInfo = null;
let numerosDisponibles = [];
let paginaActualA4 = 1;
let paginaActualRedes = 1;

$(document).ready(async () => {
    if (!Auth.requireAuth()) return;
    userInfo = Auth.getUserInfo();

    const params = new URLSearchParams(window.location.search);
    const rifaId = params.get('rifa_id');

    if (!rifaId) {
        $('#bloque_sin_rifa').removeClass('d-none');
        return;
    }

    await cargarDatosPublicidad(parseInt(rifaId, 10));
});

async function cargarDatosPublicidad(rifaId) {
    SafeUtils.showLoading('Cargando números disponibles...');
    try {
        const [respRifa, respNumeros] = await Promise.all([
            API.get('rifas/getById', { id: rifaId, sede_id: userInfo.sede_id }),
            API.get('rifas/numeros/get', { rifa_id: rifaId, estado: 'DISPONIBLE' })
        ]);

        if (!respRifa?.ok || !respRifa.data) {
            SafeUtils.showAlert(respRifa?.msj || 'No se pudo cargar la rifa', 'error');
            $('#bloque_sin_rifa').removeClass('d-none');
            return;
        }

        rifaInfo = respRifa.data;
        numerosDisponibles = (respNumeros?.ok && Array.isArray(respNumeros.data)) ? respNumeros.data : [];

        $('#publicidad_rifa_nombre').text(rifaInfo.nombre || '—');
        $('#publicidad_total_numeros').text(numerosDisponibles.length);
        $('#preview_a4_titulo').text(rifaInfo.nombre || 'Números disponibles');
        $('#preview_redes_titulo').text(rifaInfo.nombre || 'Números disponibles');
        const desc = (rifaInfo.descripcion || '').trim();
        const $descEl = $('#preview_redes_descripcion');
        if (desc) {
            $descEl.text(desc.length > 120 ? desc.substring(0, 120) + '…' : desc).show();
        } else {
            $descEl.empty().hide();
        }
        $('#preview_redes_subtitulo').text('Elige tu número y participa');

        paginaActualA4 = 1;
        paginaActualRedes = 1;

        $('#bloque_con_rifa').removeClass('d-none');
        renderizarA4();
        renderizarRedes();
        actualizarPaginacionA4();
        actualizarPaginacionRedes();

        $('#a4_por_hoja').off('change').on('change', () => { paginaActualA4 = 1; renderizarA4(); actualizarPaginacionA4(); });
        $('#a4_prev_page').off('click').on('click', () => { if (paginaActualA4 > 1) { paginaActualA4--; renderizarA4(); actualizarPaginacionA4(); } });
        $('#a4_next_page').off('click').on('click', () => { const total = getTotalPagesA4(); if (paginaActualA4 < total) { paginaActualA4++; renderizarA4(); actualizarPaginacionA4(); } });
        $('#redes_por_imagen').off('change').on('change', () => { paginaActualRedes = 1; renderizarRedes(); actualizarPaginacionRedes(); });
        $('#redes_prev_page').off('click').on('click', () => { if (paginaActualRedes > 1) { paginaActualRedes--; renderizarRedes(); actualizarPaginacionRedes(); } });
        $('#redes_next_page').off('click').on('click', () => { const total = getTotalPagesRedes(); if (paginaActualRedes < total) { paginaActualRedes++; renderizarRedes(); actualizarPaginacionRedes(); } });
        $('#btn_exportar_pdf').off('click').on('click', exportarPdf);
        $('#btn_exportar_imagen').off('click').on('click', exportarImagen);
    } catch (e) {
        console.error('Error cargando publicidad:', e);
        SafeUtils.showAlert('Error al cargar los datos', 'error');
        $('#bloque_sin_rifa').removeClass('d-none');
    } finally {
        SafeUtils.closeLoading();
    }
}

function getPorHojaA4() { return parseInt($('#a4_por_hoja').val(), 10) || 10; }
function getPorImagenRedes() { return parseInt($('#redes_por_imagen').val(), 10) || 10; }
function getTotalPagesA4() {
    const porHoja = getPorHojaA4();
    return Math.max(1, Math.ceil(numerosDisponibles.length / porHoja));
}
function getTotalPagesRedes() {
    const porImagen = getPorImagenRedes();
    return Math.max(1, Math.ceil(numerosDisponibles.length / porImagen));
}

function getNumerosPaginaA4() {
    const porHoja = getPorHojaA4();
    const start = (paginaActualA4 - 1) * porHoja;
    return numerosDisponibles.slice(start, start + porHoja).map(n => n.numero_formateado || String(n.numero_entero));
}

function getNumerosPaginaRedes() {
    const porImagen = getPorImagenRedes();
    const start = (paginaActualRedes - 1) * porImagen;
    return numerosDisponibles.slice(start, start + porImagen).map(n => n.numero_formateado || String(n.numero_entero));
}

function actualizarPaginacionA4() {
    const total = getTotalPagesA4();
    $('#a4_pagina_info').text(`Página ${paginaActualA4} de ${total}`);
    $('#a4_prev_page').prop('disabled', paginaActualA4 <= 1);
    $('#a4_next_page').prop('disabled', paginaActualA4 >= total);
}

function actualizarPaginacionRedes() {
    const total = getTotalPagesRedes();
    $('#redes_pagina_info').text(`Página ${paginaActualRedes} de ${total}`);
    $('#redes_prev_page').prop('disabled', paginaActualRedes <= 1);
    $('#redes_next_page').prop('disabled', paginaActualRedes >= total);
}

function renderizarA4() {
    const $cont = $('#preview_a4_numeros');
    $cont.empty();
    const numeros = getNumerosPaginaA4();
    numeros.forEach(num => {
        $cont.append(`<span class="ticket-numero-publicidad">${escapeHtml(num)}</span>`);
    });
    if (numeros.length === 0) {
        $cont.html('<p class="text-muted mb-0">No hay números en esta página</p>');
    }
}

function renderizarRedes() {
    const $cont = $('#preview_redes_numeros');
    $cont.empty();
    const numeros = getNumerosPaginaRedes();
    numeros.forEach(num => {
        $cont.append(`<span class="ticket-numero-publicidad">${escapeHtml(num)}</span>`);
    });
    if (numeros.length === 0) {
        $cont.html('<p class="text-muted mb-0 small">No hay números en esta página</p>');
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Exportar PDF: usar el elemento visible #preview_a4 (solo la página actual).
 * Construimos un clon con estilos inline para que html2pdf lo capture bien.
 */
function exportarPdf() {
    const numeros = getNumerosPaginaA4();
    if (numeros.length === 0) {
        SafeUtils.showToast('No hay números en esta página para exportar', 'warning');
        return;
    }
    if (typeof html2pdf === 'undefined') {
        SafeUtils.showAlert('La librería de PDF no está cargada', 'error');
        return;
    }
    SafeUtils.showLoading('Generando PDF...');

    const titulo = rifaInfo.nombre || 'Números disponibles';
    const descripcion = (rifaInfo.descripcion || '').trim();
    const contenido = numeros.map(n => `<span style="display:inline-flex;align-items:center;justify-content:center;min-width:3rem;padding:0.5rem 0.75rem;margin:0.25rem;border:2px solid #DC143C;border-radius:0.5rem;font-weight:700;font-size:1rem;background:#fff;color:#1a1a1a;">${escapeHtml(n)}</span>`).join('');

    let bloqueDesc = '';
    if (descripcion) {
        const descEscapada = escapeHtml(descripcion.length > 200 ? descripcion.substring(0, 200) + '…' : descripcion);
        bloqueDesc = `<p style="text-align:center;font-size:0.9rem;color:#1a1a1a;margin-bottom:1rem;max-width:700px;margin-left:auto;margin-right:auto;line-height:1.4;">${descEscapada}</p>`;
    }

    const html = `
        <div id="pdf-export-container" style="width:794px;min-height:1123px;padding:40px;background:#fff;box-sizing:border-box;">
            <h6 style="text-align:center;margin-bottom:0.5rem;font-size:1.1rem;color:#DC143C;">${escapeHtml(titulo)}</h6>
            ${bloqueDesc}
            <p style="text-align:center;font-size:0.85rem;color:#6c757d;margin-bottom:1rem;">Página ${paginaActualA4} de ${getTotalPagesA4()}</p>
            <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:8px;">${contenido}</div>
        </div>
    `;

    const wrapper = document.createElement('div');
    wrapper.style.cssText = 'position:absolute;left:-9999px;top:0;width:794px;background:#fff;';
    wrapper.innerHTML = html;
    document.body.appendChild(wrapper);
    const element = wrapper.querySelector('#pdf-export-container');

    const opt = {
        margin: 10,
        filename: `publicidad_${rifaInfo.codigo || rifaInfo.id}_p${paginaActualA4}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };

    html2pdf().set(opt).from(element).save()
        .then(() => {
            document.body.removeChild(wrapper);
            SafeUtils.closeLoading();
            SafeUtils.showToast('PDF generado correctamente', 'success');
        })
        .catch(err => {
            document.body.removeChild(wrapper);
            SafeUtils.closeLoading();
            console.error(err);
            SafeUtils.showAlert('Error al generar el PDF', 'error');
        });
}

/**
 * Exportar imagen para redes: dibujar en canvas 1080x1980 (diseño atractivo).
 */
function exportarImagen() {
    const numeros = getNumerosPaginaRedes();
    if (numeros.length === 0) {
        SafeUtils.showToast('No hay números en esta página para exportar', 'warning');
        return;
    }

    SafeUtils.showLoading('Generando imagen...');

    // Colores del landing (landing-rifas.css): rojo, dorado, negro
    const ROJO = '#DC143C';
    const ROJO_OSCURO = '#B22222';
    const DORADO = '#FFD700';
    const DORADO_OSCURO = '#DAA520';
    const NEGRO = '#000000';
    const GRIS_OSCURO = '#1a1a1a';
    const GRIS_CLARO = '#f5f5f5';

    const W = 1080;
    const H = 1980;
    const canvas = document.createElement('canvas');
    canvas.width = W;
    canvas.height = H;
    const ctx = canvas.getContext('2d');

    // Fondo: degradado suave tipo hero (gris claro → blanco) con toque rojo/dorado
    const bgGrad = ctx.createLinearGradient(0, 0, W, H);
    bgGrad.addColorStop(0, '#f5f5f5');
    bgGrad.addColorStop(0.3, '#ffffff');
    bgGrad.addColorStop(0.7, '#fffbf8');
    bgGrad.addColorStop(1, '#f5f5f5');
    ctx.fillStyle = bgGrad;
    ctx.fillRect(0, 0, W, H);
    // Overlay sutil rojo/dorado (como hero-section)
    const overlay = ctx.createLinearGradient(0, 0, W, H);
    overlay.addColorStop(0, 'rgba(220, 20, 60, 0.04)');
    overlay.addColorStop(0.5, 'rgba(255, 215, 0, 0.03)');
    overlay.addColorStop(1, 'rgba(220, 20, 60, 0.04)');
    ctx.fillStyle = overlay;
    ctx.fillRect(0, 0, W, H);

    // Borde decorativo (rojo, estilo landing)
    ctx.strokeStyle = ROJO;
    ctx.lineWidth = 6;
    ctx.strokeRect(24, 24, W - 48, H - 48);
    ctx.strokeStyle = 'rgba(220, 20, 60, 0.2)';
    ctx.lineWidth = 2;
    ctx.strokeRect(32, 32, W - 64, H - 64);

    const titulo = (rifaInfo.nombre || 'Números disponibles').substring(0, 40);
    const descripcion = (rifaInfo.descripcion || '').trim();
    const subtitulo = 'Elige tu número y participa';
    const cta = '¡Reserva el tuyo! · Sorteo 100% transparente';

    // Título (nombre de la rifa) - rojo
    ctx.fillStyle = ROJO;
    ctx.font = 'bold 52px Arial, sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText(titulo, W / 2, 180);

    // Descripción de la rifa (hasta 3 líneas)
    let yActual = 250;
    if (descripcion) {
        const lineasDesc = wrapText(descripcion, 50, 3);
        ctx.fillStyle = GRIS_OSCURO;
        ctx.font = '30px Arial, sans-serif';
        lineasDesc.forEach((linea) => {
            ctx.fillText(linea, W / 2, yActual);
            yActual += 40;
        });
        yActual += 20;
    }

    // Subtítulo "Elige tu número y participa" - rojo
    ctx.fillStyle = ROJO_OSCURO;
    ctx.font = 'bold 36px Arial, sans-serif';
    ctx.fillText(subtitulo, W / 2, yActual + 20);
    yActual += 70;

    // Números en grid (2 columnas) - cajas estilo landing (borde rojo, fondo claro)
    const cols = 2;
    const boxW = 320;
    const boxH = 100;
    const gap = 40;
    const startX = (W - (cols * boxW + (cols - 1) * gap)) / 2 + boxW / 2 + gap / 2;
    const startY = yActual + 40;
    const rowH = boxH + 24;

    numeros.forEach((num, i) => {
        const col = i % cols;
        const row = Math.floor(i / cols);
        const x = startX + col * (boxW + gap);
        const y = startY + row * rowH;

        // Caja: fondo blanco/gris claro, borde rojo (ribbon-box style)
        ctx.fillStyle = '#ffffff';
        ctx.strokeStyle = ROJO;
        ctx.lineWidth = 3;
        roundRect(ctx, x - boxW / 2, y - boxH / 2, boxW, boxH, 12);
        ctx.fill();
        ctx.stroke();
        // Pequeña franja dorada tipo ribbon en la esquina
        ctx.fillStyle = DORADO;
        ctx.beginPath();
        ctx.moveTo(x - boxW / 2 + 12, y - boxH / 2);
        ctx.lineTo(x - boxW / 2 + 50, y - boxH / 2);
        ctx.lineTo(x - boxW / 2 + 38, y - boxH / 2 + 12);
        ctx.closePath();
        ctx.fill();

        ctx.fillStyle = GRIS_OSCURO;
        ctx.font = 'bold 42px Arial, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText(String(num), x, y);
    });

    // CTA abajo - dorado (estilo landing)
    ctx.fillStyle = DORADO_OSCURO;
    ctx.font = 'bold 32px Arial, sans-serif';
    ctx.fillText(cta, W / 2, H - 120);

    // Marca de agua
    ctx.fillStyle = 'rgba(26, 26, 26, 0.5)';
    ctx.font = '24px Arial, sans-serif';
    ctx.fillText('Sistema de Rifas', W / 2, H - 60);

    function wrapText(texto, maxCharsPerLine, maxLines) {
        const limpio = String(texto).replace(/\s+/g, ' ').trim();
        if (!limpio) return [];
        const palabras = limpio.split(' ');
        const lineas = [];
        let linea = '';
        for (const p of palabras) {
            const prueba = linea ? linea + ' ' + p : p;
            if (prueba.length <= maxCharsPerLine) {
                linea = prueba;
            } else {
                if (linea) lineas.push(linea);
                linea = p.length > maxCharsPerLine ? p.substring(0, maxCharsPerLine) : p;
            }
            if (lineas.length >= maxLines) break;
        }
        if (linea && lineas.length < maxLines) lineas.push(linea);
        return lineas;
    }

    function roundRect(ctx, x, y, w, h, r) {
        ctx.beginPath();
        ctx.moveTo(x + r, y);
        ctx.lineTo(x + w - r, y);
        ctx.quadraticCurveTo(x + w, y, x + w, y + r);
        ctx.lineTo(x + w, y + h - r);
        ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
        ctx.lineTo(x + r, y + h);
        ctx.quadraticCurveTo(x, y + h, x, y + h - r);
        ctx.lineTo(x, y + r);
        ctx.quadraticCurveTo(x, y, x + r, y);
        ctx.closePath();
    }

    const link = document.createElement('a');
    link.download = `publicidad_${rifaInfo.codigo || rifaInfo.id}_redes_p${paginaActualRedes}.png`;
    link.href = canvas.toDataURL('image/png');
    link.click();

    SafeUtils.closeLoading();
    SafeUtils.showToast('Imagen descargada correctamente', 'success');
}
