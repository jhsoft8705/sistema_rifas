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
                                data-rifa-total="${rifa.total_numeros || 0}"
                                data-rifa-premios='${JSON.stringify(rifa.premios || [])}'>
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
            // Guardar rifa seleccionada globalmente para que el evento del modal la pueda usar
            this.rifaSeleccionada = rifa;
            window.rifaSeleccionada = rifa; // También en window para acceso global
            
            // Llenar datos del modal con la rifa seleccionada
            const modalInstance = new bootstrap.Modal(modal);
            
            // Disparar evento personalizado antes de mostrar para inicializar datos
            const initEvent = new CustomEvent('initModalRifa', { detail: rifa });
            modal.dispatchEvent(initEvent);
            
            modalInstance.show();
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

// Estilos CSS adicionales para el modal de compra
const style = document.createElement('style');
style.textContent = `
    /* Estilos para el modal de compra */
    #modal_comprar_ticket .modal-body {
        padding: 1.5rem;
    }
    
    #modal_comprar_ticket .nav-pills .nav-link {
        border-radius: 0.5rem;
        margin-right: 0.5rem;
        padding: 0.75rem 1.25rem;
    }
    
    #modal_comprar_ticket .nav-pills .nav-link.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    #modal_comprar_ticket .nav-pills .nav-link.done {
        background-color: rgba(64, 81, 137, 0.05);
        color: #405189;
    }
    
    #modal_comprar_ticket .form-control.border-danger {
        border-color: #dc3545 !important;
    }
    
    #modal_comprar_ticket .text-danger {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
    /* Animación de carga */
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    /* Estilos para números de boleto */
    .numero-btn {
        transition: all 0.2s ease;
    }
    
    .numero-btn:hover {
        transform: scale(1.05);
    }
    
    .numero-btn.numero-seleccionado {
        background-color: #198754 !important;
        border-color: #198754 !important;
        color: white !important;
    }
    
    /* Estilos para el resumen final */
    #resumen_numeros_boletos .badge {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
    }
`;
document.head.appendChild(style);

// ====================================================================
// SCRIPTS ADICIONALES DE INDEX.PHP - MOVIDOS AQUÍ PARA SEPARACIÓN
// ====================================================================

// Script para Contadores Regresivos
(function() {
    // Función para actualizar contador regresivo
    function actualizarContador(elemento, fechaObjetivo) {
        const ahora = new Date().getTime();
        const distancia = fechaObjetivo - ahora;

        if (distancia < 0) {
            // Si ya pasó la fecha
            const days = elemento.querySelector('.countdown-days');
            const hours = elemento.querySelector('.countdown-hours');
            const minutes = elemento.querySelector('.countdown-minutes');
            const seconds = elemento.querySelector('.countdown-seconds');
            
            if (days) days.textContent = '00';
            if (hours) hours.textContent = '00';
            if (minutes) minutes.textContent = '00';
            if (seconds) seconds.textContent = '00';
            return;
        }

        const dias = Math.floor(distancia / (1000 * 60 * 60 * 24));
        const horas = Math.floor((distancia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutos = Math.floor((distancia % (1000 * 60 * 60)) / (1000 * 60));
        const segundos = Math.floor((distancia % (1000 * 60)) / 1000);

        const days = elemento.querySelector('.countdown-days');
        const hours = elemento.querySelector('.countdown-hours');
        const minutes = elemento.querySelector('.countdown-minutes');
        const seconds = elemento.querySelector('.countdown-seconds');
        
        if (days) days.textContent = dias.toString().padStart(2, '0');
        if (hours) hours.textContent = horas.toString().padStart(2, '0');
        if (minutes) minutes.textContent = minutos.toString().padStart(2, '0');
        if (seconds) seconds.textContent = segundos.toString().padStart(2, '0');
    }

    // Inicializar todos los contadores
    document.addEventListener('DOMContentLoaded', function() {
        // Contador del Hero
        const heroCountdown = {
            days: document.getElementById('hero-days'),
            hours: document.getElementById('hero-hours'),
            minutes: document.getElementById('hero-minutes'),
            seconds: document.getElementById('hero-seconds'),
            fecha: new Date('2025-12-31T20:00:00').getTime()
        };

        // Actualizar contador del hero
        function actualizarHeroCountdown() {
            const ahora = new Date().getTime();
            const distancia = heroCountdown.fecha - ahora;

            if (distancia < 0) {
                heroCountdown.days.textContent = '00';
                heroCountdown.hours.textContent = '00';
                heroCountdown.minutes.textContent = '00';
                heroCountdown.seconds.textContent = '00';
                return;
            }

            const dias = Math.floor(distancia / (1000 * 60 * 60 * 24));
            const horas = Math.floor((distancia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutos = Math.floor((distancia % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((distancia % (1000 * 60)) / 1000);

            heroCountdown.days.textContent = dias.toString().padStart(2, '0');
            heroCountdown.hours.textContent = horas.toString().padStart(2, '0');
            heroCountdown.minutes.textContent = minutos.toString().padStart(2, '0');
            heroCountdown.seconds.textContent = segundos.toString().padStart(2, '0');
        }

        // Actualizar contadores de rifas
        const contadoresRifas = document.querySelectorAll('.countdown-rifa');
        contadoresRifas.forEach(contador => {
            const fechaStr = contador.getAttribute('data-fecha');
            const fechaObjetivo = new Date(fechaStr).getTime();
            
            // Actualizar inmediatamente
            actualizarContador(contador, fechaObjetivo);
            
            // Actualizar cada segundo
            setInterval(() => {
                actualizarContador(contador, fechaObjetivo);
            }, 1000);
        });

        // Actualizar hero countdown inmediatamente y cada segundo
        actualizarHeroCountdown();
        setInterval(actualizarHeroCountdown, 1000);
    });
})();

// Script para Modal de Compra de Tickets
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        let precioUnitario = 0;
        let ticketsDisponibles = 0;
        let rifaNombreGlobal = '';

        // ======= NAVEGACIÓN ENTRE TABS =======
        
        // Prevenir navegación directa a tabs (solo permitir navegación por botones)
        document.querySelectorAll('#modal_comprar_ticket .nav-link').forEach(tabButton => {
            // Prevenir clic en tabs deshabilitados
            tabButton.addEventListener('click', function(e) {
                if (this.disabled || this.hasAttribute('disabled')) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    return false;
                }
            });
            
            // Prevenir evento show.bs.tab en tabs deshabilitados
            tabButton.addEventListener('show.bs.tab', function(e) {
                if (this.disabled || this.hasAttribute('disabled')) {
                    e.preventDefault();
                    e.stopPropagation();
                    return false;
                }
            });
        });
        
        // Botones de "Siguiente Tab"
        document.querySelectorAll('.nexttab').forEach(button => {
            button.addEventListener('click', function() {
                const nextTabId = this.getAttribute('data-nexttab');
                const nextTabButton = document.getElementById(nextTabId);
                const currentTab = document.querySelector('#modal_comprar_ticket .nav-link.active');
                
                // Marcar el tab actual como completado
                if (currentTab && !currentTab.classList.contains('done')) {
                    currentTab.classList.add('done');
                }
                
                // Habilitar el siguiente tab antes de mostrarlo
                nextTabButton.disabled = false;
                nextTabButton.removeAttribute('disabled');
                
                const nextTab = new bootstrap.Tab(nextTabButton);
                nextTab.show();
                
                // Scroll al inicio del modal
                document.querySelector('#modal_comprar_ticket .modal-body').scrollTop = 0;
            });
        });

        // Botones de "Tab Anterior"
        document.querySelectorAll('.previestab').forEach(button => {
            button.addEventListener('click', function() {
                const prevTabId = this.getAttribute('data-previous');
                const prevTab = new bootstrap.Tab(document.getElementById(prevTabId));
                prevTab.show();
                
                // Scroll al inicio del modal
                document.querySelector('#modal_comprar_ticket .modal-body').scrollTop = 0;
            });
        });

        // Evento al abrir el modal
        const modalElement = document.getElementById('modal_comprar_ticket');
        
        // Función para inicializar el modal con datos de rifa
        function inicializarModalConRifa(rifaData) {
            if (!rifaData) {
                console.error('No se proporcionaron datos de la rifa');
                return;
            }
            
            const rifaId = rifaData.id || rifaData.rifaId;
            const rifaNombre = rifaData.nombre || rifaData.rifaNombre || '';
            const rifaPrecio = rifaData.precio_ticket || rifaData.precio || rifaData.rifaPrecio || '0';
            const rifaDisponibles = rifaData.numeros_disponibles || rifaData.disponibles || rifaData.rifaDisponibles || 0;
            const rifaTotal = rifaData.total_numeros || rifaData.total || rifaData.rifaTotal || 0;
            const rifaPremios = rifaData.premios || rifaData.rifaPremios || [];
            
            // Debug: Log de los valores obtenidos
            console.log('Inicializando modal con datos:', {
                rifaId,
                rifaNombre,
                rifaPrecio,
                rifaDisponibles,
                rifaTotal,
                rifaPremios
            });

            // Guardar nombre de rifa globalmente
            rifaNombreGlobal = rifaNombre;

            // Convertir valores numéricos de forma segura
            precioUnitario = parseFloat(rifaPrecio) || 0;
            ticketsDisponibles = parseInt(rifaDisponibles) || 0;
            window.ticketsDisponibles = ticketsDisponibles; // Hacer global para acceso desde validarTabOrden
            const ticketsTotal = parseInt(rifaTotal) || 0;
            
            console.log('Valores procesados:', {
                precioUnitario,
                ticketsDisponibles,
                ticketsTotal
            });

            // Actualizar el modal
            document.getElementById('modal_titulo_rifa').textContent = rifaNombre || 'Rifa';
            document.getElementById('rifa_id').value = rifaId || '';
            
            // Formatear precio con símbolo de moneda
            const precioFormateado = precioUnitario > 0 ? precioUnitario.toFixed(2) : '0.00';
            document.getElementById('precio_ticket').textContent = 'S/ ' + precioFormateado;
            document.getElementById('tickets_disponibles').textContent = ticketsDisponibles;
            document.getElementById('tickets_total').textContent = ticketsTotal;
            
            // Actualizar tabs duplicados
            document.getElementById('tickets_disponibles_tab').textContent = ticketsDisponibles;
            document.getElementById('tickets_total_tab').textContent = ticketsTotal;

            // Actualizar límite máximo de tickets (si hay disponibles, usar ese límite, sino permitir hasta 999)
            const maxTickets = ticketsDisponibles > 0 ? ticketsDisponibles : 999;
            document.getElementById('cantidad_tickets').setAttribute('max', maxTickets);

            // Mostrar lista de premios
            const listaPremios = document.getElementById('lista_premios');
            listaPremios.innerHTML = '';
            
            if (rifaPremios && rifaPremios.length > 0) {
                rifaPremios.forEach((premio, index) => {
                    const iconClass = index === 0 ? 'ri-trophy-fill text-warning' : 
                                     index === 1 ? 'ri-medal-line text-secondary' : 
                                     'ri-award-line text-dark';
                    const premioNombre = premio.premio_nombre || premio.nombre || 'Premio';
                    const premioPosicion = premio.posicion || (index + 1);
                    listaPremios.innerHTML += `
                        <div class="d-flex align-items-center gap-2 small">
                            <i class="${iconClass} fs-16"></i>
                            <span class="text-muted">${premioPosicion}°</span>
                            <strong>${premioNombre}</strong>
                        </div>
                    `;
                });
            } else {
                listaPremios.innerHTML = '<p class="text-muted small mb-0">No hay premios registrados</p>';
            }

            // Limpiar números seleccionados al abrir el modal
            window.numerosSeleccionados = [];
            document.getElementById('numeros_reservados').value = '';
            document.getElementById('numeros_formateados').value = '';
            const displayNumeros = document.getElementById('numero_seleccionado_display');
            if (displayNumeros) {
                displayNumeros.style.display = 'none';
            }
            
            // Resetear cantidad a 1
            document.getElementById('cantidad_tickets').value = '1';
            calcularTotal();
            
            // Validar tabs inicialmente
            setTimeout(() => {
                validarTabPersonal();
                validarTabOrden();
            }, 100);
            
            // Resetear navegación de tabs - Solo el primero habilitado
            const personalTab = document.getElementById('pills-personal-tab');
            const orderTab = document.getElementById('pills-order-tab');
            const paymentTab = document.getElementById('pills-payment-tab');
            const finishTab = document.getElementById('pills-finish-tab');
            
            personalTab.disabled = false;
            personalTab.removeAttribute('disabled');
            personalTab.classList.remove('done');
            
            orderTab.disabled = true;
            orderTab.setAttribute('disabled', 'disabled');
            orderTab.classList.remove('done');
            
            paymentTab.disabled = true;
            paymentTab.setAttribute('disabled', 'disabled');
            paymentTab.classList.remove('done');
            
            finishTab.disabled = true;
            finishTab.setAttribute('disabled', 'disabled');
            finishTab.classList.remove('done');
            
            // Volver al primer tab
            const firstTab = new bootstrap.Tab(personalTab);
            firstTab.show();
        }

        // Evento al abrir el modal (show.bs.modal)
        modalElement.addEventListener('show.bs.modal', function (event) {
            // Limpiar datos previos al abrir el modal
            window.numerosSeleccionados = [];
            document.getElementById('numeros_reservados').value = '';
            document.getElementById('numeros_formateados').value = '';
            document.getElementById('cantidad_tickets').value = '1';
            const displayNumeros = document.getElementById('numero_seleccionado_display');
            if (displayNumeros) {
                displayNumeros.style.display = 'none';
            }
            // Limpiar vista previa de comprobante
            const comprobanteInput = document.getElementById('comprobante_pago');
            if (comprobanteInput) {
                comprobanteInput.value = '';
            }
            const previewComprobante = document.getElementById('preview_comprobante');
            if (previewComprobante) {
                previewComprobante.style.display = 'none';
            }
            
            // Botón que disparó el modal (puede ser null si se abre programáticamente)
            const button = event.relatedTarget;
            let rifaData = null;
            
            // Si hay un botón, obtener datos de los data attributes
            if (button) {
                const rifaId = button.getAttribute('data-rifa-id');
                const rifaNombre = button.getAttribute('data-rifa-nombre');
                const rifaPrecio = button.getAttribute('data-rifa-precio');
                const rifaDisponibles = button.getAttribute('data-rifa-disponibles');
                const rifaTotal = button.getAttribute('data-rifa-total');
                const rifaPremiosAttr = button.getAttribute('data-rifa-premios');
                
                rifaData = {
                    id: rifaId,
                    nombre: rifaNombre,
                    precio_ticket: rifaPrecio,
                    numeros_disponibles: rifaDisponibles,
                    total_numeros: rifaTotal,
                    premios: rifaPremiosAttr ? (() => {
                        try {
                            return JSON.parse(rifaPremiosAttr);
                        } catch (e) {
                            console.error('Error al parsear premios:', e);
                            return [];
                        }
                    })() : []
                };
            } else {
                // Si no hay botón, intentar obtener datos de window.rifaSeleccionada (seteado por landing.js)
                if (window.rifaSeleccionada) {
                    rifaData = window.rifaSeleccionada;
                } else if (window.LandingRifas && window.LandingRifas.rifaSeleccionada) {
                    rifaData = window.LandingRifas.rifaSeleccionada;
                }
            }
            
            // Si hay datos, inicializar el modal
            if (rifaData) {
                inicializarModalConRifa(rifaData);
            } else {
                console.warn('No se encontraron datos de la rifa al abrir el modal');
            }
        });

        // Listener para evento personalizado initModalRifa (disparado desde landing.js)
        modalElement.addEventListener('initModalRifa', function (event) {
            if (event.detail) {
                inicializarModalConRifa(event.detail);
            }
        });

        // Función para calcular el total
        function calcularTotal() {
            const cantidad = parseInt(document.getElementById('cantidad_tickets').value) || 1;
            
            // Obtener precio del DOM o usar variable global
            const precioTicketEl = document.getElementById('precio_ticket');
            const precioActual = precioTicketEl ? parseFloat(precioTicketEl.textContent.replace(/[^0-9.]/g, '')) || precioUnitario : precioUnitario;
            
            const total = (cantidad * precioActual).toFixed(2);
            
            document.getElementById('total_pagar').textContent = 'S/ ' + total;
            document.getElementById('cantidad_display').textContent = cantidad;
            
            console.log('Calculando total:', {
                cantidad,
                precioActual,
                precioUnitario,
                total
            });
        }
        
        // Función para actualizar el resumen final
        function actualizarResumenFinal() {
            const ciudad = document.getElementById('ciudad').value || '-';
            const estado = document.getElementById('estado').value || '-';
            const ubicacion = ciudad !== '-' && estado !== '-' ? `${ciudad}, ${estado}` : '-';
            
            // Documento
            const tipoDoc = document.getElementById('tipo_documento').value || '';
            const numDoc = document.getElementById('numero_documento').value || '';
            const documento = tipoDoc && numDoc ? `${tipoDoc}: ${numDoc}` : '-';
            
            // Números de boleto seleccionados
            const numerosReservadosJSON = document.getElementById('numeros_reservados').value;
            const numerosFormateadosJSON = document.getElementById('numeros_formateados').value;
            
            // Obtener precio actualizado del DOM o usar la variable global
            const precioTicketEl = document.getElementById('precio_ticket');
            const precioActual = precioTicketEl ? parseFloat(precioTicketEl.textContent.replace(/[^0-9.]/g, '')) || precioUnitario : precioUnitario;
            const cantidadTickets = parseInt(document.getElementById('cantidad_tickets').value) || 1;
            const totalCalculado = (cantidadTickets * precioActual).toFixed(2);
            
            console.log('Actualizando resumen:', {
                precioActual,
                precioUnitario,
                cantidadTickets,
                totalCalculado
            });
            
            document.getElementById('resumen_rifa_nombre').textContent = rifaNombreGlobal || 'Rifa';
            const nombres = document.getElementById('nombres').value || '';
            const apellidos = document.getElementById('apellidos').value || '';
            document.getElementById('resumen_nombre').textContent = `${nombres} ${apellidos}`.trim() || '-';
            document.getElementById('resumen_email').textContent = document.getElementById('email_participante').value || '-';
            document.getElementById('resumen_telefono').textContent = document.getElementById('telefono').value || '-';
            document.getElementById('resumen_documento').textContent = documento;
            document.getElementById('resumen_direccion').textContent = document.getElementById('direccion_envio').value || '-';
            document.getElementById('resumen_ubicacion').textContent = ubicacion;
            
            // Mostrar números de boleto si se seleccionaron
            if (numerosFormateadosJSON && numerosFormateadosJSON !== '') {
                try {
                    const numerosArray = JSON.parse(numerosFormateadosJSON);
                    const containerNumeros = document.getElementById('resumen_numeros_boletos');
                    containerNumeros.innerHTML = '';
                    
                    numerosArray.forEach(num => {
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-success fs-14 px-3 py-2 me-1 mb-1';
                        badge.textContent = num;
                        containerNumeros.appendChild(badge);
                    });
                    
                    document.getElementById('resumen_numero_row').style.display = '';
                } catch (e) {
                    console.error('Error parsing números:', e);
                    document.getElementById('resumen_numero_row').style.display = 'none';
                }
            } else {
                document.getElementById('resumen_numero_row').style.display = 'none';
            }
            
            // Actualizar cantidades y precios
            document.getElementById('resumen_cantidad').textContent = cantidadTickets;
            document.getElementById('resumen_precio').textContent = 'S/ ' + precioActual.toFixed(2);
            document.getElementById('resumen_total').textContent = 'S/ ' + totalCalculado;
            
            // Actualizar también el total_pagar si existe
            const totalPagarEl = document.getElementById('total_pagar');
            if (totalPagarEl) {
                totalPagarEl.textContent = totalCalculado;
            }
        }
        
        // Actualizar resumen cuando se llega al tab final
        document.getElementById('pills-finish-tab').addEventListener('shown.bs.tab', function() {
            actualizarResumenFinal();
        });
        
        // ======= VALIDACIÓN EN TIEMPO REAL Y HABILITACIÓN DE BOTONES =======
        
        // Función para validar Tab 1 - Información Personal
        function validarTabPersonal() {
            const nombresInput = document.getElementById('nombres');
            const apellidosInput = document.getElementById('apellidos');
            const emailInput = document.getElementById('email_participante');
            const telefonoInput = document.getElementById('telefono');
            const tipoDocumentoInput = document.getElementById('tipo_documento');
            const numeroDocumentoInput = document.getElementById('numero_documento');
            const ciudadInput = document.getElementById('ciudad');
            const estadoInput = document.getElementById('estado');
            const direccionInput = document.getElementById('direccion_envio');
            
            const nombres = nombresInput.value.trim();
            const apellidos = apellidosInput.value.trim();
            const email = emailInput.value.trim();
            const telefono = telefonoInput.value.trim();
            const tipoDocumento = tipoDocumentoInput.value.trim();
            const numeroDocumento = numeroDocumentoInput.value.trim();
            const ciudad = ciudadInput.value.trim();
            const estado = estadoInput.value.trim();
            const direccion = direccionInput.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            const nombresValido = nombres.length >= 2;
            const apellidosValido = apellidos.length >= 2;
            const emailValido = emailRegex.test(email);
            const telefonoValido = telefono.length >= 8;
            const tipoDocumentoValido = tipoDocumento !== '';
            const numeroDocumentoValido = numeroDocumento.length >= 6;
            // Ciudad es opcional, no se valida como requerida
            const estadoValido = estado.length >= 3;
            const direccionValida = direccion.length >= 10;
            
            // Feedback visual para nombres
            if (nombres.length > 0) {
                if (nombresValido) {
                    nombresInput.classList.remove('border-danger');
                    nombresInput.classList.add('border-success');
                } else {
                    nombresInput.classList.remove('border-success');
                    nombresInput.classList.add('border-danger');
                }
            } else {
                nombresInput.classList.remove('border-success', 'border-danger');
            }
            
            // Feedback visual para apellidos
            if (apellidos.length > 0) {
                if (apellidosValido) {
                    apellidosInput.classList.remove('border-danger');
                    apellidosInput.classList.add('border-success');
                } else {
                    apellidosInput.classList.remove('border-success');
                    apellidosInput.classList.add('border-danger');
                }
            } else {
                apellidosInput.classList.remove('border-success', 'border-danger');
            }
            
            // Feedback visual para email
            if (email.length > 0) {
                if (emailValido) {
                    emailInput.classList.remove('border-danger');
                    emailInput.classList.add('border-success');
                } else {
                    emailInput.classList.remove('border-success');
                    emailInput.classList.add('border-danger');
                }
            } else {
                emailInput.classList.remove('border-success', 'border-danger');
            }
            
            // Feedback visual para teléfono
            if (telefono.length > 0) {
                if (telefonoValido) {
                    telefonoInput.classList.remove('border-danger');
                    telefonoInput.classList.add('border-success');
                } else {
                    telefonoInput.classList.remove('border-success');
                }
            } else {
                telefonoInput.classList.remove('border-success', 'border-danger');
            }
            
            // Feedback visual para tipo de documento
            if (tipoDocumento.length > 0) {
                if (tipoDocumentoValido) {
                    tipoDocumentoInput.classList.remove('border-danger');
                    tipoDocumentoInput.classList.add('border-success');
                } else {
                    tipoDocumentoInput.classList.remove('border-success');
                    tipoDocumentoInput.classList.add('border-danger');
                }
            } else {
                tipoDocumentoInput.classList.remove('border-success', 'border-danger');
            }
            
            // Feedback visual para número de documento
            if (numeroDocumento.length > 0) {
                if (numeroDocumentoValido) {
                    numeroDocumentoInput.classList.remove('border-danger');
                    numeroDocumentoInput.classList.add('border-success');
                } else {
                    numeroDocumentoInput.classList.remove('border-success');
                    numeroDocumentoInput.classList.add('border-danger');
                }
            } else {
                numeroDocumentoInput.classList.remove('border-success', 'border-danger');
            }
            
            // Feedback visual para ciudad (opcional, solo feedback positivo si tiene valor)
            if (ciudad.length > 0) {
                ciudadInput.classList.remove('border-danger');
                ciudadInput.classList.add('border-success');
            } else {
                ciudadInput.classList.remove('border-success', 'border-danger');
            }
            
            // Feedback visual para estado
            if (estado.length > 0) {
                if (estadoValido) {
                    estadoInput.classList.remove('border-danger');
                    estadoInput.classList.add('border-success');
                } else {
                    estadoInput.classList.remove('border-success');
                }
            } else {
                estadoInput.classList.remove('border-success', 'border-danger');
            }
            
            // Feedback visual para dirección
            if (direccion.length > 0) {
                if (direccionValida) {
                    direccionInput.classList.remove('border-danger');
                    direccionInput.classList.add('border-success');
                } else {
                    direccionInput.classList.remove('border-success');
                }
            } else {
                direccionInput.classList.remove('border-success', 'border-danger');
            }
            
            const esValido = nombresValido && apellidosValido && emailValido && telefonoValido && tipoDocumentoValido && numeroDocumentoValido && estadoValido && direccionValida;
            
            // Debug logging
            console.log('🔍 [DEBUG] validarTabPersonal() - Validación:', {
                nombres: nombres,
                nombresValido: nombresValido,
                apellidos: apellidos,
                apellidosValido: apellidosValido,
                emailValido: emailValido,
                telefonoValido: telefonoValido,
                tipoDocumentoValido: tipoDocumentoValido,
                numeroDocumentoValido: numeroDocumentoValido,
                estadoValido: estadoValido,
                direccionValida: direccionValida,
                esValido: esValido
            });
            
            // Habilitar o deshabilitar botón
            const btnContinuar = document.getElementById('btn_continuar_personal');
            if (btnContinuar) {
                btnContinuar.disabled = !esValido;
            } else {
                console.error('❌ [ERROR] No se encontró el botón btn_continuar_personal');
            }
            
            // Cambiar el texto del botón si está deshabilitado (responsive)
            if (btnContinuar) {
                if (!esValido) {
                    btnContinuar.innerHTML = `
                        <i class="ri-shopping-cart-line label-icon align-middle fs-16 ms-2"></i>
                        <span class="d-none d-sm-inline">Complete los datos obligatorios</span>
                        <span class="d-inline d-sm-none">Complete datos</span>
                    `;
                } else {
                    btnContinuar.innerHTML = `
                        <i class="ri-shopping-cart-line label-icon align-middle fs-16 ms-2"></i>
                        <span class="d-none d-sm-inline">Continuar a tu Orden</span>
                        <span class="d-inline d-sm-none">Continuar</span>
                    `;
                }
            }
            
            return esValido;
        }
        
        // Función para validar Tab 2 - Tu Orden (cantidad de tickets y números seleccionados)
        // Hacer accesible globalmente para que pueda ser llamada desde otros scopes
        window.validarTabOrden = function validarTabOrden() {
            console.log('🔍 [DEBUG] validarTabOrden() - INICIO');
            const cantidad = parseInt(document.getElementById('cantidad_tickets').value) || 1;
            console.log('🔍 [DEBUG] Cantidad de tickets requerida:', cantidad);
            
            // Actualizar cantidadTicketsRequerida para mantener consistencia (global)
            if (typeof window.cantidadTicketsRequerida !== 'undefined') {
                window.cantidadTicketsRequerida = cantidad;
            }
            console.log('🔍 [DEBUG] window.cantidadTicketsRequerida:', window.cantidadTicketsRequerida);
            
            // Obtener tickets disponibles del DOM o de variable global
            let ticketsDisponibles = 0;
            try {
                // Intentar leer del elemento del DOM primero
                const ticketsDisponiblesElement = document.getElementById('tickets_disponibles');
                if (ticketsDisponiblesElement) {
                    ticketsDisponibles = parseInt(ticketsDisponiblesElement.textContent) || 0;
                } else {
                    // Si no existe en el DOM, intentar variable global
                    ticketsDisponibles = window.ticketsDisponibles || 0;
                }
            } catch (e) {
                console.warn('Error al obtener ticketsDisponibles:', e);
                ticketsDisponibles = window.ticketsDisponibles || 0;
            }
            
            // Si ticketsDisponibles es 0, permitir continuar (puede ser que no haya números generados aún)
            // pero validar que la cantidad sea al menos 1
            const cantidadValida = cantidad >= 1 && (ticketsDisponibles === 0 || cantidad <= ticketsDisponibles);
            
            // Verificar si hay números seleccionados (si se han seleccionado números)
            const numerosReservados = document.getElementById('numeros_reservados').value;
            console.log('🔍 [DEBUG] numeros_reservados (raw):', numerosReservados);
            let numerosValidos = true;
            
            // Obtener cantidad de números seleccionados (tanto del campo oculto como del array en memoria)
            let cantidadNumerosSeleccionados = 0;
            console.log('🔍 [DEBUG] Iniciando conteo de números seleccionados...');
            
            // Primero intentar obtener del campo oculto (más confiable ya que se actualiza siempre)
            let numerosDelCampo = 0;
            if (numerosReservados && numerosReservados !== '[]' && numerosReservados !== '' && numerosReservados !== 'null') {
                try {
                    const numerosArray = JSON.parse(numerosReservados);
                    console.log('🔍 [DEBUG] numerosArray parseado:', numerosArray);
                    if (Array.isArray(numerosArray)) {
                        numerosDelCampo = numerosArray.length;
                        console.log('🔍 [DEBUG] numerosDelCampo:', numerosDelCampo);
                    }
                } catch (e) {
                    console.error('❌ [DEBUG] Error parsing numeros_reservados:', e, 'Valor:', numerosReservados);
                    numerosDelCampo = 0;
                }
            } else {
                console.log('🔍 [DEBUG] numeros_reservados está vacío o inválido');
            }
            
            // También intentar obtener del array en memoria global (como respaldo)
            let numerosDeMemoria = 0;
            try {
                console.log('🔍 [DEBUG] window.numerosSeleccionados:', window.numerosSeleccionados);
                if (typeof window.numerosSeleccionados !== 'undefined' && window.numerosSeleccionados && Array.isArray(window.numerosSeleccionados)) {
                    numerosDeMemoria = window.numerosSeleccionados.length;
                    console.log('🔍 [DEBUG] numerosDeMemoria:', numerosDeMemoria);
                } else {
                    console.log('🔍 [DEBUG] window.numerosSeleccionados no es un array válido');
                }
            } catch (e) {
                console.warn('❌ [DEBUG] Error al acceder a window.numerosSeleccionados:', e);
            }
            
            // También verificar el campo numeros_formateados como respaldo adicional
            let numerosDelCampoFormateados = 0;
            try {
                const numerosFormateados = document.getElementById('numeros_formateados').value;
                console.log('🔍 [DEBUG] numeros_formateados (raw):', numerosFormateados);
                if (numerosFormateados && numerosFormateados !== '[]' && numerosFormateados !== '' && numerosFormateados !== 'null') {
                    const formateadosArray = JSON.parse(numerosFormateados);
                    console.log('🔍 [DEBUG] formateadosArray parseado:', formateadosArray);
                    if (Array.isArray(formateadosArray)) {
                        numerosDelCampoFormateados = formateadosArray.length;
                        console.log('🔍 [DEBUG] numerosDelCampoFormateados:', numerosDelCampoFormateados);
                    }
                }
            } catch (e) {
                console.warn('❌ [DEBUG] Error parsing numeros_formateados:', e);
            }
            
            // Usar el mayor valor entre todos los métodos (prioridad: campo oculto > memoria > formateados)
            cantidadNumerosSeleccionados = Math.max(numerosDelCampo, numerosDeMemoria, numerosDelCampoFormateados);
            console.log('🔍 [DEBUG] cantidadNumerosSeleccionados (final):', cantidadNumerosSeleccionados, {
                numerosDelCampo,
                numerosDeMemoria,
                numerosDelCampoFormateados
            });
            
            // Verificar si el display de números está visible (indica que el usuario está seleccionando manualmente)
            // Usar múltiples métodos para determinar si hay números seleccionados:
            // 1. Verificar estilo del display
            // 2. Verificar si hay números en los campos ocultos o en memoria
            const displayElement = document.getElementById('numero_seleccionado_display');
            const displayStyle = displayElement ? window.getComputedStyle(displayElement).display : 'none';
            const displayVisibleByStyle = displayStyle !== 'none';
            const displayVisibleByContent = cantidadNumerosSeleccionados > 0;
            // El display está visible si tiene estilo visible O si hay números seleccionados
            const displayVisible = displayVisibleByStyle || displayVisibleByContent;
            
            // SIEMPRE validar que se hayan seleccionado exactamente la cantidad requerida
            // Esto aplica tanto para selección manual como automática
            numerosValidos = cantidadNumerosSeleccionados === cantidad;
            console.log('🔍 [DEBUG] numerosValidos:', numerosValidos, `(${cantidadNumerosSeleccionados} === ${cantidad})`);
            
            console.log('✅ [DEBUG] Validación Tab Orden - RESUMEN:', {
                cantidad,
                cantidadTicketsRequerida: window.cantidadTicketsRequerida,
                ticketsDisponibles,
                cantidadValida,
                cantidadNumerosSeleccionados,
                numerosDelCampo: numerosDelCampo,
                numerosDeMemoria: numerosDeMemoria,
                numerosDelCampoFormateados: numerosDelCampoFormateados,
                numerosReservados,
                numerosFormateados: document.getElementById('numeros_formateados').value,
                displayVisible,
                displayVisibleByStyle,
                displayVisibleByContent,
                displayStyle,
                numerosValidos,
                windowNumerosSeleccionados: window.numerosSeleccionados
            });
            
            const esValido = cantidadValida && numerosValidos;
            console.log('🔍 [DEBUG] esValido:', esValido, `(${cantidadValida} && ${numerosValidos})`);
            
            // Habilitar o deshabilitar botón
            const btnContinuar = document.getElementById('btn_continuar_orden');
            const estadoAnterior = btnContinuar.disabled;
            btnContinuar.disabled = !esValido;
            console.log('🔍 [DEBUG] Botón continuar:', {
                estadoAnterior: estadoAnterior ? 'DESHABILITADO' : 'HABILITADO',
                estadoNuevo: btnContinuar.disabled ? 'DESHABILITADO' : 'HABILITADO',
                texto: btnContinuar.innerHTML
            });
            
            // Cambiar texto del botón si está deshabilitado
            if (!esValido) {
                if (!cantidadValida) {
                    btnContinuar.innerHTML = `
                        <i class="ri-bank-card-line label-icon align-middle fs-16 ms-2"></i>
                        <span class="d-none d-sm-inline">Cantidad inválida</span>
                        <span class="d-inline d-sm-none">Inválido</span>
                    `;
                } else if (!numerosValidos) {
                    // Obtener cantidad actual de números seleccionados
                    let numerosActuales = cantidadNumerosSeleccionados;
                    
                    btnContinuar.innerHTML = `
                        <i class="ri-bank-card-line label-icon align-middle fs-16 ms-2"></i>
                        <span class="d-none d-sm-inline">Selecciona ${cantidad} número(s) (${numerosActuales}/${cantidad})</span>
                        <span class="d-inline d-sm-none">Selecciona ${cantidad} número(s)</span>
                    `;
                }
            } else {
                btnContinuar.innerHTML = `
                    <i class="ri-bank-card-line label-icon align-middle fs-16 ms-2"></i>
                    <span class="d-none d-sm-inline">Continuar a Pago</span>
                    <span class="d-inline d-sm-none">Continuar</span>
                `;
            }
            
            return esValido;
        };
        
        // También mantener referencia local para compatibilidad
        const validarTabOrden = window.validarTabOrden;
        
        // Función para validar Tab 4 - Confirmar (términos y condiciones)
        function validarTabConfirmar() {
            const aceptoTerminos = document.getElementById('acepto_terminos').checked;
            
            // Habilitar o deshabilitar botón de compra
            const btnCompra = document.getElementById('btn_realizar_compra');
            btnCompra.disabled = !aceptoTerminos;
            
            // Cambiar el texto del botón si está deshabilitado (desktop y móvil)
            const btnTextDesktop = document.getElementById('btn_compra_text');
            const btnTextMobile = document.getElementById('btn_compra_text_mobile');
            
            if (!aceptoTerminos) {
                btnTextDesktop.textContent = 'Acepta los términos para continuar';
                btnTextMobile.textContent = 'Acepta términos';
            } else {
                btnTextDesktop.textContent = 'Confirmar Compra';
                btnTextMobile.textContent = 'Confirmar';
            }
            
            return aceptoTerminos;
        }
        
        // ======= EVENTOS DE VALIDACIÓN EN TIEMPO REAL =======
        
        // Validar campos del Tab 1 en tiempo real
        // Usar delegación de eventos para asegurar que funcione incluso si los elementos se cargan después
        const modalComprarTicket = document.getElementById('modal_comprar_ticket');
        if (modalComprarTicket) {
            modalComprarTicket.addEventListener('input', function(e) {
                const targetId = e.target.id;
                if (['nombres', 'apellidos', 'email_participante', 'telefono', 'numero_documento', 'ciudad', 'estado', 'direccion_envio'].includes(targetId)) {
                    validarTabPersonal();
                }
            });
            
            modalComprarTicket.addEventListener('change', function(e) {
                const targetId = e.target.id;
                if (['tipo_documento'].includes(targetId)) {
                    validarTabPersonal();
                }
            });
        }
        
        // También agregar listeners directos si los elementos existen
        const nombresEl = document.getElementById('nombres');
        const apellidosEl = document.getElementById('apellidos');
        const emailEl = document.getElementById('email_participante');
        const telefonoEl = document.getElementById('telefono');
        const tipoDocEl = document.getElementById('tipo_documento');
        const numDocEl = document.getElementById('numero_documento');
        const ciudadEl = document.getElementById('ciudad');
        const estadoEl = document.getElementById('estado');
        const direccionEl = document.getElementById('direccion_envio');
        
        if (nombresEl) nombresEl.addEventListener('input', validarTabPersonal);
        if (apellidosEl) apellidosEl.addEventListener('input', validarTabPersonal);
        if (emailEl) emailEl.addEventListener('input', validarTabPersonal);
        if (telefonoEl) telefonoEl.addEventListener('input', validarTabPersonal);
        if (tipoDocEl) tipoDocEl.addEventListener('change', validarTabPersonal);
        if (numDocEl) numDocEl.addEventListener('input', validarTabPersonal);
        if (ciudadEl) ciudadEl.addEventListener('input', validarTabPersonal);
        if (estadoEl) estadoEl.addEventListener('input', validarTabPersonal);
        if (direccionEl) direccionEl.addEventListener('input', validarTabPersonal);
        
        // Validar cantidad de tickets en tiempo real (este listener se maneja más abajo también)
        // Se mantiene aquí solo para validación rápida, pero el listener completo está más abajo
        
        // Validar checkbox de términos
        document.getElementById('acepto_terminos').addEventListener('change', function() {
            validarTabConfirmar();
        });

        // Botón aumentar cantidad
        document.getElementById('btn_mas').addEventListener('click', function() {
            const input = document.getElementById('cantidad_tickets');
            let valor = parseInt(input.value) || 1;
            const max = parseInt(input.getAttribute('max')) || 999;
            
            if (valor < max) {
                input.value = valor + 1;
                const nuevaCantidad = valor + 1;
                if (typeof window.cantidadTicketsRequerida !== 'undefined') {
                    window.cantidadTicketsRequerida = nuevaCantidad;
                }
                calcularTotal();
                
                // Actualizar display de números si hay números seleccionados
                if (typeof actualizarDisplayNumeros === 'function' && window.numerosSeleccionados && window.numerosSeleccionados.length > 0) {
                    actualizarDisplayNumeros();
                }
                
                if (typeof window.validarTabOrden === 'function') {
                    window.validarTabOrden();
                } else if (typeof validarTabOrden === 'function') {
                    validarTabOrden();
                }
                
                // Actualizar resumen si estamos en el tab final
                const finishTab = document.getElementById('pills-finish-tab');
                if (finishTab && finishTab.classList.contains('active')) {
                    actualizarResumenFinal();
                }
            }
        });

        // Botón disminuir cantidad
        document.getElementById('btn_menos').addEventListener('click', function() {
            const input = document.getElementById('cantidad_tickets');
            let valor = parseInt(input.value) || 1;
            
            if (valor > 1) {
                input.value = valor - 1;
                const nuevaCantidad = valor - 1;
                if (typeof window.cantidadTicketsRequerida !== 'undefined') {
                    window.cantidadTicketsRequerida = nuevaCantidad;
                }
                calcularTotal();
                
                // Actualizar display de números si hay números seleccionados
                if (typeof actualizarDisplayNumeros === 'function' && window.numerosSeleccionados && window.numerosSeleccionados.length > 0) {
                    actualizarDisplayNumeros();
                }
                
                if (typeof window.validarTabOrden === 'function') {
                    window.validarTabOrden();
                } else if (typeof validarTabOrden === 'function') {
                    validarTabOrden();
                }
                
                // Actualizar resumen si estamos en el tab final
                const finishTab = document.getElementById('pills-finish-tab');
                if (finishTab && finishTab.classList.contains('active')) {
                    actualizarResumenFinal();
                }
            }
        });

        // Evento al cambiar la cantidad manualmente
        document.getElementById('cantidad_tickets').addEventListener('input', function() {
            console.log('🔵 [DEBUG] cantidad_tickets - INPUT cambiado');
            let valor = parseInt(this.value) || 1;
            const max = parseInt(this.getAttribute('max')) || 999;
            console.log('🔵 [DEBUG] Nuevo valor:', valor, 'Max:', max);
            
            if (valor < 1) {
                this.value = 1;
                valor = 1;
            } else if (max > 0 && valor > max) {
                this.value = max;
                valor = max;
                if (window.Utils && window.Utils.showAlert) {
                    window.Utils.showAlert(`Solo hay ${max} tickets disponibles`, 'warning');
                } else {
                    alert(`Solo hay ${max} tickets disponibles`);
                }
            }
            
            if (typeof window.cantidadTicketsRequerida !== 'undefined') {
                window.cantidadTicketsRequerida = valor;
            }
            console.log('🔵 [DEBUG] window.cantidadTicketsRequerida actualizado a:', window.cantidadTicketsRequerida);
            calcularTotal();
            
            // Actualizar display de números si hay números seleccionados
            if (typeof actualizarDisplayNumeros === 'function' && window.numerosSeleccionados && window.numerosSeleccionados.length > 0) {
                console.log('🔵 [DEBUG] Llamando actualizarDisplayNumeros()');
                actualizarDisplayNumeros();
            } else {
                console.log('🔵 [DEBUG] No se llama actualizarDisplayNumeros() - no hay números seleccionados');
            }
            
            console.log('🔵 [DEBUG] Llamando validarTabOrden() desde input cantidad_tickets');
            if (typeof window.validarTabOrden === 'function') {
                window.validarTabOrden();
            } else if (typeof validarTabOrden === 'function') {
                validarTabOrden();
            } else {
                console.error('❌ [DEBUG] validarTabOrden no está definida');
            }
            
            // Actualizar resumen si estamos en el tab final
            const finishTab = document.getElementById('pills-finish-tab');
            if (finishTab && finishTab.classList.contains('active')) {
                actualizarResumenFinal();
            }
        });

        // Función para limpiar errores
        function limpiarErrores() {
            // Remover clases de error de todos los inputs
            document.querySelectorAll('#form_comprar_ticket .form-control').forEach(input => {
                input.classList.remove('border-danger');
            });
            
            // Ocultar todos los mensajes de error
            document.querySelectorAll('#form_comprar_ticket [id$="_error"]').forEach(error => {
                error.style.display = 'none';
                error.textContent = '';
            });
            
            // Limpiar vista previa del comprobante
            document.getElementById('preview_comprobante').style.display = 'none';
        }

        // Función para mostrar error en un campo
        function mostrarError(campo, mensaje) {
            const input = document.getElementById(campo);
            const errorDiv = document.getElementById(campo + '_error');
            
            if (input) {
                input.classList.add('border-danger');
            }
            
            if (errorDiv) {
                errorDiv.textContent = mensaje;
                errorDiv.style.display = 'block';
            }
        }

        // Función de validación
        function validarFormulario() {
            limpiarErrores();
            let esValido = true;

            // Validar nombres
            const nombres = document.getElementById('nombres').value.trim();
            if (nombres === '') {
                mostrarError('nombres', 'Por favor, ingrese sus nombres');
                esValido = false;
            }
            
            // Validar apellidos
            const apellidos = document.getElementById('apellidos').value.trim();
            if (apellidos === '') {
                mostrarError('apellidos', 'Por favor, ingrese sus apellidos');
                esValido = false;
            }

            // Validar email (obligatorio)
            const email = document.getElementById('email_participante').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email === '') {
                mostrarError('email_participante', 'Por favor, ingrese su correo electrónico');
                esValido = false;
            } else if (!emailRegex.test(email)) {
                mostrarError('email_participante', 'Por favor, ingrese un correo electrónico válido');
                esValido = false;
            }

            // Validar teléfono
            const telefono = document.getElementById('telefono').value.trim();
            if (telefono === '') {
                mostrarError('telefono', 'Por favor, ingrese su número de teléfono');
                esValido = false;
            }

            // Validar tipo de documento
            const tipoDocumento = document.getElementById('tipo_documento').value;
            if (tipoDocumento === '') {
                mostrarError('tipo_documento', 'Por favor, seleccione el tipo de documento');
                esValido = false;
            }

            // Validar número de documento
            const numeroDocumento = document.getElementById('numero_documento').value.trim();
            if (numeroDocumento === '') {
                mostrarError('numero_documento', 'Por favor, ingrese su número de documento');
                esValido = false;
            } else if (numeroDocumento.length < 6) {
                mostrarError('numero_documento', 'El número de documento debe tener al menos 6 caracteres');
                esValido = false;
            }

            // Validar estado (requerido)
            const estado = document.getElementById('estado').value.trim();
            if (estado === '') {
                mostrarError('estado', 'Por favor, ingrese su estado o provincia');
                esValido = false;
            }

            // Ciudad es opcional, no requiere validación

            // Validar dirección de envío
            const direccion = document.getElementById('direccion_envio').value.trim();
            if (direccion === '') {
                mostrarError('direccion_envio', 'Por favor, ingrese su dirección completa de envío');
                esValido = false;
            } else if (direccion.length < 10) {
                mostrarError('direccion_envio', 'La dirección debe ser más específica (mínimo 10 caracteres)');
                esValido = false;
            }

            // Validar cantidad de tickets
            const cantidadTickets = parseInt(document.getElementById('cantidad_tickets').value);
            if (isNaN(cantidadTickets) || cantidadTickets < 1) {
                mostrarError('cantidad_tickets', 'La cantidad debe ser al menos 1');
                esValido = false;
            } else if (ticketsDisponibles > 0 && cantidadTickets > ticketsDisponibles) {
                // Solo validar límite si hay tickets disponibles (si es 0, permitir cualquier cantidad)
                mostrarError('cantidad_tickets', `Solo hay ${ticketsDisponibles} tickets disponibles`);
                esValido = false;
            }

            // Validar términos y condiciones
            const aceptoTerminos = document.getElementById('acepto_terminos').checked;
            if (!aceptoTerminos) {
                mostrarError('acepto_terminos', 'Debe aceptar los términos y condiciones');
                esValido = false;
            }

            return esValido;
        }

        // Limpiar error cuando el usuario empieza a escribir (inputs y textareas)
        document.querySelectorAll('#form_comprar_ticket input, #form_comprar_ticket textarea').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('border-danger');
                const errorDiv = document.getElementById(this.id + '_error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                    errorDiv.textContent = '';
                }
            });
        });

        // Limpiar error del checkbox cuando cambia
        document.getElementById('acepto_terminos').addEventListener('change', function() {
            const errorDiv = document.getElementById('acepto_terminos_error');
            if (errorDiv) {
                errorDiv.style.display = 'none';
                errorDiv.textContent = '';
            }
        });

        // Función para subir comprobante de pago
        window.subirComprobantePago = async function(ticketId, archivo, monto) {
            try {
                const formData = new FormData();
                formData.append('comprobante', archivo);
                formData.append('sede_id', '1'); // TODO: Obtener de configuración
                formData.append('ticket_id', ticketId);
                formData.append('monto', monto);
                formData.append('fecha_pago', new Date().toISOString().slice(0, 19).replace('T', ' '));
                formData.append('creado_por', 'Usuario');
                
                const API_BASE_URL = window.API_BASE_URL || (window.location.origin + '/sistema_rifas/api');
                const response = await fetch(`${API_BASE_URL}/tickets/uploadComprobante`, {
                    method: 'POST',
                    body: formData
                });
                
                const resultado = await response.json();
                
                if (!resultado.ok) {
                    throw new Error(resultado.msj || 'Error al subir comprobante');
                }
                
                return resultado;
            } catch (error) {
                console.error('Error en subirComprobantePago:', error);
                throw error;
            }
        };
        
        // Mostrar vista previa del archivo de comprobante
        document.getElementById('comprobante_pago').addEventListener('change', function() {
            const archivo = this.files[0];
            const preview = document.getElementById('preview_comprobante');
            const nombreArchivo = document.getElementById('nombre_archivo');
            
            if (archivo) {
                // Validar tamaño (máx 5MB)
                if (archivo.size > 5 * 1024 * 1024) {
                    if (window.Utils && window.Utils.showAlert) {
                        window.Utils.showAlert('El archivo es demasiado grande. Máximo 5MB', 'error');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Archivo muy grande',
                            text: 'El archivo es demasiado grande. Máximo 5MB',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        alert('El archivo es demasiado grande. Máximo 5MB');
                    }
                    this.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                // Validar tipo de archivo
                const tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'];
                if (!tiposPermitidos.includes(archivo.type)) {
                    if (window.Utils && window.Utils.showAlert) {
                        window.Utils.showAlert('Tipo de archivo no permitido. Solo se permiten JPG, PNG y PDF', 'error');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Tipo de archivo inválido',
                            text: 'Solo se permiten JPG, PNG y PDF',
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        alert('Tipo de archivo no permitido. Solo se permiten JPG, PNG y PDF');
                    }
                    this.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                nombreArchivo.textContent = archivo.name + ' (' + (archivo.size / 1024).toFixed(2) + ' KB)';
                preview.style.display = 'block';
            } else {
                preview.style.display = 'none';
            }
        });

        // Manejo del envío del formulario
        document.getElementById('form_comprar_ticket').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            console.log('Formulario enviado');
            
            // Validar formulario
            if (!validarFormulario()) {
                console.log('Validación falló');
                return;
            }

            // Obtener datos del formulario
            const rifaId = document.getElementById('rifa_id').value;
            const nombres = document.getElementById('nombres').value.trim();
            const apellidos = document.getElementById('apellidos').value.trim();
            
            // Obtener precio del total (remover S/ y espacios)
            const totalPagarText = document.getElementById('total_pagar').textContent;
            const precioPagado = parseFloat(totalPagarText.replace(/[^0-9.]/g, '')) || 0;
            
            // Números seleccionados - obtener solo los números enteros
            const numerosReservadosJSON = document.getElementById('numeros_reservados').value;
            let numerosSeleccionados = null;
            if (numerosReservadosJSON && numerosReservadosJSON !== '' && numerosReservadosJSON !== '[]') {
                try {
                    const numerosArray = JSON.parse(numerosReservadosJSON);
                    // Asegurar que sea un array de números enteros
                    if (Array.isArray(numerosArray)) {
                        numerosSeleccionados = numerosArray.map(n => {
                            // Si es un objeto, tomar el entero; si es un número, usarlo directamente
                            return typeof n === 'object' && n !== null ? n.entero || n : parseInt(n, 10);
                        }).filter(n => !isNaN(n) && n > 0);
                    }
                } catch (e) {
                    console.error('Error parsing números:', e);
                }
            }
            
            // Si no hay números seleccionados pero hay números en memoria global, usarlos
            if ((!numerosSeleccionados || numerosSeleccionados.length === 0) && window.numerosSeleccionados && window.numerosSeleccionados.length > 0) {
                numerosSeleccionados = window.numerosSeleccionados.map(n => {
                    return typeof n === 'object' && n !== null ? n.entero || n : parseInt(n, 10);
                }).filter(n => !isNaN(n) && n > 0);
            }
            
            console.log('🔵 [DEBUG] Números a enviar al backend:', numerosSeleccionados);
            
            // Preparar datos para enviar
            const datosCompra = {
                sede_id: 1, // TODO: Obtener de configuración
                rifa_id: parseInt(rifaId),
                nombres: nombres,
                apellidos: apellidos,
                tipo_documento: document.getElementById('tipo_documento').value,
                numero_documento: document.getElementById('numero_documento').value,
                email: document.getElementById('email_participante').value,
                telefono: document.getElementById('telefono').value,
                direccion: document.getElementById('direccion_envio').value,
                ciudad: document.getElementById('ciudad').value.trim() || null,
                pais: 'Perú', // TODO: Obtener de configuración
                precio_pagado: precioPagado,
                cantidad_tickets: parseInt(document.getElementById('cantidad_tickets').value) || 1,
                numeros_seleccionados: numerosSeleccionados,
                canal_venta: 'WEB' // Usuario final comprando desde landing
            };
            
            console.log('Enviando datos de compra:', datosCompra);

            try {
                // Mostrar loading
                const btnCompra = document.getElementById('btn_realizar_compra');
                const textoOriginal = btnCompra.innerHTML;
                btnCompra.disabled = true;
                btnCompra.innerHTML = '<i class="ri-loader-4-line animate-spin me-1"></i> Procesando...';
                
                // Enviar al backend
                const API_BASE_URL = window.API_BASE_URL || (window.location.origin + '/sistema_rifas/api');
                const response = await fetch(`${API_BASE_URL}/tickets/create`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datosCompra)
                });
                
                const resultado = await response.json();
                
                console.log('Respuesta del servidor:', resultado);
                
                if (resultado.ok) {
                    // Éxito - Ticket creado
                    const codigoTicket = resultado.codigo_ticket || resultado.data?.codigo_ticket || 'N/A';
                    const ticketId = resultado.ticket_id || resultado.data?.ticket_id;
                    
                    // Si hay comprobante, subirlo ahora
                    const comprobanteFile = document.getElementById('comprobante_pago').files[0];
                    if (comprobanteFile && ticketId) {
                        try {
                            await subirComprobantePago(ticketId, comprobanteFile, precioPagado);
                        } catch (error) {
                            console.error('Error al subir comprobante:', error);
                            // Mostrar advertencia pero no bloquear el éxito del ticket
                            if (window.Utils && window.Utils.showToast) {
                                window.Utils.showToast('El ticket se creó correctamente, pero hubo un problema al subir el comprobante. Puedes subirlo más tarde.', 'warning');
                            }
                        }
                    }
                    
                    // Cerrar modal primero
                    bootstrap.Modal.getInstance(modalElement).hide();
                    
                    // Limpiar todos los datos seleccionados
                    window.numerosSeleccionados = [];
                    document.getElementById('numeros_reservados').value = '';
                    document.getElementById('numeros_formateados').value = '';
                    document.getElementById('cantidad_tickets').value = '1';
                    
                    // Resetear formulario
                    document.getElementById('form_comprar_ticket').reset();
                    limpiarErrores();
                    
                    // Mostrar mensaje de éxito y recargar después
                    if (window.Utils && window.Utils.showAlert) {
                        window.Utils.showAlert(
                            `Tu compra ha sido registrada correctamente.\n\nCódigo de ticket: ${codigoTicket}\n\n${comprobanteFile ? 'Tu comprobante ha sido subido y será validado por un administrador.' : 'Recibirás un correo con las instrucciones de pago.'}`,
                            'success',
                            {
                                html: `
                                    <p>Tu compra ha sido registrada correctamente.</p>
                                    <p class="fw-semibold">Código de ticket: <span class="text-primary">${codigoTicket}</span></p>
                                    <p class="text-muted small">${comprobanteFile ? 'Tu comprobante ha sido subido y será validado por un administrador.' : 'Recibirás un correo con las instrucciones de pago.'}</p>
                                `
                            }
                        ).then(() => {
                            // Recargar página después de cerrar el alert
                            location.reload();
                        });
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Compra registrada exitosamente!',
                            html: `
                                <p>Tu compra ha sido registrada correctamente.</p>
                                <p class="fw-semibold">Código de ticket: <span class="text-primary">${codigoTicket}</span></p>
                                <p class="text-muted small">${comprobanteFile ? 'Tu comprobante ha sido subido y será validado por un administrador.' : 'Recibirás un correo con las instrucciones de pago.'}</p>
                            `,
                            confirmButtonText: 'Aceptar'
                        }).then(() => {
                            // Recargar página después de cerrar el alert
                            location.reload();
                        });
                    } else {
                        alert('¡Compra registrada exitosamente!\n\nCódigo de ticket: ' + codigoTicket + '\n\n' + (comprobanteFile ? 'Tu comprobante ha sido subido.' : 'Recibirás un correo con las instrucciones de pago.'));
                        // Recargar página después del alert
                        setTimeout(() => {
                            location.reload();
                        }, 500);
                    }
                } else {
                    // Error
                    const mensajeError = resultado.msj || resultado.detalle || 'No se pudo procesar tu compra. Por favor, intenta nuevamente.';
                    
                    if (window.Utils && window.Utils.showAlert) {
                        window.Utils.showAlert(mensajeError, 'error');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error al procesar la compra',
                            text: mensajeError,
                            confirmButtonText: 'Aceptar'
                        });
                    } else {
                        alert('Error: ' + mensajeError);
                    }
                    
                    btnCompra.disabled = false;
                    btnCompra.innerHTML = textoOriginal;
                }
            } catch (error) {
                console.error('Error al enviar compra:', error);
                const mensajeError = 'No se pudo conectar con el servidor. Por favor, verifica tu conexión e intenta nuevamente.';
                
                if (window.Utils && window.Utils.showAlert) {
                    window.Utils.showAlert(mensajeError, 'error');
                } else if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: mensajeError,
                        confirmButtonText: 'Aceptar'
                    });
                } else {
                    alert('Error de conexión: ' + mensajeError);
                }
                
                const btnCompra = document.getElementById('btn_realizar_compra');
                btnCompra.disabled = false;
                btnCompra.innerHTML = '<i class="ri-shopping-bag-line label-icon align-middle fs-16 ms-2"></i><span id="btn_compra_text" class="d-none d-sm-inline">Confirmar Compra</span><span id="btn_compra_text_mobile" class="d-inline d-sm-none">Confirmar</span>';
            }
        });

        // Resetear validación al cerrar el modal
        modalElement.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('form_comprar_ticket');
            form.reset();
            limpiarErrores();
            
            // Limpiar números seleccionados
            window.numerosSeleccionados = [];
            document.getElementById('numeros_reservados').value = '';
            document.getElementById('numeros_formateados').value = '';
            document.getElementById('cantidad_tickets').value = '1';
            
            // Ocultar display de números seleccionados
            const displayNumeros = document.getElementById('numero_seleccionado_display');
            if (displayNumeros) {
                displayNumeros.style.display = 'none';
            }
            
            // Limpiar vista previa de comprobante
            const comprobanteInput = document.getElementById('comprobante_pago');
            if (comprobanteInput) {
                comprobanteInput.value = '';
            }
            const previewComprobante = document.getElementById('preview_comprobante');
            if (previewComprobante) {
                previewComprobante.style.display = 'none';
            }
            
            // Resetear navegación - solo primer tab habilitado
            const personalTab = document.getElementById('pills-personal-tab');
            const orderTab = document.getElementById('pills-order-tab');
            const paymentTab = document.getElementById('pills-payment-tab');
            const finishTab = document.getElementById('pills-finish-tab');
            
            personalTab.disabled = false;
            personalTab.removeAttribute('disabled');
            personalTab.classList.remove('done');
            
            orderTab.disabled = true;
            orderTab.setAttribute('disabled', 'disabled');
            orderTab.classList.remove('done');
            
            paymentTab.disabled = true;
            paymentTab.setAttribute('disabled', 'disabled');
            paymentTab.classList.remove('done');
            
            finishTab.disabled = true;
            finishTab.setAttribute('disabled', 'disabled');
            finishTab.classList.remove('done');
            
            // Deshabilitar botones de continuar
            document.getElementById('btn_continuar_personal').disabled = true;
            document.getElementById('btn_continuar_orden').disabled = true; // Deshabilitado por defecto hasta que se seleccionen números
            document.getElementById('btn_continuar_pago').disabled = false;
            document.getElementById('btn_realizar_compra').disabled = true;
            
            // Volver al primer tab
            const firstTab = new bootstrap.Tab(personalTab);
            firstTab.show();
        });
        
        // Validar estado inicial cuando se muestran los tabs
        document.getElementById('pills-personal-tab').addEventListener('shown.bs.tab', function() {
            console.log('🔵 [DEBUG] Tab 1 (Personal) - MOSTRADO, ejecutando validación...');
            setTimeout(() => {
                validarTabPersonal();
            }, 50);
        });
        
        // Validar también cuando se carga la página si el tab está activo
        setTimeout(() => {
            const personalTab = document.getElementById('pills-personal-tab');
            if (personalTab && personalTab.classList.contains('active')) {
                console.log('🔵 [DEBUG] Tab 1 está activo al cargar, ejecutando validación inicial...');
                validarTabPersonal();
            }
        }, 200);
        
        document.getElementById('pills-order-tab').addEventListener('shown.bs.tab', function() {
            console.log('🔵 [DEBUG] Tab 2 (Tu Orden) - MOSTRADO');
            console.log('🔵 [DEBUG] Estado actual:', {
                cantidadTickets: document.getElementById('cantidad_tickets').value,
                numerosReservados: document.getElementById('numeros_reservados').value,
                numerosFormateados: document.getElementById('numeros_formateados').value,
                windowNumerosSeleccionados: window.numerosSeleccionados,
                displayVisible: document.getElementById('numero_seleccionado_display').style.display
            });
            // Al mostrar el tab de orden, validar y asegurar que el botón esté deshabilitado si no hay números
            if (typeof window.validarTabOrden === 'function') {
                window.validarTabOrden();
            } else if (typeof validarTabOrden === 'function') {
                validarTabOrden();
            }
        });
        
        document.getElementById('pills-order-tab').addEventListener('hide.bs.tab', function() {
            console.log('🔴 [DEBUG] Tab 2 (Tu Orden) - OCULTADO');
            console.log('🔴 [DEBUG] Estado al ocultar:', {
                cantidadTickets: document.getElementById('cantidad_tickets').value,
                numerosReservados: document.getElementById('numeros_reservados').value,
                numerosFormateados: document.getElementById('numeros_formateados').value,
                windowNumerosSeleccionados: window.numerosSeleccionados,
                btnContinuarDisabled: document.getElementById('btn_continuar_orden').disabled
            });
        });
        
        document.getElementById('pills-finish-tab').addEventListener('shown.bs.tab', function() {
            validarTabConfirmar();
        });
    });
})();

// Script para Modal de Ver Premios
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const modalVerPremios = document.getElementById('modal_ver_premios');
        
        modalVerPremios.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const rifaNombre = button.getAttribute('data-rifa-nombre');
            const rifaPremios = JSON.parse(button.getAttribute('data-rifa-premios'));

            // Actualizar título del modal
            document.getElementById('modal_premios_rifa_nombre').textContent = rifaNombre;

            // Renderizar galería de premios
            const galeria = document.getElementById('galeria_premios');
            galeria.innerHTML = '';

            rifaPremios.forEach((premio, index) => {
                const badgeClass = index === 0 ? 'bg-warning text-white' : index === 1 ? 'bg-secondary text-white' : 'bg-dark text-white';
                const colClass = rifaPremios.length === 1 ? 'col-12' : rifaPremios.length === 2 ? 'col-md-6' : 'col-md-4';
                
                galeria.innerHTML += `
                    <div class="${colClass}">
                        <div class="card border shadow-sm h-100">
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <span class="badge ${badgeClass} fs-6 px-3 py-2">${premio.posicion}° Premio</span>
                                </div>
                                <h5 class="text-center mb-3">${premio.nombre}</h5>
                                <div class="text-center mb-3">
                                    <img src="${premio.imagen || 'assets/images/premios/default.jpg'}" 
                                         alt="${premio.nombre}" 
                                         class="img-fluid rounded shadow-sm" 
                                         style="max-height: 300px; width: auto; object-fit: cover;"
                                         onerror="this.src='assets/images/premios/default.jpg'">
                                </div>
                                ${premio.descripcion ? `
                                    <p class="text-muted text-center mb-0">
                                        <small>${premio.descripcion}</small>
                                    </p>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
        });
    });
})();

// Script para Consultar Tickets
(function() {
    document.addEventListener('DOMContentLoaded', function() {
        const formConsultar = document.getElementById('form_consultar_tickets');
        const resultadosDiv = document.getElementById('resultados_tickets');
        const listaTickets = document.getElementById('lista_tickets_usuario');

        formConsultar.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const documento = document.getElementById('documento_ticket').value.trim();
            const email = document.getElementById('email_consulta').value.trim();

            // Limpiar errores
            document.getElementById('documento_ticket').classList.remove('border-danger');
            document.getElementById('email_consulta').classList.remove('border-danger');
            document.getElementById('documento_ticket_error').style.display = 'none';
            document.getElementById('email_consulta_error').style.display = 'none';

            let hayErrores = false;

            // Validación
            if (documento === '') {
                mostrarErrorConsulta('documento_ticket', 'Por favor, ingrese su documento o código de ticket');
                hayErrores = true;
            }

            if (email === '') {
                mostrarErrorConsulta('email_consulta', 'Por favor, ingrese su correo electrónico');
                hayErrores = true;
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                mostrarErrorConsulta('email_consulta', 'Por favor, ingrese un correo válido');
                hayErrores = true;
            }

            if (hayErrores) return;

            // Simulación de consulta (aquí harías la llamada AJAX)
            console.log('Consultando tickets para:', { documento, email });

            // Ejemplo de respuesta simulada
            const ticketsSimulados = [
                {
                    rifa: 'Rifa iPhone 15 Pro Max',
                    codigo: 'TICKET-2025-001',
                    numeros: ['001', '023', '045', '067', '089'],
                    estado: 'VALIDADO',
                    fecha_compra: '2025-11-20',
                    premio: 'iPhone 15 Pro Max 256GB'
                }
            ];

            mostrarTickets(ticketsSimulados);
        });

        function mostrarErrorConsulta(campo, mensaje) {
            const input = document.getElementById(campo);
            const errorDiv = document.getElementById(campo + '_error');
            input.classList.add('border-danger');
            errorDiv.textContent = mensaje;
            errorDiv.style.display = 'block';
        }

        function mostrarTickets(tickets) {
            if (tickets.length === 0) {
                listaTickets.innerHTML = `
                    <div class="alert alert-warning border-0">
                        <i class="ri-information-line me-2"></i>
                        No se encontraron tickets con los datos proporcionados. Verifica tu información.
                    </div>
                `;
                resultadosDiv.style.display = 'block';
                return;
            }

            listaTickets.innerHTML = '';

            tickets.forEach(ticket => {
                const estadoClass = ticket.estado === 'VALIDADO' ? 'bg-success' : 
                                  ticket.estado === 'PENDIENTE' ? 'bg-warning' : 'bg-secondary';
                
                listaTickets.innerHTML += `
                    <div class="card border mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h6 class="mb-1"><i class="ri-ticket-line text-primary"></i> ${ticket.rifa}</h6>
                                    <p class="text-muted mb-0 small">Código: <strong>${ticket.codigo}</strong></p>
                                    <p class="text-muted mb-0 small">Compra: ${ticket.fecha_compra}</p>
                                </div>
                                <span class="badge ${estadoClass} text-white">${ticket.estado}</span>
                            </div>
                            
                            <div class="mb-3">
                                <strong class="d-block mb-2"><i class="ri-trophy-line text-warning"></i> Premio Participando:</strong>
                                <p class="mb-0">${ticket.premio}</p>
                            </div>
                            
                            <div>
                                <strong class="d-block mb-2"><i class="ri-hashtag text-info"></i> Tus Números de Ticket:</strong>
                                <div class="d-flex flex-wrap gap-2">
                                    ${ticket.numeros.map(num => `
                                        <span class="badge bg-primary-subtle text-primary fs-6 px-3 py-2">${num}</span>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });

            resultadosDiv.style.display = 'block';
            
            // Scroll suave a los resultados
            resultadosDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        // Limpiar errores al escribir
        document.querySelectorAll('#form_consultar_tickets input').forEach(input => {
            input.addEventListener('input', function() {
                this.classList.remove('border-danger');
                const errorDiv = document.getElementById(this.id + '_error');
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });
        });
    });
})();

// Script para Sistema de Selección de Números de Boleto
(function() {
    // Variables globales para gestión de números (hacer accesibles globalmente)
    let numerosDisponibles = [];
    window.numerosSeleccionados = window.numerosSeleccionados || []; // Ahora es un array de múltiples números, accesible globalmente
    let timerReserva = null;
    let tiempoRestante = 600; // 10 minutos en segundos
    let rifaActual = null;
    window.cantidadTicketsRequerida = window.cantidadTicketsRequerida || 1;
    
    // Alias local para facilitar el uso dentro de este scope
    let numerosSeleccionados = window.numerosSeleccionados;
    let cantidadTicketsRequerida = window.cantidadTicketsRequerida;
    
    // Función para mostrar modal con grid de números
    // Cargar números disponibles desde la API
    window.mostrarGridNumeros = async function() {
        const rifaId = document.getElementById('rifa_id').value;
        const cantidadTickets = parseInt(document.getElementById('cantidad_tickets').value) || 1;
        
        rifaActual = rifaId;
        cantidadTicketsRequerida = cantidadTickets;
        window.cantidadTicketsRequerida = cantidadTickets;
        
        // Actualizar título del modal
        const tituloModal = document.getElementById('modal_seleccionar_numero_label');
        if (cantidadTickets > 1) {
            tituloModal.innerHTML = `<i class="ri-hashtag me-2"></i>Selecciona ${cantidadTickets} Números de la Suerte`;
        } else {
            tituloModal.innerHTML = `<i class="ri-hashtag me-2"></i>Selecciona tu Número de la Suerte`;
        }
        
        // Abrir modal
        const modal = new bootstrap.Modal(document.getElementById('modal_seleccionar_numero'));
        modal.show();
        
        // Cargar números disponibles
        cargarNumerosDisponibles(rifaId);
    };
    
    // Cargar números desde la API
    async function cargarNumerosDisponibles(rifaId) {
        const gridContainer = document.getElementById('grid_numeros_disponibles');
        gridContainer.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div><p class="text-muted mt-2">Cargando números disponibles...</p></div>';
        
        try {
            const API_BASE_URL = window.API_BASE_URL || (window.location.origin + '/sistema_rifas/api');
            const response = await fetch(`${API_BASE_URL}/rifas/numeros/disponibles?rifa_id=${rifaId}`);
            const resultado = await response.json();
            
            if (resultado.ok && resultado.data && resultado.data.length > 0) {
                // Cargar también números vendidos y reservados para mostrar el estado completo
                const responseTodos = await fetch(`${API_BASE_URL}/rifas/numeros/get?rifa_id=${rifaId}`);
                const resultadoTodos = await responseTodos.json();
                
                if (resultadoTodos.ok && resultadoTodos.data) {
                    numerosDisponibles = resultadoTodos.data;
                    mostrarGridNumeros_Render(resultadoTodos.data);
                } else {
                    numerosDisponibles = resultado.data;
                    mostrarGridNumeros_Render(resultado.data);
                }
            } else {
                gridContainer.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No hay números disponibles en este momento.</p></div>';
            }
        } catch (error) {
            console.error('Error al cargar números disponibles:', error);
            gridContainer.innerHTML = '<div class="col-12 text-center py-5"><p class="text-danger">Error al cargar los números. Por favor, intenta nuevamente.</p></div>';
        }
    }
    
    // Generar números simulados (TEMPORAL - reemplazar con API real)
    function generarNumerosSimulados(inicio, fin, rifaId) {
        const numeros = [];
        for (let i = inicio; i <= fin; i++) {
            // Simular algunos vendidos/reservados
            let estado = 'DISPONIBLE';
            if (i % 13 === 0) estado = 'VENDIDO';
            else if (i % 17 === 0) estado = 'RESERVADO';
            else if (i % 37 === 0) estado = 'BLOQUEADO';
            
            numeros.push({
                numero_entero: i,
                numero_formateado: String(i).padStart(4, '0'),
                estado: estado,
                es_especial: (i % 50 === 0) // 50, 100, 150 son especiales
            });
        }
        numerosDisponibles = numeros;
        return numeros;
    }
    
    // Renderizar grid de números
    function mostrarGridNumeros_Render(numeros) {
        const gridContainer = document.getElementById('grid_numeros_disponibles');
        gridContainer.innerHTML = '';
        
        // Calcular estadísticas
        const disponibles = numeros.filter(n => n.estado === 'DISPONIBLE').length;
        const vendidos = numeros.filter(n => n.estado === 'VENDIDO').length;
        const reservados = numeros.filter(n => n.estado === 'RESERVADO').length;
        const porcentaje = Math.round((vendidos / numeros.length) * 100);
        
        document.getElementById('stat_disponibles').textContent = disponibles;
        document.getElementById('stat_vendidos').textContent = vendidos;
        document.getElementById('stat_reservados').textContent = reservados;
        document.getElementById('stat_porcentaje').textContent = porcentaje + '%';
        
        // Obtener cantidad requerida y números ya seleccionados
        const cantidadRequerida = parseInt(document.getElementById('cantidad_tickets').value) || 1;
        const numerosYaSeleccionados = window.numerosSeleccionados || numerosSeleccionados || [];
        const limiteAlcanzado = numerosYaSeleccionados.length >= cantidadRequerida;
        
        // Renderizar números en grid (Bootstrap col)
        numeros.forEach(num => {
            const col = document.createElement('div');
            col.className = 'col-lg-1 col-md-2 col-sm-3 col-4';
            
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = num.numero_formateado;
            button.dataset.numero = num.numero_entero;
            button.dataset.formateado = num.numero_formateado;
            button.dataset.estado = num.estado;
            
            // Verificar si el número ya está seleccionado
            const yaSeleccionado = numerosYaSeleccionados.find(n => n.entero === num.numero_entero);
            
            // Clases según estado
            if (num.estado === 'VENDIDO') {
                button.className = 'numero-btn numero-vendido';
                button.disabled = true;
            } else if (num.estado === 'RESERVADO') {
                button.className = 'numero-btn numero-reservado';
                button.disabled = true;
            } else if (num.estado === 'BLOQUEADO') {
                button.className = 'numero-btn numero-bloqueado';
                button.disabled = true;
            } else if (yaSeleccionado) {
                // Número ya seleccionado por el usuario actual
                button.className = 'numero-btn numero-seleccionado';
                button.disabled = false; // Permitir deseleccionar
                button.onclick = () => {
                    const index = numerosYaSeleccionados.findIndex(n => n.entero === num.numero_entero);
                    if (index !== -1) {
                        eliminarNumero(index);
                    }
                };
            } else if (limiteAlcanzado) {
                // Límite alcanzado, deshabilitar números disponibles
                button.className = 'numero-btn numero-disponible numero-deshabilitado';
                button.disabled = true;
                button.title = `Ya has seleccionado ${cantidadRequerida} número(s). Elimina uno para seleccionar otro.`;
            } else if (num.es_especial) {
                button.className = 'numero-btn numero-especial numero-disponible';
                button.onclick = () => seleccionarNumero(num.numero_entero, num.numero_formateado);
            } else {
                button.className = 'numero-btn numero-disponible';
                button.onclick = () => seleccionarNumero(num.numero_entero, num.numero_formateado);
            }
            
            col.appendChild(button);
            gridContainer.appendChild(col);
        });
    }
    
    // Actualizar estado de botones en el grid después de seleccionar/eliminar
    function actualizarEstadoBotonesGrid() {
        const cantidadRequerida = parseInt(document.getElementById('cantidad_tickets').value) || 1;
        const numerosYaSeleccionados = window.numerosSeleccionados || numerosSeleccionados || [];
        const limiteAlcanzado = numerosYaSeleccionados.length >= cantidadRequerida;
        
        // Obtener todos los botones de números disponibles
        const botones = document.querySelectorAll('#grid_numeros_disponibles .numero-btn.numero-disponible');
        
        botones.forEach(button => {
            const numeroEntero = parseInt(button.dataset.numero);
            const yaSeleccionado = numerosYaSeleccionados.find(n => n.entero === numeroEntero);
            
            if (yaSeleccionado) {
                // Número ya seleccionado
                button.className = 'numero-btn numero-seleccionado';
                button.disabled = false;
                button.onclick = () => {
                    const index = numerosYaSeleccionados.findIndex(n => n.entero === numeroEntero);
                    if (index !== -1) {
                        eliminarNumero(index);
                    }
                };
            } else if (limiteAlcanzado) {
                // Límite alcanzado, deshabilitar
                button.className = 'numero-btn numero-disponible numero-deshabilitado';
                button.disabled = true;
                button.title = `Ya has seleccionado ${cantidadRequerida} número(s). Elimina uno para seleccionar otro.`;
                button.onclick = null;
            } else {
                // Disponible para seleccionar
                button.className = 'numero-btn numero-disponible';
                button.disabled = false;
                button.onclick = () => seleccionarNumero(numeroEntero, button.dataset.formateado);
            }
        });
    }
    
    // Seleccionar número específico
    async function seleccionarNumero(numeroEntero, numeroFormateado) {
        // Obtener cantidad requerida actualizada del input
        const cantidadActual = parseInt(document.getElementById('cantidad_tickets').value) || 1;
        cantidadTicketsRequerida = cantidadActual;
        window.cantidadTicketsRequerida = cantidadActual;
        
        // Verificar si ya está seleccionado
        const yaSeleccionado = numerosSeleccionados.find(n => n.entero === numeroEntero);
        if (yaSeleccionado) {
            if (window.Utils && window.Utils.showAlert) {
                window.Utils.showAlert('Este número ya fue seleccionado', 'warning');
            } else {
                alert('Este número ya fue seleccionado');
            }
            return;
        }
        
        // VALIDACIÓN CRÍTICA: Prevenir selección si ya se alcanzó el límite
        if (numerosSeleccionados.length >= cantidadTicketsRequerida) {
            const mensaje = `Ya has seleccionado ${cantidadTicketsRequerida} número(s). No puedes seleccionar más. Si deseas cambiar, primero elimina algún número seleccionado.`;
            if (window.Utils && window.Utils.showAlert) {
                window.Utils.showAlert(mensaje, 'warning');
            } else {
                alert(mensaje);
            }
            return; // IMPORTANTE: Salir sin reservar el número
        }
        
        const rifaId = document.getElementById('rifa_id').value;
        const sesionId = obtenerOGenerarSesionId();
        
        try {
            const API_BASE_URL = window.API_BASE_URL || (window.location.origin + '/sistema_rifas/api');
            const response = await fetch(`${API_BASE_URL}/rifas/numeros/reservar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    rifa_id: parseInt(rifaId),
                    numeros: [numeroEntero],
                    sesion_id: sesionId
                })
            });
            
            const result = await response.json();
            
            if (result.ok && result.numeros_reservados && result.numeros_reservados.length > 0) {
                const numeroReservado = result.numeros_reservados[0];
                
                // Agregar a la lista de seleccionados (actualizar tanto local como global)
                numerosSeleccionados.push({
                    entero: numeroReservado.numero_entero,
                    formateado: numeroReservado.numero_formateado
                });
                window.numerosSeleccionados = numerosSeleccionados; // Sincronizar con global
                
                // Forzar actualización inmediata de campos ocultos ANTES de actualizar display
                const enterosArray = numerosSeleccionados.map(n => n.entero);
                const formateadosArray = numerosSeleccionados.map(n => n.formateado);
                console.log('🟢 [DEBUG] seleccionarNumero() - Actualizando campos ocultos:', {
                    enterosArray,
                    formateadosArray,
                    cantidad: numerosSeleccionados.length
                });
                document.getElementById('numeros_reservados').value = JSON.stringify(enterosArray);
                document.getElementById('numeros_formateados').value = JSON.stringify(formateadosArray);
                console.log('🟢 [DEBUG] Campos ocultos actualizados:', {
                    numeros_reservados: document.getElementById('numeros_reservados').value,
                    numeros_formateados: document.getElementById('numeros_formateados').value
                });
                
                // Actualizar display (esto también actualiza los campos ocultos, pero ya están actualizados arriba)
                actualizarDisplayNumeros();
                
                // Actualizar estado de botones en el grid (deshabilitar si se alcanzó el límite)
                actualizarEstadoBotonesGrid();
                
                // Validar tab orden DESPUÉS de actualizar display (para que los campos ocultos estén actualizados)
                setTimeout(() => {
                    console.log('🟢 [DEBUG] seleccionarNumero() - Llamando validarTabOrden() después de 200ms');
                    console.log('🟢 [DEBUG] Verificando campos antes de validar:', {
                        numeros_reservados: document.getElementById('numeros_reservados').value,
                        numeros_formateados: document.getElementById('numeros_formateados').value,
                        windowNumerosSeleccionados: window.numerosSeleccionados
                    });
                    console.log('🟢 [DEBUG] typeof window.validarTabOrden:', typeof window.validarTabOrden);
                    
                    try {
                        if (typeof window.validarTabOrden === 'function') {
                            console.log('🟢 [DEBUG] ✅ Llamando window.validarTabOrden()');
                            const resultado = window.validarTabOrden();
                            console.log('🟢 [DEBUG] ✅ validarTabOrden() ejecutada, resultado:', resultado);
                        } else if (typeof validarTabOrden === 'function') {
                            console.log('🟢 [DEBUG] ✅ Llamando validarTabOrden() local');
                            const resultado = validarTabOrden();
                            console.log('🟢 [DEBUG] ✅ validarTabOrden() ejecutada, resultado:', resultado);
                        } else {
                            console.error('❌ [DEBUG] validarTabOrden no está definida ni en window ni localmente');
                        }
                    } catch (error) {
                        console.error('❌ [DEBUG] Error al ejecutar validarTabOrden():', error);
                        console.error('❌ [DEBUG] Stack:', error.stack);
                    }
                }, 200);
                
                // Si ya completó la cantidad, cerrar modal
                if (numerosSeleccionados.length >= cantidadTicketsRequerida) {
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('modal_seleccionar_numero'));
                        if (modal) modal.hide();
                    }, 500);
                }
                
                // Iniciar temporizador si es el primer número
                if (numerosSeleccionados.length === 1) {
                    iniciarTemporizadorReserva();
                }
                
                // Mostrar notificación
                if (window.Utils && window.Utils.showToast) {
                    window.Utils.showToast(`Número ${numeroReservado.numero_formateado} reservado (${numerosSeleccionados.length}/${cantidadTicketsRequerida})`, 'success');
                } else {
                    alert(`Número ${numeroReservado.numero_formateado} reservado`);
                }
            } else {
                const mensaje = result.msj || 'No se pudo reservar el número';
                if (window.Utils && window.Utils.showAlert) {
                    window.Utils.showAlert(mensaje, 'error');
                } else {
                    alert(mensaje);
                }
            }
        } catch (error) {
            console.error('Error al reservar número:', error);
            const mensaje = 'Error al reservar el número. Por favor, intenta nuevamente.';
            if (window.Utils && window.Utils.showAlert) {
                window.Utils.showAlert(mensaje, 'error');
            } else {
                alert(mensaje);
            }
        }
    }
    
    // Actualizar el display de números seleccionados
    function actualizarDisplayNumeros() {
        // SIEMPRE usar window.numerosSeleccionados para asegurar que tenemos la versión más actualizada
        const numerosActuales = window.numerosSeleccionados || numerosSeleccionados || [];
        
        const display = document.getElementById('numero_seleccionado_display');
        const lista = document.getElementById('lista_numeros_seleccionados');
        const contador = document.getElementById('contador_numeros');
        
        if (numerosActuales.length === 0) {
            display.style.display = 'none';
            // Limpiar campos ocultos
            document.getElementById('numeros_reservados').value = '';
            document.getElementById('numeros_formateados').value = '';
            // Sincronizar variable local
            numerosSeleccionados = [];
            window.numerosSeleccionados = [];
            // Validar tab orden cuando se limpian números
            if (typeof window.validarTabOrden === 'function') {
                window.validarTabOrden();
            } else if (typeof validarTabOrden === 'function') {
                validarTabOrden();
            }
            return;
        }
        
        // Sincronizar variable local con global
        numerosSeleccionados = numerosActuales;
        
        // Mostrar display (forzar visibilidad con múltiples métodos para asegurar que se detecte)
        display.style.display = 'block';
        display.style.visibility = 'visible';
        display.removeAttribute('hidden');
        display.classList.remove('d-none');
        
        // Obtener cantidad actual del input para mantener consistencia (SIEMPRE del input, no de variables)
        const cantidadActual = parseInt(document.getElementById('cantidad_tickets').value) || 1;
        cantidadTicketsRequerida = cantidadActual;
        window.cantidadTicketsRequerida = cantidadActual;
        
        // Actualizar contador usando la cantidad del input
        contador.textContent = `${numerosActuales.length}/${cantidadActual}`;
        
        // Actualizar lista de badges
        lista.innerHTML = '';
        numerosActuales.forEach((num, index) => {
            const badge = document.createElement('div');
            badge.className = 'd-inline-flex align-items-center';
            badge.innerHTML = `
                <span class="badge bg-success fs-16 px-3 py-2 me-1">${num.formateado}</span>
                <button type="button" class="btn btn-sm btn-danger p-1" onclick="eliminarNumero(${index})" style="width: 24px; height: 24px; line-height: 1;">
                    <i class="ri-close-line" style="font-size: 12px;"></i>
                </button>
            `;
            lista.appendChild(badge);
        });
        
        // Actualizar campos ocultos (asegurar que siempre estén sincronizados)
        const enterosArray = numerosActuales.map(n => n.entero);
        const formateadosArray = numerosActuales.map(n => n.formateado);
        
        document.getElementById('numeros_reservados').value = JSON.stringify(enterosArray);
        document.getElementById('numeros_formateados').value = JSON.stringify(formateadosArray);
        
        // Sincronizar con variable global (asegurar consistencia)
        window.numerosSeleccionados = numerosActuales;
        
        // NO validar aquí - dejar que las funciones que llaman a actualizarDisplayNumeros() 
        // se encarguen de la validación después de asegurar que todo está actualizado
    }
    
    // Eliminar un número específico de la selección
    window.eliminarNumero = async function(index) {
        let confirmacion = false;
        
        if (window.Utils && window.Utils.showConfirm) {
            const result = await window.Utils.showConfirm(
                '¿Eliminar número?',
                '¿Deseas eliminar este número de tu selección?',
                'Sí, eliminar',
                'Cancelar'
            );
            confirmacion = result.isConfirmed;
        } else {
            confirmacion = confirm('¿Deseas eliminar este número de tu selección?');
        }
        
        if (confirmacion) {
            // Obtener el número que se va a eliminar para liberarlo
            const numeroAEliminar = numerosSeleccionados[index];
            
            numerosSeleccionados.splice(index, 1);
            window.numerosSeleccionados = numerosSeleccionados; // Sincronizar con global
            actualizarDisplayNumeros();
            
            // Forzar actualización inmediata de campos ocultos antes de validar
            const enterosArray = numerosSeleccionados.map(n => n.entero);
            const formateadosArray = numerosSeleccionados.map(n => n.formateado);
            document.getElementById('numeros_reservados').value = JSON.stringify(enterosArray);
            document.getElementById('numeros_formateados').value = JSON.stringify(formateadosArray);
            
            // Liberar el número en el backend si existe
            if (numeroAEliminar) {
                const rifaId = document.getElementById('rifa_id')?.value;
                const sesionId = obtenerOGenerarSesionId();
                
                if (rifaId && sesionId) {
                    try {
                        const API_BASE_URL = window.API_BASE_URL || (window.location.origin + '/sistema_rifas/api');
                        await fetch(`${API_BASE_URL}/rifas/numeros/liberar`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                rifa_id: parseInt(rifaId),
                                sesion_id: sesionId
                            })
                        });
                    } catch (error) {
                        console.warn('Error al liberar número reservado:', error);
                    }
                }
            }
            
            // Validar tab orden DESPUÉS de actualizar display
            setTimeout(() => {
                console.log('🔴 [DEBUG] eliminarNumero() - Llamando validarTabOrden() después de 200ms');
                if (typeof window.validarTabOrden === 'function') {
                    window.validarTabOrden();
                } else if (typeof validarTabOrden === 'function') {
                    validarTabOrden();
                } else {
                    console.error('❌ [DEBUG] validarTabOrden no está definida');
                }
            }, 200);
            
            // Si no quedan números, detener temporizador
            if (numerosSeleccionados.length === 0) {
                if (timerReserva) {
                    clearInterval(timerReserva);
                    timerReserva = null;
                }
            }
            
            // Actualizar estado de botones en el grid después de eliminar
            actualizarEstadoBotonesGrid();
        }
    };
    
    // Asignar múltiples números aleatorios
    window.asignarNumerosAleatorios = async function() {
        const rifaId = document.getElementById('rifa_id').value;
        const cantidadTickets = parseInt(document.getElementById('cantidad_tickets').value) || 1;
        const sesionId = obtenerOGenerarSesionId();
        
        try {
            const API_BASE_URL = window.API_BASE_URL || (window.location.origin + '/sistema_rifas/api');
            const response = await fetch(`${API_BASE_URL}/rifas/numeros/aleatorio`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    rifa_id: parseInt(rifaId),
                    cantidad: cantidadTickets,
                    sesion_id: sesionId
                })
            });
            
            const result = await response.json();
            
            if (result.ok && result.numeros && result.numeros.length > 0) {
                // Limpiar selección anterior
                numerosSeleccionados = [];
                window.numerosSeleccionados = [];
                
                // Agregar números asignados
                result.numeros.forEach(num => {
                    numerosSeleccionados.push({
                        entero: num.numero_entero,
                        formateado: num.numero_formateado
                    });
                });
                window.numerosSeleccionados = numerosSeleccionados; // Sincronizar con global
                
                // Actualizar display (esto actualiza los campos ocultos y el contador)
                cantidadTicketsRequerida = cantidadTickets;
                window.cantidadTicketsRequerida = cantidadTickets;
                
                // Forzar actualización inmediata de campos ocultos ANTES de actualizar display
                const enterosArray = numerosSeleccionados.map(n => n.entero);
                const formateadosArray = numerosSeleccionados.map(n => n.formateado);
                console.log('🟡 [DEBUG] asignarNumerosAleatorios() - Actualizando campos ocultos:', {
                    enterosArray,
                    formateadosArray,
                    cantidad: numerosSeleccionados.length
                });
                document.getElementById('numeros_reservados').value = JSON.stringify(enterosArray);
                document.getElementById('numeros_formateados').value = JSON.stringify(formateadosArray);
                console.log('🟡 [DEBUG] Campos ocultos actualizados:', {
                    numeros_reservados: document.getElementById('numeros_reservados').value,
                    numeros_formateados: document.getElementById('numeros_formateados').value
                });
                
                // Actualizar display de números
                actualizarDisplayNumeros();
                
                // Validar tab orden DESPUÉS de actualizar display (con delay para asegurar que los campos se actualicen)
                setTimeout(() => {
                    console.log('🟡 [DEBUG] asignarNumerosAleatorios() - Llamando validarTabOrden() después de 300ms');
                    console.log('🟡 [DEBUG] Verificando campos antes de validar:', {
                        numeros_reservados: document.getElementById('numeros_reservados').value,
                        numeros_formateados: document.getElementById('numeros_formateados').value,
                        windowNumerosSeleccionados: window.numerosSeleccionados
                    });
                    console.log('🟡 [DEBUG] typeof window.validarTabOrden:', typeof window.validarTabOrden);
                    console.log('🟡 [DEBUG] window.validarTabOrden:', window.validarTabOrden);
                    
                    try {
                        if (typeof window.validarTabOrden === 'function') {
                            console.log('🟡 [DEBUG] ✅ Llamando window.validarTabOrden()');
                            const resultado = window.validarTabOrden();
                            console.log('🟡 [DEBUG] ✅ validarTabOrden() ejecutada, resultado:', resultado);
                        } else if (typeof validarTabOrden === 'function') {
                            console.log('🟡 [DEBUG] ✅ Llamando validarTabOrden() local');
                            const resultado = validarTabOrden();
                            console.log('🟡 [DEBUG] ✅ validarTabOrden() ejecutada, resultado:', resultado);
                        } else {
                            console.error('❌ [DEBUG] validarTabOrden no está definida ni en window ni localmente');
                            console.error('❌ [DEBUG] typeof window.validarTabOrden:', typeof window.validarTabOrden);
                            console.error('❌ [DEBUG] typeof validarTabOrden:', typeof validarTabOrden);
                            console.error('❌ [DEBUG] Intentando llamar directamente...');
                            // Intentar llamar directamente como último recurso
                            if (window.validarTabOrden) {
                                window.validarTabOrden();
                            }
                        }
                    } catch (error) {
                        console.error('❌ [DEBUG] Error al ejecutar validarTabOrden():', error);
                        console.error('❌ [DEBUG] Stack:', error.stack);
                    }
                }, 300);
                
                // Iniciar temporizador
                iniciarTemporizadorReserva();
                
                // Mostrar notificación
                const numerosTexto = numerosSeleccionados.map(n => n.formateado).join(', ');
                mostrarNotificacion(`Se te asignaron los números: ${numerosTexto}`, 'success');
            } else {
                const mensaje = result.msj || 'No se pudieron asignar números aleatorios';
                mostrarNotificacion(mensaje, 'error');
            }
        } catch (error) {
            console.error('Error al asignar números aleatorios:', error);
            mostrarNotificacion('Error al asignar números aleatorios. Por favor, intenta nuevamente.', 'error');
        }
    };
    
    // Cancelar todas las selecciones
    window.cancelarTodasLasSelecciones = async function() {
        try {
            let confirmacion = false;
            
            if (window.Utils && window.Utils.showConfirm) {
                try {
                    const result = await window.Utils.showConfirm(
                        '¿Limpiar selección?',
                        '¿Estás seguro de que quieres limpiar todos los números seleccionados?',
                        'Sí, limpiar',
                        'Cancelar'
                    );
                    confirmacion = result && result.isConfirmed === true;
                } catch (error) {
                    // Si hay error con SweetAlert, usar confirm nativo
                    console.warn('Error al mostrar confirmación:', error);
                    confirmacion = confirm('¿Estás seguro de que quieres limpiar todos los números seleccionados?');
                }
            } else {
                confirmacion = confirm('¿Estás seguro de que quieres limpiar todos los números seleccionados?');
            }
            
            if (confirmacion) {
                // Limpiar selección (tanto local como global)
                numerosSeleccionados = [];
                window.numerosSeleccionados = [];
                
                // Limpiar campos ocultos
                const numerosReservados = document.getElementById('numeros_reservados');
                const numerosFormateados = document.getElementById('numeros_formateados');
                const display = document.getElementById('numero_seleccionado_display');
                
                if (numerosReservados) numerosReservados.value = '';
                if (numerosFormateados) numerosFormateados.value = '';
                
                // Ocultar display
                if (display) display.style.display = 'none';
                
                // Validar tab orden para deshabilitar botón continuar
                if (typeof window.validarTabOrden === 'function') {
                    try {
                        window.validarTabOrden();
                    } catch (error) {
                        console.warn('Error al validar tab orden:', error);
                    }
                } else if (typeof validarTabOrden === 'function') {
                    try {
                        validarTabOrden();
                    } catch (error) {
                        console.warn('Error al validar tab orden:', error);
                    }
                }
                
                // Detener temporizador
                if (timerReserva) {
                    clearInterval(timerReserva);
                    timerReserva = null;
                }
                
                // Liberar números reservados en el backend
                const rifaId = document.getElementById('rifa_id')?.value;
                const sesionId = obtenerOGenerarSesionId();
                
                if (rifaId && sesionId) {
                    try {
                        const API_BASE_URL = window.API_BASE_URL || (window.location.origin + '/sistema_rifas/api');
                        await fetch(`${API_BASE_URL}/rifas/numeros/liberar`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                rifa_id: parseInt(rifaId),
                                sesion_id: sesionId
                            })
                        });
                        // No mostrar error si falla, solo loguear
                    } catch (error) {
                        console.warn('Error al liberar números reservados:', error);
                    }
                }
            }
        } catch (error) {
            console.error('Error en cancelarTodasLasSelecciones:', error);
            // Aún así, limpiar la selección si hay un error
            try {
                numerosSeleccionados = [];
                window.numerosSeleccionados = [];
                const numerosReservados = document.getElementById('numeros_reservados');
                const numerosFormateados = document.getElementById('numeros_formateados');
                const display = document.getElementById('numero_seleccionado_display');
                
                if (numerosReservados) numerosReservados.value = '';
                if (numerosFormateados) numerosFormateados.value = '';
                if (display) display.style.display = 'none';
                
                if (timerReserva) {
                    clearInterval(timerReserva);
                    timerReserva = null;
                }
                
                // Intentar liberar números reservados en el backend
                const rifaId = document.getElementById('rifa_id')?.value;
                const sesionId = obtenerOGenerarSesionId();
                
                if (rifaId && sesionId) {
                    try {
                        const API_BASE_URL = window.API_BASE_URL || (window.location.origin + '/sistema_rifas/api');
                        await fetch(`${API_BASE_URL}/rifas/numeros/liberar`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                rifa_id: parseInt(rifaId),
                                sesion_id: sesionId
                            })
                        });
                    } catch (error) {
                        console.warn('Error al liberar números reservados:', error);
                    }
                }
            } catch (cleanupError) {
                console.error('Error al limpiar:', cleanupError);
            }
        }
    };
    
    // Iniciar temporizador de reserva (10 minutos)
    function iniciarTemporizadorReserva() {
        tiempoRestante = 600; // 10 minutos
        
        // Limpiar temporizador anterior si existe
        if (timerReserva) {
            clearInterval(timerReserva);
        }
        
        // Actualizar cada segundo
        timerReserva = setInterval(() => {
            tiempoRestante--;
            
            // Formatear tiempo
            const minutos = Math.floor(tiempoRestante / 60);
            const segundos = tiempoRestante % 60;
            const tiempoFormateado = minutos + ':' + String(segundos).padStart(2, '0');
            
            document.getElementById('timer_reserva').textContent = tiempoFormateado;
            
            // Si el tiempo se agotó
            if (tiempoRestante <= 0) {
                clearInterval(timerReserva);
                if (window.Utils && window.Utils.showAlert) {
                    window.Utils.showAlert('Tu reserva ha expirado. Por favor, selecciona otro número.', 'warning').then(() => {
                        cancelarTodasLasSelecciones();
                        location.reload();
                    });
                } else {
                    alert('⏰ Tu reserva ha expirado. Por favor, selecciona otro número.');
                    cancelarTodasLasSelecciones();
                    location.reload();
                }
            }
            
            // Alerta a los 2 minutos
            if (tiempoRestante === 120) {
                mostrarNotificacion('Solo quedan 2 minutos de tu reserva', 'warning');
            }
        }, 1000);
    }
    
    // Buscar número específico
    window.buscarNumero = function() {
        const busqueda = document.getElementById('buscar_numero').value.trim();
        if (busqueda === '') {
            mostrarNotificacion('Ingresa un número para buscar', 'warning');
            return;
        }
        
        const numeroEncontrado = numerosDisponibles.find(n => 
            n.numero_formateado.includes(busqueda) || 
            String(n.numero_entero) === busqueda
        );
        
        if (!numeroEncontrado) {
            mostrarNotificacion(`No se encontró el número: ${busqueda}`, 'error');
            return;
        }
        
        // Hacer scroll al número
        const grid = document.getElementById('grid_numeros_disponibles');
        const buttons = grid.querySelectorAll('button');
        buttons.forEach(btn => {
            if (btn.dataset.numero == numeroEncontrado.numero_entero) {
                btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                btn.classList.add('animate__animated', 'animate__pulse');
                setTimeout(() => {
                    btn.classList.remove('animate__animated', 'animate__pulse');
                }, 1000);
            }
        });
    };
    
    // Filtrar números
    window.filtrarNumeros = function(filtro) {
        let numerosFiltrados = [...numerosDisponibles];
        
        if (filtro === 'pares') {
            numerosFiltrados = numerosFiltrados.filter(n => n.numero_entero % 2 === 0);
        } else if (filtro === 'impares') {
            numerosFiltrados = numerosFiltrados.filter(n => n.numero_entero % 2 !== 0);
        } else if (filtro === 'multiplos5') {
            numerosFiltrados = numerosFiltrados.filter(n => n.numero_entero % 5 === 0);
        }
        
        mostrarGridNumeros_Render(numerosFiltrados);
    };
    
    // Obtener o generar ID de sesión único
    function obtenerOGenerarSesionId() {
        let sesionId = sessionStorage.getItem('session_id_rifas');
        
        if (!sesionId) {
            sesionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('session_id_rifas', sesionId);
        }
        
        return sesionId;
    }
    
    // Mostrar notificación usando Utils.js (SweetAlert/Toast)
    function mostrarNotificacion(mensaje, tipo) {
        if (window.Utils) {
            if (tipo === 'error' || tipo === 'warning') {
                // Para errores y advertencias usar showAlert
                window.Utils.showAlert(mensaje, tipo);
            } else {
                // Para éxito e info usar showToast
                window.Utils.showToast(mensaje, tipo === 'success' ? 'success' : 'info');
            }
        } else {
            // Fallback si Utils no está disponible
            if (tipo === 'error') {
                alert('❌ ' + mensaje);
            } else if (tipo === 'warning') {
                alert('⚠️ ' + mensaje);
            } else {
                console.log('✅ ' + mensaje);
            }
        }
    }
    
    // Actualizar resumen cuando se selecciona número
    document.addEventListener('DOMContentLoaded', function() {
        // Observer para limpiar selecciones cuando se cambia la cantidad de tickets
        const cantidadTicketsInput = document.getElementById('cantidad_tickets');
        if (cantidadTicketsInput) {
            cantidadTicketsInput.addEventListener('change', async function() {
                // Si cambia la cantidad, limpiar números seleccionados
                if (numerosSeleccionados.length > 0) {
                    const nuevaCantidad = parseInt(this.value);
                    if (nuevaCantidad !== numerosSeleccionados.length) {
                        let confirmacion = false;
                        
                        if (window.Utils && window.Utils.showConfirm) {
                            const result = await window.Utils.showConfirm(
                                '¿Cambiar cantidad?',
                                'Al cambiar la cantidad de tickets se limpiarán los números seleccionados. ¿Continuar?',
                                'Sí, continuar',
                                'Cancelar'
                            );
                            confirmacion = result.isConfirmed;
                        } else {
                            confirmacion = confirm('Al cambiar la cantidad de tickets se limpiarán los números seleccionados. ¿Continuar?');
                        }
                        
                        if (confirmacion) {
                            cancelarTodasLasSelecciones();
                        } else {
                            this.value = numerosSeleccionados.length;
                        }
                    }
                }
            });
        }
    });
})();

