/**
 * Landing.js - Gestión de Rifas en Landing Page
 * Consume API pública de rifas y renderiza dinámicamente
 * Utiliza métodos de Utils.js para formateo y utilidades
 */

const LandingRifas = {
    API_BASE_URL: window.API_BASE_URL || (window.location.origin + '/sistema_rifas/api'),
    rifas: [],
    rifaSeleccionada: null,
    numerosSeleccionados: [],

    /**
     * Inicializar landing page
     */
    async init() {
        await this.cargarRifasPublicas();
        this.inicializarEventos();
        this.inicializarContadores();
    },

    /**
     * Cargar rifas públicas desde la API
     */
    async cargarRifasPublicas() {
        try {
            console.log('Cargando rifas públicas desde:', `${this.API_BASE_URL}/rifas/publicas`);
            const response = await fetch(`${this.API_BASE_URL}/rifas/publicas`);
            const resultado = await response.json();

            console.log('Respuesta de la API:', resultado);

            if (resultado.ok && resultado.data && resultado.data.length > 0) {
                this.rifas = resultado.data;
                console.log(`Se encontraron ${resultado.data.length} rifas públicas`);
                this.renderizarRifas();
            } else {
                console.warn('No hay rifas públicas disponibles:', resultado.msj);
                this.mostrarMensajeSinRifas();
            }
        } catch (error) {
            console.error('Error al cargar rifas públicas:', error);
            this.mostrarErrorCarga();
        }
    },

    /**
     * Renderizar rifas en el contenedor
     */
    renderizarRifas() {
        const contenedor = document.getElementById('contenedor_rifas_publicas');
        if (!contenedor) {
            console.error('Contenedor de rifas no encontrado');
            return;
        }

        contenedor.innerHTML = '';

        this.rifas.forEach((rifa) => {
            const card = this.crearCardRifa(rifa);
            contenedor.appendChild(card);
        });

        // Reinicializar contadores después de renderizar
        setTimeout(() => {
            this.inicializarContadores();
        }, 100);
    },

    /**
     * Crear card HTML para una rifa
     */
    crearCardRifa(rifa) {
        const div = document.createElement('div');
        div.className = 'col-lg-4 col-md-6';
        
        const porcentajeVendido = rifa.total_numeros > 0 
            ? Math.round((rifa.numeros_vendidos / rifa.total_numeros) * 100) 
            : 0;
        
        const premiosHtml = this.generarHtmlPremios(rifa.premios || []);
        const fechaSorteo = this.formatearFecha(rifa.fecha_sorteo);
        const precioFormateado = window.Utils ? window.Utils.formatearMoneda(rifa.precio_ticket) : this.formatearMoneda(rifa.precio_ticket);

        div.innerHTML = `
            <div class="card ribbon-box border shadow-none">
                <div class="card-body">
                    <div class="ribbon ribbon-success ribbon-shape">Activa</div>
                    <div class="avatar-lg mx-auto mb-3">
                        <div class="avatar-title bg-primary text-white fs-1 rounded shadow-sm">
                            <i class="ri-ticket-2-line"></i>
                        </div>
                    </div>
                    <h5 class="text-center mb-1">${rifa.descripcion ? this.escapeHtml(rifa.descripcion) : this.escapeHtml(rifa.nombre)}</h5>
                    ${rifa.nombre && rifa.descripcion ? `<p class="text-muted text-center mb-2"><small>${this.escapeHtml(rifa.nombre)}</small></p>` : ''}
                    
                    ${premiosHtml}
                    
                    <div class="row text-center mb-3">
                        <div class="col-6">
                            <p class="text-muted mb-1">Precio ticket</p>
                            <h5 class="mb-0 text-success">${precioFormateado}</h5>
                        </div>
                        <div class="col-6">
                            <p class="text-muted mb-1">Tickets disponibles</p>
                            <h5 class="mb-0 text-primary">${rifa.numeros_disponibles || 0}/${rifa.total_numeros || 0}</h5>
                        </div>
                    </div>
                    <p class="text-muted text-center mb-2"><i class="ri-calendar-line"></i> Sorteo: ${fechaSorteo}</p>
                    
                    ${rifa.mostrar_contador == 1 ? this.generarContadorRegresivo(rifa.fecha_sorteo, rifa.id) : ''}
                    
                    <div class="progress mb-3" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: ${porcentajeVendido}%" 
                             aria-valuenow="${porcentajeVendido}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-success w-100 btn-comprar-ticket" 
                                data-rifa-id="${rifa.id}"
                                data-rifa-nombre="${this.escapeHtml(rifa.nombre)}"
                                data-rifa-precio="${rifa.precio_ticket}"
                                data-rifa-disponibles="${rifa.numeros_disponibles || 0}"
                                data-rifa-total="${rifa.total_numeros || 0}">
                            <i class="ri-shopping-cart-line me-1"></i> Comprar Tickets
                        </button>
                        <button class="btn btn-outline-primary w-100 btn-ver-premios" 
                                data-rifa-id="${rifa.id}"
                                data-rifa-nombre="${this.escapeHtml(rifa.nombre)}"
                                data-rifa-premios='${JSON.stringify(rifa.premios || [])}'>
                            <i class="ri-image-line me-1"></i> Ver Premios
                        </button>
                    </div>
                </div>
            </div>
        `;

        return div;
    },

    /**
     * Generar HTML de premios
     */
    generarHtmlPremios(premios) {
        if (!premios || premios.length === 0) {
            return '<div class="mb-3"><div class="text-center mb-2"><span class="badge bg-info-subtle text-info"><i class="ri-gift-line me-1"></i> Sin premios</span></div></div>';
        }

        // Ordenar premios: primero por es_principal DESC, luego por orden ASC, luego por id ASC
        const premiosOrdenados = [...premios].sort((a, b) => {
            if (a.es_principal !== b.es_principal) {
                return (b.es_principal || 0) - (a.es_principal || 0);
            }
            const ordenA = a.orden || 999;
            const ordenB = b.orden || 999;
            if (ordenA !== ordenB) {
                return ordenA - ordenB;
            }
            return (a.id || 0) - (b.id || 0);
        });

        const totalPremios = premiosOrdenados.length;
        let html = `
            <div class="mb-3">
                <div class="text-center mb-2">
                    <span class="badge bg-info-subtle text-info">
                        <i class="ri-gift-line me-1"></i> ${totalPremios} Premio${totalPremios > 1 ? 's' : ''}
                    </span>
                </div>
                <div class="text-start px-3">
        `;

        premiosOrdenados.forEach((premio, index) => {
            const posicion = index + 1;
            const badgeClass = posicion === 1 ? 'bg-warning text-white' : 'bg-secondary text-white';
            // Mostrar nombre del premio tal cual está registrado
            const nombrePremio = premio.premio_nombre || 'Premio';
            html += `
                <small class="text-dark d-block ${index < premiosOrdenados.length - 1 ? 'mb-1' : ''}">
                    <span class="badge ${badgeClass} me-1">${posicion}°</span>${this.escapeHtml(nombrePremio)}
                </small>
            `;
        });

        html += '</div></div>';
        return html;
    },

    /**
     * Generar contador regresivo
     */
    generarContadorRegresivo(fechaSorteo, rifaId) {
        const fechaISO = fechaSorteo.replace(' ', 'T');
        return `
            <div class="card bg-warning-subtle border-0 mb-3">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-center gap-2 countdown-rifa" data-fecha="${fechaISO}" data-rifa-id="${rifaId}">
                        <div class="text-center">
                            <div class="fw-bold text-warning countdown-days">00</div>
                            <small class="text-muted" style="font-size: 0.7rem;">días</small>
                        </div>
                        <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                        <div class="text-center">
                            <div class="fw-bold text-warning countdown-hours">00</div>
                            <small class="text-muted" style="font-size: 0.7rem;">hrs</small>
                        </div>
                        <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                        <div class="text-center">
                            <div class="fw-bold text-warning countdown-minutes">00</div>
                            <small class="text-muted" style="font-size: 0.7rem;">min</small>
                        </div>
                        <div class="text-center px-1"><div class="fw-bold text-warning">:</div></div>
                        <div class="text-center">
                            <div class="fw-bold text-warning countdown-seconds">00</div>
                            <small class="text-muted" style="font-size: 0.7rem;">seg</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    },

    /**
     * Inicializar eventos de los botones
     */
    inicializarEventos() {
        // Delegación de eventos para botones dinámicos
        document.addEventListener('click', (e) => {
            if (e.target.closest('.btn-comprar-ticket')) {
                const btn = e.target.closest('.btn-comprar-ticket');
                const rifaId = btn.dataset.rifaId;
                const rifa = this.rifas.find(r => r.id == rifaId);
                if (rifa) {
                    this.abrirModalComprar(rifa);
                }
            }

            if (e.target.closest('.btn-ver-premios')) {
                const btn = e.target.closest('.btn-ver-premios');
                const rifaId = btn.dataset.rifaId;
                const rifa = this.rifas.find(r => r.id == rifaId);
                if (rifa) {
                    this.abrirModalPremios(rifa);
                }
            }
        });
    },

    /**
     * Abrir modal de compra
     */
    abrirModalComprar(rifa) {
        // Verificar si existe el modal de compra
        const modal = document.getElementById('modal_comprar_ticket');
        if (modal) {
            // Llenar datos del modal con la rifa seleccionada
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
            
            // Actualizar campos del modal con los datos de la rifa
            const rifaNombreEl = document.getElementById('rifa_nombre_modal');
            const rifaPrecioEl = document.getElementById('rifa_precio_modal');
            
            if (rifaNombreEl) rifaNombreEl.textContent = rifa.nombre;
            if (rifaPrecioEl) {
                const precioFormateado = window.Utils ? window.Utils.formatearMoneda(rifa.precio_ticket) : this.formatearMoneda(rifa.precio_ticket);
                rifaPrecioEl.textContent = precioFormateado;
            }
            
            // Guardar rifa seleccionada para uso posterior
            this.rifaSeleccionada = rifa;
        } else {
            console.warn('Modal de compra no encontrado');
            if (window.Utils && window.Utils.showToast) {
                window.Utils.showToast('Modal de compra no disponible', 'warning');
            }
        }
    },

    /**
     * Abrir modal de premios
     */
    abrirModalPremios(rifa) {
        // Verificar si existe el modal de premios
        const modal = document.getElementById('modal_ver_premios');
        if (modal) {
            const modalInstance = new bootstrap.Modal(modal);
            modalInstance.show();
            
            // Actualizar contenido del modal con los premios
            const premiosContainer = modal.querySelector('.premios-container');
            if (premiosContainer && rifa.premios && rifa.premios.length > 0) {
                // Ordenar premios: primero por es_principal DESC, luego por orden ASC, luego por id ASC
                const premiosOrdenados = [...rifa.premios].sort((a, b) => {
                    if (a.es_principal !== b.es_principal) {
                        return (b.es_principal || 0) - (a.es_principal || 0);
                    }
                    const ordenA = a.orden || 999;
                    const ordenB = b.orden || 999;
                    if (ordenA !== ordenB) {
                        return ordenA - ordenB;
                    }
                    return (a.id || 0) - (b.id || 0);
                });

                let premiosHtml = '';
                premiosOrdenados.forEach((premio, index) => {
                    const posicion = index + 1;
                    // Mostrar nombre del premio tal cual está registrado
                    const nombrePremio = premio.premio_nombre || 'Premio';
                    premiosHtml += `
                        <div class="premio-item mb-3">
                            <h6><span class="badge bg-warning me-2">${posicion}°</span>${this.escapeHtml(nombrePremio)}</h6>
                            ${premio.premio_descripcion ? `<p class="text-muted"><small>${this.escapeHtml(premio.premio_descripcion.replace(/<[^>]*>/g, ''))}</small></p>` : ''}
                        </div>
                    `;
                });
                premiosContainer.innerHTML = premiosHtml;
            }
        } else {
            console.warn('Modal de premios no encontrado');
            if (window.Utils && window.Utils.showToast) {
                window.Utils.showToast('Modal de premios no disponible', 'warning');
            }
        }
    },

    /**
     * Inicializar contadores regresivos
     */
    inicializarContadores() {
        const contadores = document.querySelectorAll('.countdown-rifa');
        contadores.forEach(contador => {
            const fechaStr = contador.dataset.fecha;
            const rifaId = contador.dataset.rifaId;
            
            // Evitar inicializar múltiples veces el mismo contador
            if (contador.dataset.inicializado === 'true') {
                return;
            }
            
            if (fechaStr) {
                this.iniciarContador(contador, fechaStr);
                contador.dataset.inicializado = 'true';
            }
        });
    },

    /**
     * Iniciar contador regresivo
     */
    iniciarContador(elemento, fechaStr) {
        const fechaSorteo = new Date(fechaStr);
        
        const actualizar = () => {
            const ahora = new Date();
            const diferencia = fechaSorteo - ahora;

            if (diferencia <= 0) {
                elemento.querySelector('.countdown-days').textContent = '00';
                elemento.querySelector('.countdown-hours').textContent = '00';
                elemento.querySelector('.countdown-minutes').textContent = '00';
                elemento.querySelector('.countdown-seconds').textContent = '00';
                return;
            }

            const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
            const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

            elemento.querySelector('.countdown-days').textContent = String(dias).padStart(2, '0');
            elemento.querySelector('.countdown-hours').textContent = String(horas).padStart(2, '0');
            elemento.querySelector('.countdown-minutes').textContent = String(minutos).padStart(2, '0');
            elemento.querySelector('.countdown-seconds').textContent = String(segundos).padStart(2, '0');
        };

        actualizar();
        setInterval(actualizar, 1000);
    },

    /**
     * Mostrar mensaje cuando no hay rifas
     */
    mostrarMensajeSinRifas() {
        const contenedor = document.getElementById('contenedor_rifas_publicas');
        if (contenedor) {
            contenedor.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="ri-information-line fs-4 me-2"></i>
                        <strong>No hay rifas disponibles en este momento.</strong>
                        <p class="mb-0">Vuelve pronto para ver nuestras próximas rifas.</p>
                    </div>
                </div>
            `;
        }
    },

    /**
     * Mostrar error de carga
     */
    mostrarErrorCarga() {
        const contenedor = document.getElementById('contenedor_rifas_publicas');
        if (contenedor) {
            contenedor.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        <i class="ri-error-warning-line fs-4 me-2"></i>
                        <strong>Error al cargar las rifas.</strong>
                        <p class="mb-0">Por favor, intenta recargar la página.</p>
                    </div>
                </div>
            `;
        }
        
        // Mostrar toast si Utils está disponible
        if (window.Utils && window.Utils.showToast) {
            window.Utils.showToast('Error al cargar las rifas públicas', 'error');
        }
    },

    /**
     * Formatear fecha usando Utils si está disponible
     */
    formatearFecha(fechaStr) {
        if (!fechaStr) return '';
        
        // Intentar usar Utils.formatearFechaHora si está disponible
        if (window.Utils && window.Utils.formatearFechaHora) {
            return window.Utils.formatearFechaHora(fechaStr);
        }
        
        // Fallback manual
        try {
            const fecha = new Date(fechaStr.replace(' ', 'T'));
            return fecha.toLocaleDateString('es-PE', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        } catch (e) {
            return fechaStr;
        }
    },

    /**
     * Formatear moneda usando Utils si está disponible
     */
    formatearMoneda(valor) {
        if (window.Utils && window.Utils.formatearMoneda) {
            return window.Utils.formatearMoneda(valor);
        }
        
        // Fallback manual
        return new Intl.NumberFormat('es-PE', {
            style: 'currency',
            currency: 'PEN',
            minimumFractionDigits: 2
        }).format(valor);
    },

    /**
     * Escapar HTML usando Utils si está disponible
     */
    escapeHtml(texto) {
        if (!texto) return '';
        
        if (window.Utils && window.Utils.escapeHtml) {
            return window.Utils.escapeHtml(texto);
        }
        
        // Fallback manual
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(texto).replace(/[&<>"']/g, m => map[m]);
    }
};

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    LandingRifas.init();
});

// Exportar para uso global
window.LandingRifas = LandingRifas;

