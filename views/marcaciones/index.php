<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Control de Marcaciones - CAFED</title>
    
    <!-- Bootstrap CSS -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link href="../../assets/css/icons.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../../assets/css/app.min.css" rel="stylesheet">
    
    <style>
        .stats-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .stats-card.success {
            border-left-color: #28a745;
        }
        .stats-card.warning {
            border-left-color: #ffc107;
        }
        .stats-card.danger {
            border-left-color: #dc3545;
        }
        .stats-card.info {
            border-left-color: #17a2b8;
        }
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .loading-overlay.active {
            display: flex;
        }
    </style>
</head>
<body>
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Cargando...</span>
        </div>
    </div>
    
    <div class="container-fluid py-4">
        
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="mb-0">
                            <i class="ri-fingerprint-line"></i> Control de Marcaciones Biométricas
                        </h2>
                        <p class="text-muted">Sincronización automática con dispositivos biométricos</p>
                    </div>
                    <div>
                        <button class="btn btn-primary" id="btnProcesarLogs">
                            <i class="ri-refresh-line"></i> Sincronizar Ahora
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtros -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="formFiltros" class="row g-3">
                            <div class="col-md-3">
                                <label for="sedeSelect" class="form-label">Sede</label>
                                <select class="form-select" id="sedeSelect" name="sede_id">
                                    <option value="1">Sede Principal</option>
                                    <!-- Cargar dinámicamente -->
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fechaInicio" name="fecha_inicio">
                            </div>
                            <div class="col-md-3">
                                <label for="fechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fechaFin" name="fecha_fin">
                            </div>
                            <div class="col-md-3">
                                <label for="empleadoSelect" class="form-label">Empleado (Opcional)</label>
                                <select class="form-select" id="empleadoSelect" name="empleado_id">
                                    <option value="">Todos los empleados</option>
                                    <!-- Cargar dinámicamente -->
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="button" class="btn btn-success" id="btnCargarMarcaciones">
                                    <i class="ri-search-line"></i> Buscar Marcaciones
                                </button>
                                <button type="button" class="btn btn-info" id="btnVerResumen">
                                    <i class="ri-bar-chart-box-line"></i> Ver Resumen
                                </button>
                                <button type="button" class="btn btn-warning" id="btnLogsPendientes">
                                    <i class="ri-time-line"></i> Logs Pendientes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas rápidas -->
        <div class="row mb-4" id="statsContainer">
            <div class="col-md-3">
                <div class="card stats-card success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-2">Total Presentes</p>
                                <h3 class="mb-0" id="statPresentes">0</h3>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-success-subtle text-success rounded-3">
                                    <i class="ri-check-line fs-1"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-2">Tardanzas</p>
                                <h3 class="mb-0" id="statTardanzas">0</h3>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-warning-subtle text-warning rounded-3">
                                    <i class="ri-time-line fs-1"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card danger">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-2">Faltas</p>
                                <h3 class="mb-0" id="statFaltas">0</h3>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-danger-subtle text-danger rounded-3">
                                    <i class="ri-close-line fs-1"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <p class="text-muted mb-2">Logs Pendientes</p>
                                <h3 class="mb-0" id="statPendientes">0</h3>
                            </div>
                            <div class="avatar-sm">
                                <span class="avatar-title bg-info-subtle text-info rounded-3">
                                    <i class="ri-database-2-line fs-1"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla de marcaciones -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-list-check-2"></i> Marcaciones Registradas
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped" id="tablaMarcaciones">
                                <thead class="table-light">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Empleado</th>
                                        <th>Documento</th>
                                        <th>Cargo</th>
                                        <th>Hora Entrada</th>
                                        <th>Hora Salida</th>
                                        <th>Horas Trabajadas</th>
                                        <th>Estado</th>
                                        <th>Tardanza</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="10" class="text-center">
                                            <p class="text-muted my-3">
                                                <i class="ri-information-line"></i>
                                                Selecciona un rango de fechas y haz clic en "Buscar Marcaciones"
                                            </p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Resumen de asistencia -->
        <div class="row mt-4" id="resumenSection" style="display: none;">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="ri-pie-chart-line"></i> Resumen de Asistencia
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row" id="resumenContainer">
                            <!-- Se carga dinámicamente -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Modal Logs Pendientes -->
    <div class="modal fade" id="modalLogsPendientes" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri-database-2-line"></i> Logs Pendientes de Procesar
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Código Biométrico</th>
                                    <th>Empleado</th>
                                    <th>Fecha/Hora</th>
                                    <th>Tipo Evento</th>
                                    <th>Dispositivo</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyLogsPendientes">
                                <!-- Se carga dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="procesarLogsManual()">
                        <i class="ri-refresh-line"></i> Procesar Ahora
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="../../assets/libs/jquery/jquery.min.js"></script>
    <script src="../../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 (opcional) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Módulo de Marcaciones -->
    <script src="marcaciones.js"></script>
    
    <script>
        // Sobrescribir funciones de UI
        function mostrarLoading(mensaje = 'Cargando...') {
            document.getElementById('loadingOverlay').classList.add('active');
        }
        
        function ocultarLoading() {
            document.getElementById('loadingOverlay').classList.remove('active');
        }
        
        // Establecer fechas por defecto (hoy)
        document.addEventListener('DOMContentLoaded', function() {
            const hoy = new Date().toISOString().split('T')[0];
            document.getElementById('fechaInicio').value = hoy;
            document.getElementById('fechaFin').value = hoy;
            
            // Event listeners adicionales
            document.getElementById('btnCargarMarcaciones').addEventListener('click', async function() {
                const sedeId = document.getElementById('sedeSelect').value;
                const fechaInicio = document.getElementById('fechaInicio').value;
                const fechaFin = document.getElementById('fechaFin').value;
                const empleadoId = document.getElementById('empleadoSelect').value || null;
                
                await cargarMarcaciones(sedeId, fechaInicio, fechaFin, empleadoId);
            });
            
            document.getElementById('btnVerResumen').addEventListener('click', async function() {
                const sedeId = document.getElementById('sedeSelect').value;
                const fechaInicio = document.getElementById('fechaInicio').value;
                const fechaFin = document.getElementById('fechaFin').value;
                
                document.getElementById('resumenSection').style.display = 'block';
                await cargarResumenAsistencia(sedeId, fechaInicio, fechaFin);
            });
            
            document.getElementById('btnLogsPendientes').addEventListener('click', async function() {
                const sedeId = document.getElementById('sedeSelect').value;
                await verLogsPendientes(sedeId);
            });
            
            // Cargar marcaciones del día automáticamente
            cargarMarcacionesDelDia();
            
            // Auto-actualizar cada 30 segundos
            setInterval(() => {
                const sedeId = document.getElementById('sedeSelect').value;
                const fechaInicio = document.getElementById('fechaInicio').value;
                const fechaFin = document.getElementById('fechaFin').value;
                
                // Solo actualizar si está visible
                if (document.visibilityState === 'visible') {
                    cargarMarcaciones(sedeId, fechaInicio, fechaFin);
                }
            }, 30000);
        });
        
        // Función para mostrar modal de logs pendientes
        async function verLogsPendientes(sedeId) {
            try {
                mostrarLoading('Cargando logs pendientes...');
                
                const resultado = await MarcacionesAPI.obtenerLogsPendientes(sedeId, 50);
                
                if (resultado.success) {
                    const tbody = document.getElementById('tbodyLogsPendientes');
                    tbody.innerHTML = '';
                    
                    if (resultado.data.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay logs pendientes</td></tr>';
                    } else {
                        resultado.data.forEach(log => {
                            const row = `
                                <tr>
                                    <td>${log.biometric_user_id}</td>
                                    <td>${log.nombre_completo || '<span class="text-danger">Sin mapear</span>'}</td>
                                    <td>${log.evento_at}</td>
                                    <td><span class="badge bg-info">${log.evento_tipo || 'N/A'}</span></td>
                                    <td>${log.device_id || 'N/A'}</td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                        });
                    }
                    
                    // Actualizar contador
                    document.getElementById('statPendientes').textContent = resultado.total;
                    
                    // Mostrar modal
                    const modal = new bootstrap.Modal(document.getElementById('modalLogsPendientes'));
                    modal.show();
                } else {
                    mostrarMensaje('error', resultado.mensaje);
                }
            } catch (error) {
                mostrarMensaje('error', 'Error al obtener logs pendientes');
                console.error(error);
            } finally {
                ocultarLoading();
            }
        }
    </script>
    
</body>
</html>


