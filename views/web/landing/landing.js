document.addEventListener('DOMContentLoaded', () => {
    const rifas = window.LANDING_RIFAS || [];
    const sectionNumeros = document.getElementById('section_numeros');
    const gridNumbers = document.getElementById('grid_numbers');
    const gridPlaceholder = document.getElementById('grid_placeholder');
    const formCliente = document.getElementById('form_cliente');
    const selectedList = document.getElementById('selected_numbers_list');
    const totalPago = document.getElementById('total_pago');
    const nombreDisplay = document.getElementById('rifa_nombre_display');
    const precioDisplay = document.getElementById('rifa_precio_display');

    let rifaSeleccionada = null;
    let numerosSeleccionados = [];

    document.querySelectorAll('.seleccionar-rifa').forEach(btn => {
        btn.addEventListener('click', (event) => {
            const card = event.currentTarget.closest('.rifa-card');
            const rifa = JSON.parse(card.getAttribute('data-rifa'));
            seleccionarRifa(rifa);
        });
    });

    formCliente.addEventListener('submit', (event) => {
        event.preventDefault();
        if (!formCliente.checkValidity()) {
            formCliente.classList.add('was-validated');
            return;
        }

        if (!rifaSeleccionada) {
            alert('Selecciona una rifa.');
            return;
        }

        if (numerosSeleccionados.length === 0) {
            alert('Selecciona al menos un número.');
            return;
        }

        const payload = {
            rifa_id: rifaSeleccionada.id,
            numeros: numerosSeleccionados,
            total: (numerosSeleccionados.length * parseFloat(rifaSeleccionada.precio_ticket)).toFixed(2),
            cliente: {
                nombre: document.getElementById('cliente_nombre').value.trim(),
                email: document.getElementById('cliente_email').value.trim(),
                telefono: document.getElementById('cliente_telefono').value.trim(),
                comentarios: document.getElementById('cliente_comentarios').value.trim()
            }
        };

        console.log('Reserva generada (integrar API):', payload);
        alert('¡Gracias! Hemos recibido tu pedido. Te contactaremos para confirmar tu compra.');

        formCliente.reset();
        formCliente.classList.remove('was-validated');
        numerosSeleccionados = [];
        actualizarResumen();
    });

    function seleccionarRifa(rifa) {
        rifaSeleccionada = rifa;
        numerosSeleccionados = [];
        nombreDisplay.textContent = `${rifa.codigo} - ${rifa.nombre}`;
        precioDisplay.textContent = `S/ ${parseFloat(rifa.precio_ticket).toFixed(2)}`;
        sectionNumeros.classList.remove('d-none');
        renderizarGrid(rifa);
        actualizarResumen();
    }

    function renderizarGrid(rifa) {
        gridNumbers.innerHTML = '';
        gridPlaceholder.classList.add('d-none');

        const inicio = parseInt(rifa.numero_inicial, 10);
        const fin = parseInt(rifa.numero_final, 10);
        const digitos = rifa.cantidad_digitos || 4;
        const prefijo = rifa.prefijo_numero || '';
        const sufijo = rifa.sufijo_numero || '';
        const maxPorPersona = rifa.cantidad_maxima_por_persona || 5;

        for (let numero = inicio; numero <= fin; numero++) {
            const numeroFormateado = prefijo + numero.toString().padStart(digitos, '0') + sufijo;

            const div = document.createElement('div');
            div.className = 'grid-number';
            div.dataset.numero = numeroFormateado;
            div.innerHTML = `<strong>${numeroFormateado}</strong><small class="text-muted">Disponible</small>`;

            div.addEventListener('click', () => {
                if (div.classList.contains('sold')) return;

                const yaSeleccionado = numerosSeleccionados.includes(numeroFormateado);
                if (yaSeleccionado) {
                    numerosSeleccionados = numerosSeleccionados.filter(n => n !== numeroFormateado);
                    div.classList.remove('selected');
                } else {
                    if (numerosSeleccionados.length >= maxPorPersona) {
                        alert(`Puedes seleccionar hasta ${maxPorPersona} número(s) por compra.`);
                        return;
                    }
                    numerosSeleccionados.push(numeroFormateado);
                    div.classList.add('selected');
                }
                actualizarResumen();
            });

            gridNumbers.appendChild(div);
        }
    }

    function actualizarResumen() {
        selectedList.innerHTML = '';
        if (numerosSeleccionados.length === 0) {
            selectedList.innerHTML = '<span class="text-muted">Ninguno</span>';
        } else {
            numerosSeleccionados.forEach(numero => {
                const span = document.createElement('span');
                span.textContent = numero;
                selectedList.appendChild(span);
            });
        }

        if (rifaSeleccionada) {
            const total = numerosSeleccionados.length * parseFloat(rifaSeleccionada.precio_ticket || 0);
            totalPago.value = `S/ ${total.toFixed(2)}`;
        } else {
            totalPago.value = 'S/ 0.00';
        }
    }
});


