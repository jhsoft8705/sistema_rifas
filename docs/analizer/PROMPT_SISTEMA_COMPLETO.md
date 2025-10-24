# PROMPT COMPLETO - SISTEMA DE CONTROL DE ASISTENCIA Y PLANILLA

## CONTEXTO DEL PROYECTO

Necesito que me diseñes y generes un sistema completo de control de asistencia y planilla para una entidad pública del Estado de Perú (CAFED). El sistema debe integrar con dispositivos biométricos y manejar tanto la gestión de empleados como el cálculo de planillas.

## REQUERIMIENTOS ESPECÍFICOS

### 1. ARQUITECTURA DEL SISTEMA

**Base de Datos:**
- SQL Server 2019 o superior
- Esquema jerárquico sin problemas de dependencias
- Triggers para sincronización automática
- Procedimientos almacenados para lógica de negocio
- Índices optimizados para rendimiento

**Integración Biométrica:**
- Base de datos del biométrico en servidor Plex
- Base de datos local en el mismo servidor
- Sincronización bidireccional mediante triggers
- Mapeo empleado ↔ biometric_user_id
- Logs inmutables del biométrico
- Procesamiento de logs sin mapeo

**Funcionalidades Principales:**
- Gestión de empleados (maestro)
- Control de asistencias biométricas
- Cálculo de planillas
- Reportes y analytics
- Auditoría completa

### 2. TABLAS REQUERIDAS

**Tablas Maestras (sin dependencias):**
- gerencias (unidades organizacionales)
- departamentos
- cargos
- tipo_ausentismo
- motivo_ausentismo
- horarios
- turnos_laborables
- Periodo_Asistencia

**Tablas de Configuración:**
- horario_semanal
- configuracion_dias
- configuracion_entrada
- configuracion_salida
- configuracion_refrigerio

**Tablas de Empleados:**
- empleados (maestro)
- dispositivos_biometricos
- empleado_biometrico (mapeo)

**Tablas de Asistencia:**
- biometric_log_raw (logs inmutables)
- marcaciones (procesadas)
- marcaciones_ajuste (ajustes manuales)
- biometric_logs_pendientes (sin mapeo)

**Tablas de Planilla:**
- beneficios_laborales
- conceptos_planilla
- planillas
- planilla_detalle

**Tablas de Auditoría:**
- sync_queue (cola de sincronización)
- audit_logs (auditoría general)

### 3. LÓGICA DE SINCRONIZACIÓN

**Flujo de Datos:**
1. Dispositivo biométrico registra marcación
2. Trigger inserta en biometric_log_raw
3. Trigger verifica mapeo empleado_biometrico
4. Si existe mapeo: inserta en sync_queue
5. Si no existe mapeo: inserta en biometric_logs_pendientes
6. Job procesa sync_queue cada minuto
7. Actualiza/crea marcaciones procesadas

**Manejo de Ajustes:**
- Ajustes manuales se registran en marcaciones_ajuste
- No se modifica biometric_log_raw (inmutable)
- Prioridad: ajustes manuales > logs biométricos
- Auditoría completa de cambios

### 4. FUNCIONALIDADES ESPECÍFICAS

**Gestión de Empleados:**
- Registro completo con datos personales
- Asociación con gerencias, departamentos, cargos
- Mapeo con dispositivos biométricos
- Historial de cambios

**Control de Asistencia:**
- Marcaciones automáticas desde biométrico
- Horarios y turnos configurables
- Tolerancias y excepciones
- Cálculo automático de horas trabajadas

**Cálculo de Planilla:**
- Conceptos configurables (ingresos, descuentos, aportes)
- Fórmulas personalizables
- Períodos de planilla
- Exportación para contabilidad

**Reportes:**
- Asistencias diarias/mensuales
- Tardanzas y faltas
- Horas extras
- Planillas por período

### 5. REQUERIMIENTOS TÉCNICOS

**Base de Datos:**
- SQL Server con tipos de datos apropiados
- Constraints y foreign keys
- Índices para optimización
- Triggers para automatización
- Procedimientos almacenados

**Seguridad:**
- Encriptación de datos sensibles
- Control de acceso por roles
- Auditoría de todas las operaciones
- Logs de seguridad

**Rendimiento:**
- Índices optimizados
- Consultas eficientes
- Caché de datos frecuentes
- Procesamiento asíncrono

### 6. INTEGRACIÓN BIOMÉTRICA

**Mapeo de Empleados:**
- Tabla empleado_biometrico para relacionar
- Múltiples dispositivos por empleado
- Estados activo/inactivo
- Historial de cambios

**Sincronización:**
- Triggers automáticos
- Cola de procesamiento
- Manejo de errores
- Reintentos automáticos

**Ajustes Manuales:**
- Interfaz para correcciones
- Justificación obligatoria
- Aprobación por supervisor
- Auditoría completa

### 7. NORMATIVA PERUANA

**Cumplimiento Legal:**
- Ley de Protección de Datos Personales
- Normativas laborales peruanas
- Retención de documentos
- Reportes a SUNAT

**Beneficios Sociales:**
- CTS (Compensación por Tiempo de Servicios)
- Gratificaciones
- Vacaciones
- Essalud

### 8. ENTREGABLES ESPERADOS

**Scripts SQL:**
- Creación de todas las tablas
- Triggers de sincronización
- Procedimientos almacenados
- Índices y constraints
- Datos de prueba

**Documentación:**
- Diagrama de base de datos
- Flujo de sincronización
- Manual de usuario
- Guía de implementación

**Código de Ejemplo:**
- API REST para integración
- Scripts de migración
- Herramientas de administración
- Reportes básicos

### 9. CONSIDERACIONES ESPECIALES

**Entidad Pública:**
- Transparencia y auditoría
- Cumplimiento normativo
- Integración con sistemas gubernamentales
- Presupuesto limitado

**Biometría:**
- Datos sensibles
- Seguridad estricta
- Retención de logs
- Anonimización posible

**Escalabilidad:**
- Crecimiento de empleados
- Múltiples dispositivos
- Volumen de transacciones
- Rendimiento garantizado

## INSTRUCCIONES ESPECÍFICAS PARA CURSOR

1. **Genera el esquema completo** con todas las tablas en orden jerárquico
2. **Crea los triggers** para sincronización automática
3. **Desarrolla los procedimientos** para lógica de negocio
4. **Incluye índices** para optimización de consultas
5. **Agrega vistas** para reportes comunes
6. **Documenta el flujo** de sincronización
7. **Proporciona ejemplos** de uso y datos de prueba
8. **Incluye consideraciones** de seguridad y auditoría

## RESULTADO ESPERADO

Un sistema completo, funcional y escalable que permita:
- Gestión integral de empleados
- Control de asistencia biométrica
- Cálculo automático de planillas
- Reportes y analytics
- Cumplimiento normativo peruano
- Integración robusta con dispositivos biométricos

El sistema debe estar listo para implementación en una entidad pública con todos los controles de seguridad, auditoría y cumplimiento normativo requeridos.
