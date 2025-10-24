# ARQUITECTURA DEL SISTEMA DE CONTROL DE ASISTENCIA Y PLANILLA

## DIAGRAMA DE ARQUITECTURA

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           SISTEMA DE CONTROL DE ASISTENCIA Y PLANILLA          │
│                                    CAFED - PERÚ                                 │
└─────────────────────────────────────────────────────────────────────────────────┘

┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   DISPOSITIVOS  │    │   SERVIDOR PLEX │    │   BASE DE DATOS │    │   SISTEMA WEB   │
│   BIOMÉTRICOS   │    │   (SERVIDOR)    │    │   LOCAL (SQL)    │    │   (FRONTEND)    │
│                 │    │                 │    │                 │    │                 │
│ • Huella        │◄──►│ • Plex Server   │◄──►│ • SQL Server     │◄──►│ • Dashboard     │
│ • Facial        │    │ • Base Biomét.  │    │ • Tablas Master  │    │ • Reportes      │
│ • Tarjeta       │    │ • Logs Raw      │    │ • Asistencias   │    │ • Configuración │
│ • PIN           │    │ • Sincronización│    │ • Planillas     │    │ • Administración│
└─────────────────┘    └─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │                       │
         │                       │                       │                       │
         ▼                       ▼                       ▼                       ▼
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   FLUJO DE      │    │   TRIGGERS      │    │   PROCEDIMIENTOS│    │   USUARIOS      │
│   DATOS         │    │   AUTOMÁTICOS   │    │   ALMACENADOS   │    │   FINALES       │
│                 │    │                 │    │                 │    │                 │
│ 1. Marcación    │    │ • Audit Logs   │    │ • Sync Queue    │    │ • RRHH          │
│ 2. Validación   │    │ • Biometric    │    │ • Ajustes       │    │ • Supervisores  │
│ 3. Procesamiento│    │ • Mapeo        │    │ • Planilla      │    │ • Administradores│
│ 4. Almacenamiento│   │ • Sincronización│   │ • Reportes      │    │ • Auditores     │
└─────────────────┘    └─────────────────┘    └─────────────────┘    └─────────────────┘
```

## FLUJO DE SINCRONIZACIÓN DETALLADO

```
DISPOSITIVO BIOMÉTRICO
         │
         ▼
┌─────────────────┐
│   MARCACIÓN     │
│   (Huella/PIN)  │
└─────────────────┘
         │
         ▼
┌─────────────────┐
│   LOG RAW       │
│   (Inmutable)   │
└─────────────────┘
         │
         ▼
┌─────────────────┐
│   TRIGGER       │
│   Verificar     │
│   Mapeo         │
└─────────────────┘
         │
         ▼
    ┌─────────┐
    │ ¿Existe │
    │ Mapeo?  │
    └─────────┘
         │
    ┌────┴────┐
    ▼         ▼
┌─────────┐ ┌─────────┐
│   SÍ    │ │   NO     │
│         │ │         │
│ SYNC    │ │ PENDING │
│ QUEUE   │ │ LOGS     │
└─────────┘ └─────────┘
         │         │
         ▼         ▼
┌─────────────────┐
│   PROCESAMIENTO │
│   (Job/Minuto)  │
└─────────────────┘
         │
         ▼
┌─────────────────┐
│   MARCACIONES   │
│   PROCESADAS    │
└─────────────────┘
         │
         ▼
┌─────────────────┐
│   PLANILLA      │
│   (Cálculo)     │
└─────────────────┘
```

## COMPONENTES DEL SISTEMA

### 1. CAPA DE DISPOSITIVOS
- **Dispositivos Biométricos**: Huella, facial, tarjeta, PIN
- **Protocolos**: TCP/IP, RS485, USB
- **Ubicaciones**: Múltiples puntos de acceso
- **Sincronización**: Tiempo real o batch

### 2. CAPA DE INTEGRACIÓN
- **Servidor Plex**: Almacenamiento de logs biométricos
- **Base de Datos Biométrica**: Logs inmutables
- **Sincronización**: Triggers automáticos
- **Mapeo**: Empleado ↔ Biometric User ID

### 3. CAPA DE PROCESAMIENTO
- **Base de Datos Local**: SQL Server
- **Triggers**: Automatización de procesos
- **Procedimientos**: Lógica de negocio
- **Jobs**: Procesamiento programado

### 4. CAPA DE APLICACIÓN
- **Sistema Web**: Frontend para usuarios
- **APIs REST**: Integración externa
- **Reportes**: Dashboards y exportaciones
- **Configuración**: Parámetros del sistema

## TABLAS PRINCIPALES Y RELACIONES

### TABLAS MAESTRAS
```
gerencias (1) ──┐
                ├── empleados (N)
departamentos (1) ──┘
                ├── empleados (N)
cargos (1) ──────┘
                ├── empleados (N)
turnos_laborables (1) ──┘
```

### TABLAS DE ASISTENCIA
```
empleados (1) ──┐
                ├── marcaciones (N)
                ├── empleado_biometrico (1)
                └── beneficios_laborales (N)

biometric_log_raw (1) ──┐
                        ├── sync_queue (N)
                        └── biometric_logs_pendientes (N)
```

### TABLAS DE PLANILLA
```
empleados (1) ──┐
                ├── planilla_detalle (N)
                └── beneficios_laborales (N)

planillas (1) ──┐
                ├── planilla_detalle (N)
                └── conceptos_planilla (N)
```

## FLUJO DE DATOS BIOMÉTRICOS

### 1. CAPTURA
- Empleado marca asistencia en dispositivo
- Dispositivo valida identidad (huella/PIN)
- Se genera log con timestamp y datos

### 2. TRANSMISIÓN
- Dispositivo envía log al servidor Plex
- Se almacena en base de datos biométrica
- Trigger detecta nueva inserción

### 3. PROCESAMIENTO
- Trigger verifica mapeo empleado-biométrico
- Si existe mapeo: inserta en cola de procesamiento
- Si no existe: marca como pendiente

### 4. SINCRONIZACIÓN
- Job procesa cola cada minuto
- Actualiza/crea marcaciones procesadas
- Calcula horas trabajadas y tardanzas

### 5. PLANILLA
- Sistema calcula conceptos de planilla
- Aplica fórmulas y descuentos
- Genera reportes y exportaciones

## MANEJO DE AJUSTES MANUALES

### 1. SOLICITUD
- Supervisor solicita corrección
- Proporciona justificación
- Adjunta evidencia (documentos)

### 2. APROBACIÓN
- Sistema valida permisos
- Registra ajuste en auditoría
- No modifica logs biométricos

### 3. APLICACIÓN
- Se crea marcaciones_ajuste
- Se actualiza marcaciones procesadas
- Se mantiene trazabilidad completa

### 4. SINCRONIZACIÓN
- Ajustes tienen prioridad sobre logs
- Se preserva integridad de datos
- Se genera reporte de discrepancias

## CONSIDERACIONES DE SEGURIDAD

### 1. DATOS BIOMÉTRICOS
- Encriptación en reposo y tránsito
- Acceso restringido por roles
- Logs de auditoría completos
- Retención configurable

### 2. INTEGRIDAD
- Logs biométricos inmutables
- Ajustes con auditoría
- Validación de datos
- Transacciones atómicas

### 3. CUMPLIMIENTO
- Ley de Protección de Datos Personales
- Normativas laborales peruanas
- Retención de documentos
- Reportes regulatorios

## ESCALABILIDAD Y RENDIMIENTO

### 1. BASE DE DATOS
- Índices optimizados
- Particionado por fechas
- Archivo de datos históricos
- Caché de consultas frecuentes

### 2. PROCESAMIENTO
- Jobs distribuidos
- Cola de procesamiento
- Reintentos automáticos
- Monitoreo de rendimiento

### 3. INTEGRACIÓN
- APIs asíncronas
- Sincronización en tiempo real
- Manejo de errores
- Logs detallados

## MONITOREO Y MANTENIMIENTO

### 1. LOGS
- Auditoría de todas las operaciones
- Trazabilidad de cambios
- Detección de anomalías
- Alertas automáticas

### 2. BACKUP
- Respaldos automáticos
- Recuperación de desastres
- Pruebas de restauración
- Retención de datos

### 3. ACTUALIZACIONES
- Parches de seguridad
- Mejoras de rendimiento
- Nuevas funcionalidades
- Migración de datos
