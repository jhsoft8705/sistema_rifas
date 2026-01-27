# Módulos Tickets y Ventas - Documentación

## 📋 Resumen

El sistema tiene dos módulos relacionados con la gestión de tickets/ventas:

### 1. **Módulo Tickets** (`views/tickets/`)
**Propósito**: Gestión administrativa completa de tickets y validación de comprobantes.

**Funcionalidades**:
- Listar todos los tickets del sistema
- Filtrar por sede, rifa y estado
- Ver detalle completo de cada ticket
- **Validar comprobantes de pago** (aprobar/rechazar)
- Visualizar comprobantes subidos por usuarios

**Cuándo usar**:
- Cuando necesitas revisar y validar comprobantes de pago
- Para gestión administrativa de tickets
- Para ver el estado detallado de cada ticket

---

### 2. **Módulo Ventas** (`views/rifas/ventas/`)
**Propósito**: Registro de ventas administrativas y visualización de ventas realizadas.

**Funcionalidades**:
- **Tab 1: Rifas Disponibles**: Ver rifas disponibles para venta administrativa
- **Tab 2: Ventas Realizadas**: Listar todas las ventas/tickets realizados
- Registrar ventas directamente desde el panel administrativo
- Ver comprobantes de ventas
- **Aprobar/rechazar pagos** directamente desde la lista de ventas

**Cuándo usar**:
- Para registrar ventas físicas o telefónicas directamente
- Para ver un resumen de todas las ventas realizadas
- Para aprobar pagos rápidamente desde la lista de ventas

---

## 🔄 Flujo de Negocio Completo

### Desde Landing Page (Usuario Final)

1. **Usuario compra tickets**:
   - Selecciona rifa y números
   - Llena datos personales
   - Puede subir comprobante de pago (opcional)
   - Se crea ticket con estado `PENDIENTE_PAGO` o `PAGO_SUBIDO`

2. **Sistema crea ticket**:
   - Estado inicial: `PENDIENTE_PAGO` (si no sube comprobante)
   - Estado inicial: `PAGO_SUBIDO` (si sube comprobante)
   - Números quedan en estado `RESERVADO`

### Desde Panel Administrativo

#### Opción 1: Módulo Tickets (`views/tickets/`)
- Operador ve todos los tickets pendientes
- Puede filtrar por estado (`PENDIENTE_PAGO`, `PAGO_SUBIDO`, `VALIDANDO`)
- Hace clic en "Validar" → Se abre modal con:
  - Información del ticket
  - Comprobante subido (si existe)
  - Opciones: Aprobar o Rechazar
- Al aprobar:
  - Ticket pasa a estado `APROBADO`
  - Números pasan de `RESERVADO` a `VENDIDO`
  - Usuario puede participar en el sorteo

#### Opción 2: Módulo Ventas (`views/rifas/ventas/`)
- Operador va al tab "Ventas Realizadas"
- Ve lista de todas las ventas/tickets
- Puede filtrar por rifa y estado
- Hace clic en botón "Aprobar" (solo visible si estado es `PENDIENTE_PAGO`, `PAGO_SUBIDO` o `VALIDANDO`)
- Se abre modal con:
  - Información del ticket
  - Comprobante subido (si existe)
  - Opciones: Aprobar o Rechazar
- Al aprobar: mismo proceso que en módulo Tickets

---

## 🎯 Diferencias Clave

| Característica | Módulo Tickets | Módulo Ventas |
|---------------|----------------|---------------|
| **Enfoque** | Gestión administrativa completa | Registro y visualización de ventas |
| **Validación** | ✅ Sí (modal completo) | ✅ Sí (modal simplificado) |
| **Registro de ventas** | ❌ No | ✅ Sí (ventas administrativas) |
| **Filtros** | Por sede, rifa, estado | Por rifa, estado |
| **Vista de comprobantes** | ✅ Detallada | ✅ Detallada |
| **Uso recomendado** | Validación de comprobantes | Registro rápido y aprobación desde lista |

---

## 📝 Estados del Ticket

- `PENDIENTE_PAGO`: Usuario compró pero no ha subido comprobante
- `PAGO_SUBIDO`: Usuario subió comprobante, esperando validación
- `VALIDANDO`: Comprobante en proceso de validación
- `APROBADO`: Pago validado, ticket activo para sorteo
- `RECHAZADO`: Comprobante rechazado, números liberados
- `PARTICIPANDO`: Ticket participando en sorteo activo
- `GANADOR`: Ticket ganador del sorteo
- `EXPIRADO`: Ticket expirado

---

## 🔧 Procedimientos Almacenados Relacionados

1. **`register_ticket`**: Crea ticket con estado inicial según canal
2. **`register_comprobante_pago`**: Registra comprobante y cambia estado a `PAGO_SUBIDO`
3. **`validar_comprobante`**: Aprueba o rechaza comprobante, actualiza estado del ticket y números

---

## 💡 Recomendaciones

- **Para validación de comprobantes**: Usar módulo **Tickets** (más completo)
- **Para registro rápido de ventas**: Usar módulo **Ventas** → Tab "Rifas Disponibles"
- **Para aprobación rápida desde lista**: Usar módulo **Ventas** → Tab "Ventas Realizadas"

---

## 🚀 Próximas Mejoras Sugeridas

1. Unificar ambos módulos en uno solo con tabs
2. Agregar notificaciones por email cuando se aprueba/rechaza un ticket
3. Agregar historial de cambios de estado
4. Dashboard con estadísticas de ventas y tickets pendientes
