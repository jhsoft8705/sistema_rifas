/**
 * Sistema de Gestión de Empleados - Formulario de Registro
 * CAFED - Control de Asistencia
 */

// Variables globales
let map;
let marker;
let tabsValidation = {
    1: false,
    2: false,
    3: false,
    4: false,
    5: false
};

// Inicialización cuando el documento esté listo
$(document).ready(function() {
    initializeFormValidation();
    initializeImagePreview();
    initializeFormSubmit();
    
    // Validar tabs al cambiar entre ellos
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const targetTab = $(e.target).attr('href');
        const tabNumber = getTabNumber(targetTab);
        validateCurrentTab(tabNumber);
    });
    
    // Validar campos en tiempo real
    $('#form_empleado input, #form_empleado select').on('change blur', function() {
        validateAllTabs();
    });
});

/**
 * Obtener número de tab desde el ID
 */
function getTabNumber(tabId) {
    const mapping = {
        '#tab_datos_personales': 1,
        '#tab_datos_laborales': 2,
        '#tab_datos_bancarios': 3,
        '#tab_direccion': 4,
        '#tab_adicional': 5
    };
    return mapping[tabId] || 1;
}

/**
 * Inicializar validación del formulario
 */
function initializeFormValidation() {
    // Añadir clase para Bootstrap validation
    const forms = document.querySelectorAll('.needs-validation');
    
    // Validar todos los tabs al inicio
    validateAllTabs();
}

/**
 * Validar todos los tabs
 */
function validateAllTabs() {
    validateTab1(); // Datos Personales
    validateTab2(); // Datos Laborales
    validateTab3(); // Datos Bancarios
    validateTab4(); // Dirección
    validateTab5(); // Adicional
    
    updateTabsVisualState();
}

/**
 * Validar Tab 1: Datos Personales
 */
function validateTab1() {
    const requiredFields = [
        'tipo_documento',
        'nro_documento',
        'fecha_nacimiento',
        'nombre',
        'apellido_paterno',
        'sexo',
        'estado_civil',
        'profesion',
        'grado_instruccion',
        'email',
        'telefono'
    ];
    
    tabsValidation[1] = validateTabFields(requiredFields);
}

/**
 * Validar Tab 2: Datos Laborales
 */
function validateTab2() {
    const requiredFields = [
        'fecha_ingreso',
        'cargo',
        'unidad_organizacional',
        'regimen_laboral',
        'tipo_trabajador',
        'nivel_remunerativo',
        'sistema_pension',
        'codigo_reloj'
    ];
    
    tabsValidation[2] = validateTabFields(requiredFields);
}

/**
 * Validar Tab 3: Datos Bancarios
 */
function validateTab3() {
    const requiredFields = [
        'banco',
        'numero_cuenta',
        'numero_cci'
    ];
    
    tabsValidation[3] = validateTabFields(requiredFields);
}

/**
 * Validar Tab 4: Dirección
 */
function validateTab4() {
    const requiredFields = [
        'direccion',
        'distrito',
        'provincia',
        'departamento'
    ];
    
    tabsValidation[4] = validateTabFields(requiredFields);
}

/**
 * Validar Tab 5: Información Adicional
 */
function validateTab5() {
    const requiredFields = [
        'estado'
    ];
    
    tabsValidation[5] = validateTabFields(requiredFields);
}

/**
 * Validar campos de un tab
 */
function validateTabFields(fieldIds) {
    let isValid = true;
    
    fieldIds.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field && field.hasAttribute('required')) {
            if (!field.value || field.value.trim() === '') {
                isValid = false;
            }
            
            // Validación especial para email
            if (fieldId === 'email' && field.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value)) {
                    isValid = false;
                }
            }
        }
    });
    
    return isValid;
}

/**
 * Actualizar estado visual de los tabs
 */
function updateTabsVisualState() {
    for (let i = 1; i <= 5; i++) {
        const tabLink = document.getElementById(`tab-link-${i}`);
        
        if (tabLink) {
            // Remover clases previas
            tabLink.classList.remove('tab-invalid', 'tab-valid');
            
            // Añadir clase según validación
            if (tabsValidation[i]) {
                tabLink.classList.add('tab-valid');
            } else {
                tabLink.classList.add('tab-invalid');
            }
        }
    }
}

/**
 * Validar tab actual antes de continuar
 */
function validateCurrentTab(tabNumber) {
    switch(tabNumber) {
        case 1:
            validateTab1();
            break;
        case 2:
            validateTab2();
            break;
        case 3:
            validateTab3();
            break;
        case 4:
            validateTab4();
            break;
        case 5:
            validateTab5();
            break;
    }
    updateTabsVisualState();
}

/**
 * Ir al siguiente tab
 */
function nextTab(tabNumber) {
    // Validar tab actual
    const currentTab = tabNumber - 1;
    validateCurrentTab(currentTab);
    
    // Verificar si el tab actual es válido antes de continuar
    if (!tabsValidation[currentTab]) {
        Swal.fire({
            title: 'Campos incompletos',
            text: 'Por favor complete todos los campos requeridos antes de continuar',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
        return;
    }
    
    // Cambiar al siguiente tab
    const nextTabLink = document.querySelector(`a[href="#tab_${getTabIdByNumber(tabNumber)}"]`);
    if (nextTabLink) {
        const tab = new bootstrap.Tab(nextTabLink);
        tab.show();
    }
}

/**
 * Ir al tab anterior
 */
function prevTab(tabNumber) {
    const prevTabLink = document.querySelector(`a[href="#tab_${getTabIdByNumber(tabNumber)}"]`);
    if (prevTabLink) {
        const tab = new bootstrap.Tab(prevTabLink);
        tab.show();
    }
}

/**
 * Obtener ID del tab por número
 */
function getTabIdByNumber(tabNumber) {
    const mapping = {
        1: 'datos_personales',
        2: 'datos_laborales',
        3: 'datos_bancarios',
        4: 'direccion',
        5: 'adicional'
    };
    return mapping[tabNumber] || 'datos_personales';
}

/**
 * Inicializar preview de imagen
 */
function initializeImagePreview() {
    const fotoInput = document.getElementById('foto_empleado');
    const previewImg = document.getElementById('preview_foto');
    
    if (fotoInput) {
        fotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // Validar tipo de archivo
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire({
                        title: 'Tipo de archivo no válido',
                        text: 'Solo se permiten archivos JPG, JPEG o PNG',
                        icon: 'error'
                    });
                    fotoInput.value = '';
                    return;
                }
                
                // Validar tamaño (2MB máximo)
                const maxSize = 2 * 1024 * 1024; // 2MB en bytes
                if (file.size > maxSize) {
                    Swal.fire({
                        title: 'Archivo muy grande',
                        text: 'El tamaño máximo permitido es 2MB',
                        icon: 'error'
                    });
                    fotoInput.value = '';
                    return;
                }
                
                // Mostrar preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

/**
 * Inicializar Google Maps
 */
function initMap() {
    // Coordenadas por defecto (Lima, Perú)
    const defaultLocation = { lat: -12.046374, lng: -77.042793 };
    
    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 12,
        mapTypeControl: true,
        streetViewControl: false
    });
    
    // Crear marcador
    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: true,
        title: 'Ubicación del empleado'
    });
    
    // Evento al mover el marcador
    marker.addListener('dragend', function(event) {
        updateCoordinates(event.latLng.lat(), event.latLng.lng());
    });
    
    // Evento al hacer clic en el mapa
    map.addListener('click', function(event) {
        marker.setPosition(event.latLng);
        updateCoordinates(event.latLng.lat(), event.latLng.lng());
    });
}

/**
 * Abrir Google Maps
 */
function abrirGoogleMaps() {
    const mapContainer = document.getElementById('map');
    
    if (mapContainer.style.display === 'none') {
        mapContainer.style.display = 'block';
        
        // Inicializar mapa si no está inicializado
        if (!map) {
            initMap();
        }
        
        // Intentar obtener la ubicación actual del usuario
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    map.setCenter(userLocation);
                    marker.setPosition(userLocation);
                    updateCoordinates(userLocation.lat, userLocation.lng);
                },
                function(error) {
                    console.log('Error obteniendo ubicación:', error);
                }
            );
        }
    } else {
        mapContainer.style.display = 'none';
    }
}

/**
 * Actualizar coordenadas en los campos
 */
function updateCoordinates(lat, lng) {
    document.getElementById('latitud').value = lat.toFixed(6);
    document.getElementById('longitud').value = lng.toFixed(6);
}

/**
 * Inicializar envío del formulario
 */
function initializeFormSubmit() {
    const form = document.getElementById('form_empleado');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validar todos los tabs
            validateAllTabs();
            
            // Verificar que todos los tabs estén completos
            const allTabsValid = Object.values(tabsValidation).every(valid => valid === true);
            
            if (!allTabsValid) {
                Swal.fire({
                    title: 'Formulario incompleto',
                    html: 'Algunos tabs tienen campos incompletos. Por favor revise:<br><br>' + 
                          getInvalidTabsMessage(),
                    icon: 'error',
                    confirmButtonText: 'Revisar'
                });
                return;
            }
            
            // Si todo está válido, proceder con el envío
            submitForm();
        });
    }
}

/**
 * Obtener mensaje de tabs inválidos
 */
function getInvalidTabsMessage() {
    const tabNames = {
        1: 'Datos Personales',
        2: 'Datos Laborales',
        3: 'Datos Bancarios',
        4: 'Dirección y Ubicación',
        5: 'Información Adicional'
    };
    
    let message = '<ul class="text-start">';
    for (let i = 1; i <= 5; i++) {
        if (!tabsValidation[i]) {
            message += `<li class="text-danger"><strong>${tabNames[i]}</strong></li>`;
        }
    }
    message += '</ul>';
    
    return message;
}

/**
 * Enviar formulario
 */
function submitForm() {
    // Mostrar loading
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espere mientras se registra el empleado',
        icon: 'info',
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // Preparar FormData
    const form = document.getElementById('form_empleado');
    const formData = new FormData(form);
    
    // Aquí iría la llamada AJAX al servidor
    // Por ahora simularemos el guardado
    setTimeout(function() {
        Swal.fire({
            title: '¡Éxito!',
            text: 'El empleado ha sido registrado correctamente',
            icon: 'success',
            confirmButtonText: 'Aceptar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirigir a la lista de empleados
                window.location.href = getBaseUrl() + '/empleados';
            }
        });
        
        // Ejemplo de llamada AJAX real:
        /*
        $.ajax({
            url: getBaseUrl() + '/api/empleados/crear',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: 'El empleado ha sido registrado correctamente',
                    icon: 'success',
                    confirmButtonText: 'Aceptar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = getBaseUrl() + '/empleados';
                    }
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'Error',
                    text: 'Ocurrió un error al guardar el empleado: ' + error,
                    icon: 'error',
                    confirmButtonText: 'Aceptar'
                });
            }
        });
        */
    }, 2000);
}

/**
 * Obtener URL base
 */
function getBaseUrl() {
    const path = window.location.pathname;
    const parts = path.split('/');
    const basePath = parts.slice(0, parts.indexOf('empleados')).join('/');
    return window.location.origin + basePath;
}

/**
 * Limpiar formulario
 */
function resetForm() {
    document.getElementById('form_empleado').reset();
    document.getElementById('preview_foto').src = getBaseUrl() + '/assets/images/users/user-dummy-img.jpg';
    document.getElementById('latitud').value = '';
    document.getElementById('longitud').value = '';
    
    // Ocultar mapa
    document.getElementById('map').style.display = 'none';
    
    // Resetear validación de tabs
    tabsValidation = {
        1: false,
        2: false,
        3: false,
        4: false,
        5: false
    };
    
    updateTabsVisualState();
    
    // Volver al primer tab
    const firstTab = document.querySelector('a[href="#tab_datos_personales"]');
    if (firstTab) {
        const tab = new bootstrap.Tab(firstTab);
        tab.show();
    }
}

