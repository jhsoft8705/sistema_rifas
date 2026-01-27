# Lógica de Negocio: Reserva y Liberación de Números

## Resumen Ejecutivo

El sistema maneja la reserva de números de dos formas diferentes según el módulo:

1. **Landing Page (Compra desde Web)**: Los números se reservan en la base de datos inmediatamente al seleccionarlos
2. **Módulo Administrativo de Ventas**: Los números NO se reservan en BD hasta confirmar la venta (solo en memoria del frontend)

## Tiempo de Reserva

- **Duración**: 10 minutos desde el momento de la reserva
- **Campo en BD**: `reservado_hasta` (DATETIME)
- **Propósito**: Evitar que números queden bloqueados indefinidamente si el usuario abandona la sesión

## Escenarios de Liberación

### 1. Landing Page - Usuario Presiona "Limpiar Todo"

**Comportamiento**: ✅ **LIBERACIÓN INMEDIATA**

- Cuando el usuario presiona el botón "Limpiar Todo", se ejecuta:
  1. Se llama a la API: `POST /api/rifas/numeros/liberar`
  2. El backend ejecuta `liberar_numeros_reservados()` que:
     - Busca números con `reservado_por_sesion = [sesion_id]`
     - Que tengan `estado = 'RESERVADO'`
     - Que tengan `ticket_id IS NULL` (no están vendidos)
  3. Los números se liberan **INMEDIATAMENTE**, sin importar si han pasado 10 minutos o no
  4. Se actualiza `estado = 'DISPONIBLE'`, se limpian `reservado_hasta`, `reservado_por_sesion`, `fecha_reserva`

**Código relevante**:
- Frontend: `landing.js` → `cancelarTodasLasSelecciones()` (líneas 2910-3036)
- Backend: `models/Rifa.php` → `liberar_numeros_reservados()` (líneas 611-654)

### 2. Landing Page - Usuario Elimina un Número Individual

**Comportamiento**: ✅ **LIBERACIÓN INMEDIATA**

- Similar al caso anterior, pero solo para el número específico eliminado
- Se llama a la misma API de liberación

**Código relevante**:
- Frontend: `landing.js` → `eliminarNumero()` (líneas 2730-2780)

### 3. Landing Page - Usuario Abandona sin Limpiar

**Comportamiento**: ⏱️ **LIBERACIÓN AUTOMÁTICA DESPUÉS DE 10 MINUTOS**

- Si el usuario selecciona números pero abandona la página sin presionar "Limpiar Todo":
  1. Los números quedan en estado `RESERVADO` con `reservado_hasta = NOW() + 10 MINUTES`
  2. El procedimiento almacenado `liberar_numeros_vencidos()` se ejecuta automáticamente:
     - Antes de cada búsqueda de números disponibles
     - Antes de cada reserva de números
     - Antes de cada asignación aleatoria
  3. Los números vencidos se liberan automáticamente cuando `reservado_hasta < NOW()`

**Código relevante**:
- Backend: `docs/sql/tickets.sql` → `liberar_numeros_vencidos()` (líneas 826-850)
- Backend: `models/Rifa.php` → `liberar_numeros_vencidos()` (método privado, línea 463)

### 4. Módulo Administrativo de Ventas - Usuario Presiona "Limpiar"

**Comportamiento**: ✅ **NO REQUIERE LIBERACIÓN EN BD**

- En el módulo administrativo, los números **NO se reservan en la base de datos** hasta que se confirma la venta
- Los números seleccionados solo existen en memoria del frontend (`window.numerosSeleccionadosVenta`)
- Al presionar "Limpiar", simplemente se limpia el array en memoria
- No hay necesidad de llamar a la API de liberación porque no hay reservas en BD

**Código relevante**:
- Frontend: `views/rifas/ventas/ventas.js` → `cancelarTodasLasSeleccionesVenta()` (líneas 564-569)

### 5. Módulo Administrativo de Ventas - Confirmación de Venta

**Comportamiento**: ✅ **RESERVA Y VENTA INMEDIATA**

- Cuando el operador confirma la venta:
  1. Se crea el ticket con estado `APROBADO` (venta directa, sin validación)
  2. Los números seleccionados se asignan al ticket
  3. Los números pasan directamente a estado `VENDIDO`
  4. No hay período de reserva intermedio

**Código relevante**:
- Frontend: `views/rifas/ventas/ventas.js` → `confirmarVenta()` (líneas 611-719)
- Backend: `docs/sql/tickets.sql` → `register_ticket()` (cuando `p_estado_inicial = 'APROBADO'`)

## Flujo Completo: Landing Page

```
Usuario selecciona 5 números
    ↓
Sistema reserva en BD (estado = RESERVADO, reservado_hasta = NOW() + 10 min)
    ↓
┌─────────────────────────────────────┐
│ ¿Usuario presiona "Limpiar Todo"?    │
└─────────────────────────────────────┘
    │                    │
   SÍ                   NO
    │                    │
    ↓                    ↓
Liberación          Espera 10 minutos
INMEDIATA           o hasta que se ejecute
    │                liberar_numeros_vencidos()
    │                    │
    │                    ↓
    │              Liberación automática
    │              (antes de cada búsqueda)
    │
    ↓
Números disponibles
para otros usuarios
```

## Flujo Completo: Módulo Administrativo

```
Operador selecciona números
    ↓
Números en memoria (window.numerosSeleccionadosVenta)
    ↓
┌─────────────────────────────────────┐
│ ¿Operador presiona "Limpiar"?      │
└─────────────────────────────────────┘
    │                    │
   SÍ                   NO
    │                    │
    ↓                    ↓
Limpia array          Continúa con venta
en memoria            ↓
    │              Confirma venta
    │                    │
    │                    ↓
    │              Crea ticket (APROBADO)
    │                    │
    │                    ↓
    │              Números → VENDIDO
    │
    ↓
No hay reservas en BD
que liberar
```

## Preguntas Frecuentes

### ¿Si presiono "Limpiar Todo" antes de 10 minutos, se liberan inmediatamente?

**Sí, absolutamente.** Los números se liberan inmediatamente cuando presionas "Limpiar Todo", sin importar cuánto tiempo haya pasado desde la reserva.

### ¿Tengo que esperar 10 minutos si presiono "Limpiar Todo"?

**No.** La liberación es inmediata. Los 10 minutos solo aplican si abandonas la sesión sin limpiar.

### ¿Aplica lo mismo en el módulo de ventas administrativo?

**Sí, pero de forma diferente.** En el módulo administrativo no se reservan números en BD hasta confirmar la venta, por lo que "Limpiar" solo limpia la memoria del frontend. No hay reservas en BD que liberar.

### ¿Qué pasa si selecciono números y cierro el navegador?

Los números quedarán reservados por 10 minutos. Se liberarán automáticamente cuando:
- Otro usuario intente buscar números disponibles
- Se ejecute el procedimiento `liberar_numeros_vencidos()` (antes de cada búsqueda/reserva)

## Conclusión

**La lógica de negocio es clara y correcta:**

1. ✅ **"Limpiar Todo" = Liberación Inmediata** (en ambos módulos)
2. ⏱️ **Abandono sin limpiar = Liberación automática después de 10 minutos**
3. 🔄 **Liberación automática antes de cada búsqueda** (números vencidos se liberan automáticamente)

Esta lógica garantiza que:
- Los números no queden bloqueados indefinidamente
- Los usuarios puedan liberar manualmente cuando lo deseen
- El sistema se auto-limpie de reservas vencidas
