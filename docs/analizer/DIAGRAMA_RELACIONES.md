# 📊 DIAGRAMA DE RELACIONES - BASE DE DATOS SISTEMA RIFAS

## 🎯 ESTRUCTURA GENERAL

```
┌─────────────────────────────────────────────────────────────────┐
│                         SEDES (Multi-país)                       │
│  • Configuración por país/región                                │
│  • Moneda, zona horaria, métodos de pago                        │
└───────────────────┬─────────────────────────────────────────────┘
                    │
        ┌───────────┴───────────┬─────────────┬──────────────────┐
        │                       │             │                  │
        ▼                       ▼             ▼                  ▼
┌───────────────┐     ┌──────────────┐  ┌─────────────┐  ┌──────────────┐
│  AUTENTICACIÓN│     │   PREMIOS    │  │   RIFAS     │  │ CONFIGURACIÓN│
│               │     │              │  │             │  │              │
│ • Usuarios    │     │ • Categorías │  │ • Sorteos   │  │ • Ubicaciones│
│ • Roles       │     │ • Premios    │  │ • Tickets   │  │ • Métodos    │
│ • Permisos    │     │ • Galería    │  │ • Pagos     │  │   Pago       │
│ • Sesiones    │     │              │  │ • Ganadores │  │ • Estados    │
└───────────────┘     └──────────────┘  └─────────────┘  └──────────────┘
```

---

## 🔗 RELACIONES DETALLADAS

### 1️⃣ MÓDULO CENTRAL: SEDES

```
sedes (1) ──────── (N) usuarios
      │
      ├────────── (N) roles
      │
      ├────────── (N) permisos
      │
      ├────────── (N) categorias_premios
      │
      ├────────── (N) premios
      │
      ├────────── (N) rifas
      │
      ├────────── (N) tickets
      │
      ├────────── (N) ubicaciones_rifa
      │
      ├────────── (N) metodos_pago
      │
      ├────────── (N) estados_ticket
      │
      └────────── (N) configuracion_sede
```

**Relación:** Todas las tablas dependen de `sedes` para multi-tenancy
**Tipo:** 1:N (Una sede tiene muchos registros)
**Cascada:** ON DELETE CASCADE (si se elimina sede, se eliminan sus datos)

---

### 2️⃣ MÓDULO DE AUTENTICACIÓN

```
usuarios (N) ──────── (N) roles          [usuario_roles]
         │
         └─────────── (N) permisos       [usuario_permisos]
         │
         └─────────── (1) sesiones
         

roles (N) ──────────── (N) permisos     [rol_permisos]


Relaciones de N:N con tablas pivot:
┌──────────────────────────────────────────────┐
│  usuario_roles                                │
│  • usuario_id → usuarios.id                   │
│  • rol_id → roles.id                          │
│  • fecha_asignacion, fecha_vencimiento       │
└──────────────────────────────────────────────┘

┌──────────────────────────────────────────────┐
│  usuario_permisos                             │
│  • usuario_id → usuarios.id                   │
│  • permiso_id → permisos.id                   │
│  • Permisos específicos adicionales al rol   │
└──────────────────────────────────────────────┘

┌──────────────────────────────────────────────┐
│  rol_permisos                                 │
│  • rol_id → roles.id                          │
│  • permiso_id → permisos.id                   │
│  • Define qué puede hacer cada rol           │
└──────────────────────────────────────────────┘
```

**Sesiones:**
```
usuarios (1) ──────── (N) sesiones
                       │
                       └─ token_sesion (UNIQUE)
                       └─ fecha_expiracion
                       └─ activa (TINYINT)
```

---

### 3️⃣ MÓDULO DE PREMIOS

```
categorias_premios (1) ──────── (N) premios
                                     │
                                     └─ imagen_principal
                                     └─ galeria_imagenes (JSON)
                                     └─ video_url
                                     └─ especificaciones (JSON)

Ejemplo de jerarquía:
┌─────────────────────────────┐
│ Categoría: ELECTRÓNICA      │
│  ├─ Premio: iPhone 15 Pro   │
│  ├─ Premio: Samsung S24      │
│  └─ Premio: MacBook Pro      │
└─────────────────────────────┘

┌─────────────────────────────┐
│ Categoría: VEHÍCULOS        │
│  ├─ Premio: Toyota Corolla  │
│  ├─ Premio: Moto Yamaha R15  │
│  └─ Premio: Bicicleta Trek   │
└─────────────────────────────┘
```

---

### 4️⃣ MÓDULO DE RIFAS (CORE DEL SISTEMA) ⭐

```
rifas
  │
  ├──[premio_id]──────► premios (1:1)
  │                      └─ Un premio por rifa
  │
  ├──[ubicacion_id]───► ubicaciones_rifa (N:1)
  │                      └─ Dónde se realiza
  │
  └──► tickets (1:N)
         │
         ├──► comprobantes_pago (1:N)
         │     └─ Varios comprobantes por ticket (si rechaza y reenvía)
         │
         └──► participantes (1:1)
                └─ Solo si ticket está APROBADO

Flujo visual:

RIFA
  ↓
PREMIO (1 premio por rifa)
  ↓
TICKETS (Muchas personas compran)
  ↓
COMPROBANTES (Suben comprobantes)
  ↓
VALIDACIÓN (Admin aprueba/rechaza)
  ↓
PARTICIPANTES (Solo aprobados)
  ↓
SORTEO (Intentos múltiples)
  ↓
GANADOR (1 ganador)
```

---

### 5️⃣ MÓDULO DE COMPRA Y TICKETS

```
tickets (1) ──────────► (1) rifas
        │
        ├──────────────► (1) estado_ticket_id
        │
        ├──────────────► (N) comprobantes_pago
        │                     │
        │                     └──► (1) metodo_pago_id
        │
        └──────────────► (1) participantes
                               └─ Solo si estado = APROBADO

Diagrama de estados:

┌──────────────────┐
│ PENDIENTE_PAGO   │
└────────┬─────────┘
         │ (Usuario sube comprobante)
         ▼
┌──────────────────┐
│  PAGO_SUBIDO     │
└────────┬─────────┘
         │ (Admin valida)
         ▼
    ┌────────┬────────┐
    ▼        ▼        ▼
┌────────┐ ┌────────┐ ┌────────┐
│APROBADO│ │RECHAZADO│ │EXPIRADO│
└────┬───┘ └────────┘ └────────┘
     │
     │ (Se crea participante)
     ▼
┌──────────────────┐
│  PARTICIPANDO    │
└────────┬─────────┘
         │ (Sorteo realizado)
         ▼
    ┌────────┬────────┐
    ▼        ▼
┌────────┐ ┌────────┐
│GANADOR │ │PERDEDOR│
└────────┘ └────────┘
```

**Relación Ticket → Comprobantes:**
```
tickets (1) ──────── (N) comprobantes_pago

Un ticket puede tener varios comprobantes porque:
• Usuario sube comprobante incorrecto
• Admin rechaza
• Usuario vuelve a subir comprobante correcto
```

---

### 6️⃣ MÓDULO DE SORTEO

```
rifas (1) ──────────► (N) participantes
                           │
                           └──[ticket_id]──► tickets
                           │
                           └──[numero_participacion]
                                 (1, 2, 3, 4, ...)

participantes (1) ──────► (N) intentos_sorteo
                                │
                                └─ numero_intento (1-5)
                                └─ numero_sorteado
                                └─ es_ganador (0 o 1)
                                └─ hash_verificacion

intentos_sorteo (1) ───► (1) ganadores
                              └─ Solo el intento ganador
```

**Ejemplo de sorteo con 5 intentos:**

```
RIFA: "Gana un iPhone 15"
Total participantes: 150

participantes:
┌────┬────────────────┬──────────────────┐
│ ID │ Nro. Participación │ Ticket ID     │
├────┼────────────────┼──────────────────┤
│  1 │        1        │  PERU-...-001   │
│  2 │        2        │  PERU-...-002   │
│ .. │       ...       │       ...       │
│150 │      150        │  PERU-...-150   │
└────┴────────────────┴──────────────────┘

intentos_sorteo:
┌────┬─────────────┬──────────────┬────────────┐
│ ID │ Nro.Intento │ Nro.Sorteado │ Es Ganador │
├────┼─────────────┼──────────────┼────────────┤
│  1 │      1      │      87      │     0      │ ❌
│  2 │      2      │      23      │     0      │ ❌
│  3 │      3      │     145      │     0      │ ❌
│  4 │      4      │      67      │     0      │ ❌
│  5 │      5      │      42      │     1      │ ✅ GANADOR!
└────┴─────────────┴──────────────┴────────────┘

ganadores:
┌────┬─────────┬────────────────┬──────────────────┐
│ ID │ Rifa ID │ Participante ID│ Intento Sorteo ID│
├────┼─────────┼────────────────┼──────────────────┤
│  1 │    1    │       42       │        5         │
└────┴─────────┴────────────────┴──────────────────┘
         └─────────► El participante #42 ganó
```

---

### 7️⃣ MÓDULO DE CONFIGURACIÓN

```
sedes (1) ──────────► (N) configuracion_sede
      │                   └─ clave/valor
      │
      ├──────────────► (N) ubicaciones_rifa
      │                   └─ Direcciones físicas
      │
      ├──────────────► (N) metodos_pago
      │                   └─ Yape, Plin, etc.
      │
      └──────────────► (N) estados_ticket
                          └─ PENDIENTE, APROBADO, etc.
```

---

## 🔐 INTEGRIDAD REFERENCIAL

### Claves Foráneas con Cascada:

#### **ON DELETE CASCADE** (Se eliminan los hijos):
```sql
sedes → usuarios
sedes → roles
sedes → permisos
sedes → premios
sedes → rifas
sedes → tickets
sedes → participantes
sedes → ganadores
```

#### **ON DELETE RESTRICT** (No se puede eliminar si tiene hijos):
```sql
premios → rifas
    └─ No se puede eliminar premio si tiene rifas

rifas → tickets
    └─ No se puede eliminar rifa si tiene tickets

rifas → ganadores
    └─ No se puede eliminar rifa si ya tiene ganador
```

#### **ON DELETE SET NULL** (Se marca como NULL):
```sql
categorias_premios → premios.categoria_id
ubicaciones_rifa → rifas.ubicacion_id
metodos_pago → comprobantes_pago.metodo_pago_id
```

---

## 📊 ÍNDICES IMPORTANTES

### Índices de búsqueda frecuente:

```sql
-- Búsqueda de tickets por código
CREATE INDEX idx_tickets_codigo ON tickets(codigo_ticket);

-- Búsqueda de tickets por documento
CREATE INDEX idx_tickets_documento ON tickets(numero_documento);

-- Búsqueda de rifas activas
CREATE INDEX idx_rifas_estado ON rifas(estado);
CREATE INDEX idx_rifas_fechas ON rifas(fecha_inicio_venta, fecha_fin_venta);

-- Búsqueda de participantes por rifa
CREATE INDEX idx_participantes_rifa ON participantes(rifa_id);
CREATE INDEX idx_participantes_numero ON participantes(rifa_id, numero_participacion);

-- Sesiones activas
CREATE INDEX idx_sesiones_activa ON sesiones(activa, fecha_expiracion);

-- Auditoría
CREATE INDEX idx_audit_fecha ON audit_logs(fecha_operacion);
```

---

## 🎯 CONSTRAINTS ÚNICOS

```sql
-- Código de ticket único globalmente
UNIQUE KEY unique_codigo_ticket (codigo_ticket)

-- Usuario único por sede
UNIQUE KEY unique_username_sede (sede_id, username)
UNIQUE KEY unique_email_sede (sede_id, email)

-- Premio único por sede
UNIQUE KEY unique_codigo_premio_sede (sede_id, codigo)

-- Rifa única por sede
UNIQUE KEY unique_codigo_rifa_sede (sede_id, codigo)

-- Un ticket por participante por rifa
UNIQUE KEY unique_ticket_rifa (rifa_id, ticket_id)

-- Un ganador por rifa
UNIQUE KEY unique_ganador_rifa (rifa_id)
```

---

## 🔄 TRIGGERS RECOMENDADOS

### 1. **Actualizar contador de tickets vendidos**
```sql
CREATE TRIGGER trg_actualizar_tickets_vendidos
AFTER UPDATE ON tickets
FOR EACH ROW
BEGIN
    IF NEW.estado = 'APROBADO' AND OLD.estado != 'APROBADO' THEN
        UPDATE rifas 
        SET tickets_vendidos = tickets_vendidos + 1
        WHERE id = NEW.rifa_id;
    END IF;
END;
```

### 2. **Crear participante automáticamente**
```sql
CREATE TRIGGER trg_crear_participante
AFTER UPDATE ON tickets
FOR EACH ROW
BEGIN
    IF NEW.estado = 'APROBADO' AND OLD.estado != 'APROBADO' THEN
        INSERT INTO participantes (sede_id, rifa_id, ticket_id, numero_participacion)
        SELECT 
            NEW.sede_id,
            NEW.rifa_id,
            NEW.id,
            COALESCE(MAX(numero_participacion), 0) + 1
        FROM participantes
        WHERE rifa_id = NEW.rifa_id;
    END IF;
END;
```

### 3. **Auditoría automática**
```sql
CREATE TRIGGER trg_audit_tickets
AFTER UPDATE ON tickets
FOR EACH ROW
BEGIN
    INSERT INTO audit_logs (
        sede_id, tabla_afectada, registro_id, operacion,
        datos_anteriores, datos_nuevos, fecha_operacion
    ) VALUES (
        NEW.sede_id, 'tickets', NEW.id, 'UPDATE',
        JSON_OBJECT('estado', OLD.estado),
        JSON_OBJECT('estado', NEW.estado),
        NOW()
    );
END;
```

---

## 📈 VISTAS RECOMENDADAS

### 1. **Vista de rifas con estadísticas**
```sql
CREATE VIEW v_rifas_estadisticas AS
SELECT 
    r.id,
    r.codigo,
    r.nombre,
    r.estado,
    p.nombre AS premio,
    COUNT(DISTINCT t.id) AS total_tickets,
    COUNT(DISTINCT CASE WHEN t.estado = 'APROBADO' THEN t.id END) AS tickets_aprobados,
    SUM(t.precio_pagado) AS total_recaudado,
    DATEDIFF(r.fecha_sorteo, NOW()) AS dias_restantes
FROM rifas r
INNER JOIN premios p ON r.premio_id = p.id
LEFT JOIN tickets t ON r.id = t.rifa_id
GROUP BY r.id;
```

### 2. **Vista de tickets pendientes de validación**
```sql
CREATE VIEW v_tickets_pendientes AS
SELECT 
    t.id,
    t.codigo_ticket,
    t.nombres,
    t.apellidos,
    t.email,
    t.estado,
    r.nombre AS rifa,
    c.archivo_comprobante,
    c.fecha_pago,
    DATEDIFF(NOW(), c.fecha_creacion) AS dias_esperando
FROM tickets t
INNER JOIN rifas r ON t.rifa_id = r.id
LEFT JOIN comprobantes_pago c ON t.id = c.ticket_id
WHERE t.estado IN ('PAGO_SUBIDO', 'VALIDANDO')
ORDER BY c.fecha_creacion ASC;
```

---

## 🎨 MODELO ENTIDAD-RELACIÓN SIMPLIFICADO

```
         SEDES
           │
    ┌──────┼──────┬─────────────┬──────────┐
    │      │      │             │          │
 USUARIOS ROLES PREMIOS    UBICACIONES  MÉTODOS
    │      │      │         RIFA         PAGO
    │      │      │
    └─┬────┤      │
  PERMISOS │      │
           │      │
           │    RIFAS ─────► CATEGORÍAS
           │      │          PREMIOS
           │      │
           │   TICKETS ───► ESTADOS
           │      │          TICKET
           │      │
           │  COMPROBANTES
           │      │
           │ PARTICIPANTES
           │      │
           │  INTENTOS
           │   SORTEO
           │      │
           │  GANADORES
           │
         SESIONES
```

---

## ✅ VALIDACIONES DE NEGOCIO

### 1. Un premio solo puede estar en UNA rifa activa
```sql
-- No permitir crear rifa con premio que ya tiene rifa activa
CHECK (
    NOT EXISTS (
        SELECT 1 FROM rifas r2 
        WHERE r2.premio_id = premio_id 
        AND r2.estado IN ('PUBLICADA', 'EN_VENTA')
        AND r2.id != id
    )
)
```

### 2. Fecha de sorteo debe ser posterior a fin de venta
```sql
CHECK (fecha_sorteo > fecha_fin_venta)
```

### 3. Intento ganador debe ser <= número de intentos
```sql
CHECK (intento_ganador <= numero_intentos)
```

### 4. Precio pagado debe ser >= precio del ticket
```sql
-- Al validar comprobante
CHECK (comprobantes_pago.monto >= rifas.precio_ticket)
```

---

**Última actualización:** Enero 2025

