Sprint 0 – Kickoff & Base Técnica (1 semana)
Revisar y ajustar config/conexion.php, rutas globales y autenticación.
Montar fixture inicial de sedes/roles/usuarios en MySQL (bd_rifas_mysql.sql).
Definir estructura de carpetas, componentes compartidos, helpers y librerías JS.
Preparar doc breve de estilo de código, naming y convención de mensajes API.
Configurar jobs básicos (backup DB, migraciones, resets de data demo).

Sprint 1 – Catálogo Base (2 semanas)
sedes, configuracion_sede, ubicaciones_rifa (procedures, modelos, vistas admin).
roles, permisos, rol_permisos, usuario_roles, usuario_permisos.
usuarios + módulo de sesiones y auditoría básica.
Vistas admin: gestión multi-sede, usuarios y roles.
Endpoint para Auth (login/logout, recuperación, bloqueo).
Smoke tests postman / colección inicial, documentación de endpoints.

Sprint 2 – Catálogo de Premios (1,5 semanas)
categorias_premios, premios con CRUD completo (imágenes, destacados).
Integrar carga y validación de imágenes (paths, storage).
Permisos específicos por rol.
Vistas admin: listado, filtros, modal, import masivo opcional.
API pública de lectura (api/public/premios).
Actualizar landing (views/web/index.php) para consumir nuevos endpoints.

Sprint 3 – Configuración de Pagos (1 semana)
metodos_pago, estados_ticket (CRUD admin).
Presentar métodos por sede y exponerlos públicamente para la compra.
Validaciones de negocio (requiere comprobante, campos obligatorios).
Vistas admin: cards de métodos de pago, panel para estados de ticket.
Endpoints públicos: consulta de métodos de pago activos.
QA manual y ajustes de seguridad (no exponer información sensible).

Sprint 4 – Gestión de Rifas (2 semanas)
rifas: procedimientos para crear, clonar, publicar, cerrar.
Relación con premios y ubicaciones_rifa.
Controles de estado (BORRADOR, PUBLICADA, etc.), fechas y cupos.
Vista admin: tablero de rifas (kanban por estado), formulario avanzado.
API pública: listar rifas activas, detalles prominentes.
Actualizar landing con timelines, contadores y reglas.

Sprint 5 – Venta y Validación de Tickets (2 semanas)
tickets, comprobantes_pago (alta, carga de comprobante, validación manual/automática).
Integración con metodos_pago, estados y notificaciones básicas.
Vistas admin: bandeja de validación (tabla, filtros avanzados, aprobar/rechazar).
Endpoint público: registro de ticket (formulario de compra) + subir comprobante.
Mecanismo de generación de codigo_ticket y correo de confirmación (si aplica).
Pruebas de estrés básicas al endpoint de compra.

Sprint 6 – Participantes y Sorteos (2 semanas)
Procedures para participantes, intentos_sorteo, ganadores (incluye hash de transparencia).
Lógica para registrar intentos previos y marcar ganador.
Vista admin: panel de sorteo (contador, botón “sortear”, logs, exportable).
API pública: mostrar ganadores, historial y transparencia (si se publica).
Generador de certificados / acta de entrega (PDF opcional).
Documentación del flujo y fallback manual.

Sprint 7 – Auditoría, Reportes, Notificaciones (1,5 semanas)
audit_logs: registrar acciones críticas de admin y API.
Reportes agregados (tickets vendidos, rifas por estado, premios destacados).
Dashboard admin con KPIs e indicadores.
Integrar notificaciones (correo/SMS opcional) para estado de ticket y ganadores.
Script batch para limpiezas: expiración de tickets, sesiones, etc.
QA completo y ajustes finales basados en retroalimentación.

Sprint 8 – Web Pública Completa & Hardening (1,5 semanas)
Terminar views/web/index.php y secciones adicionales (faq, reglas, recap de ganadores).
Optimizar tiempo de carga: caching, CDN de imágenes.
Revisar SEO, formatos OpenGraph y accesibilidad.
Pentest / revisión de seguridad básica (inyecciones, CSRF, CORS).
Preparar script de despliegue, guía de instalación y checklist de salida a producción.

Sprint 9 – Capacitaciones, Datos Demo y Cierre (1 semana)
Cargar data de ejemplo (premios, rifas, tickets) para demos.
Manual de usuario admin + video corto de uso.
Sesión de pases a producción y monitoreo post-lanzamiento.
Hitos de soporte y plan de mantenimiento (bugs, evolutivos).
Consideraciones generales
Cada sprint concluye con: pruebas funcionales, checklist QA, docs de endpoints actualizados y retroalimentación del PO.
Si necesitas refinar la longitud de cada sprint o agruparlos de otra forma, dime y lo reacomodamos.
Cuando quieras comenzar con un sprint específico (por ejemplo Sprint 2 – Premios), me das luz verde y arranco con la implementación paso a paso.