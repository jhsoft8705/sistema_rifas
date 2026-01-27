# Refactorización de Base de Datos - Sistema de Rifas

## Resumen de Cambios

Se ha refactorizado la base de datos para optimizarla según el flujo descrito, eliminando tablas innecesarias y simplificando la estructura.

## Flujo del Sistema

1. **Premios por categoría** → asociados a rifas
2. **Rifas** → con configuraciones de números (ej: 1-100)
3. **Personas** → compran 1 o más números
4. **Tickets** → registro de compra
5. **Comprobantes de pago** → asociados a tickets
6. **Validación** → operador valida comprobantes → cambia estado

## Tablas Eliminadas (Innecesarias)

### 1. `participantes`
- **Razón**: Redundante. Los tickets con estado `APROBADO` ya son participantes.
- **Alternativa**: Usar `tickets` con `estado = 'APROBADO'` o `'PARTICIPANDO'`.

### 2. `intentos_sorteo`
- **Razón**: Solo necesario si hay sistema de sorteo múltiple, no mencionado en el flujo básico.
- **Alternativa**: Puede agregarse después si se implementa sistema de sorteos.

### 3. `ganadores`
- **Razón**: Solo necesario si hay sistema de sorteo, no mencionado en el flujo básico.
- **Alternativa**: Puede agregarse después si se implementa sistema de sorteos.

### 4. `ubicaciones_rifa`
- **Razón**: No mencionada en el flujo descrito.
- **Alternativa**: Si se necesita después, puede agregarse.

### 5. `estados_ticket`
- **Razón**: Simplificado usando `VARCHAR` con valores predefinidos en lugar de tabla separada.
- **Alternativa**: Los estados se manejan directamente en `tickets.estado`:
  - `PENDIENTE_PAGO`
  - `PAGO_SUBIDO`
  - `VALIDANDO`
  - `APROBADO`
  - `RECHAZADO`
  - `PARTICIPANDO`
  - `GANADOR`
  - `EXPIRADO`

### 6. `metodos_pago`
- **Razón**: Opcional para el flujo básico. Puede agregarse después si se necesita.
- **Alternativa**: `comprobantes_pago.metodo_pago_id` puede ser `NULL` por ahora.

### 7. `configuracion_sede`
- **Razón**: No mencionada en el flujo básico.
- **Alternativa**: Puede agregarse después si se necesita configuración adicional.

### 8. `audit_logs`
- **Razón**: Útil pero no crítico para el flujo básico.
- **Alternativa**: Puede agregarse después si se necesita auditoría.

## Cambios Principales

### 1. Tabla `tickets` - Refactorizada

**ANTES**: Contenía campos duplicados de `personas`:
- `nombres`, `apellidos`, `tipo_documento`, `numero_documento`, `email`, `telefono`, `direccion`, `ciudad`, `pais`

**AHORA**: Solo referencia a `persona_id`:
```sql
persona_id INT NOT NULL COMMENT 'Referencia a la tabla personas'
```

**Ventajas**:
- ✅ Elimina duplicación de datos
- ✅ Facilita actualización de información de personas
- ✅ Reduce tamaño de la base de datos
- ✅ Mejora integridad referencial

**Cómo obtener datos de la persona**:
```sql
SELECT t.*, p.nombres, p.apellidos, p.email, p.telefono
FROM tickets t
INNER JOIN personas p ON t.persona_id = p.id
```

### 2. Estado Simplificado

**ANTES**: Tabla `estados_ticket` separada con relaciones.

**AHORA**: Campo `VARCHAR` con valores predefinidos:
```sql
estado VARCHAR(30) NOT NULL DEFAULT 'PENDIENTE_PAGO'
```

**Ventajas**:
- ✅ Más simple y directo
- ✅ Menos JOINs necesarios
- ✅ Fácil de consultar y filtrar

### 3. Estructura Optimizada

La estructura ahora sigue el flujo natural:
```
sedes
  ↓
categorias_premios → premios
  ↓
rifas → rifas_premios
  ↓
personas
  ↓
tickets → numeros_rifa
  ↓
comprobantes_pago
```

## Tablas Mantenidas (Esenciales)

1. ✅ `sedes` - Multisede
2. ✅ `categorias_premios` - Categorías de premios
3. ✅ `premios` - Premios
4. ✅ `rifas` - Rifas con configuraciones
5. ✅ `rifas_premios` - Relación rifa-premio (múltiples premios)
6. ✅ `personas` - Clientes/personas (centralizada)
7. ✅ `numeros_rifa` - Números de rifa configurables
8. ✅ `tickets` - Compras/tickets (refactorizada)
9. ✅ `comprobantes_pago` - Comprobantes de pago

## Tablas de Autenticación (Mantenidas)

- `roles`, `permisos`, `usuarios`
- `usuario_roles`, `usuario_permisos`, `rol_permisos`
- `sesiones`, `intentos_acceso`

## Impacto en Procedimientos Almacenados

Los procedimientos almacenados existentes (`register_ticket`, `list_ventas`, etc.) necesitarán actualizarse para:

1. **Usar `persona_id`** en lugar de campos duplicados en `tickets`
2. **Eliminar referencias** a tablas eliminadas (`participantes`, `estados_ticket`, etc.)
3. **Simplificar consultas** usando `personas` directamente

## Próximos Pasos

1. ✅ Base de datos refactorizada
2. ⏳ Actualizar procedimientos almacenados (`tickets.sql`, `rifas.sql`)
3. ⏳ Actualizar modelos PHP (`Ticket.php`, `Rifa.php`)
4. ⏳ Actualizar controladores si es necesario
5. ⏳ Probar flujo completo

## Notas Adicionales

- Si en el futuro se necesita sistema de sorteos, se pueden agregar las tablas `intentos_sorteo` y `ganadores`.
- Si se necesita configuración adicional por sede, se puede agregar `configuracion_sede`.
- Si se necesita auditoría, se puede agregar `audit_logs`.
- La estructura actual es **escalable** y puede extenderse según necesidades futuras.
