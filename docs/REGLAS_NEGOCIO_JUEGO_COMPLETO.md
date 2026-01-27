# REGLAS DE NEGOCIO - SISTEMA DE JUEGO DE RIFAS

## 📋 RESUMEN EJECUTIVO

### Condiciones para Jugar una Rifa:
1. ✅ Rifa activa (`estado_activo = 1`)
2. ✅ Números generados (tabla `numeros_rifa` tiene registros)
3. ✅ Premios asociados (tabla `rifas_premios` tiene al menos un premio activo)
4. ✅ Números disponibles para jugar (al menos un número vendido y no ganador de otro premio)

**IMPORTANTE**: NO es necesario que TODOS los números estén vendidos para jugar.

---

## 🎮 FLUJO DEL PROCESO DE JUEGO

### Paso 1: Seleccionar Rifa
- Se muestra lista de rifas que cumplen las condiciones
- Cada rifa muestra: código, nombre, premios, participantes, números vendidos

### Paso 2: Seleccionar Premio
- Se muestran TODOS los premios de la rifa
- Solo se pueden jugar premios que NO tienen ganador aún
- Los premios con ganador muestran: "Ganador: [Nombre] - [Número]"

### Paso 3: Jugar Premio
**Proceso interno (`jugar_premio_rifa`)**:

1. **Verificaciones**:
   - ✅ Rifa existe y está activa
   - ✅ Premio no tiene ganador aún
   - ✅ Hay números disponibles para jugar

2. **Contador de Intentos**:
   - Se cuenta desde `intentos_juego` (tabla de historial)
   - Se incrementa: `nuevo_intento = intentos_actuales + 1`
   - Se guarda en `intentos_juego` con `numero_id` del número seleccionado

3. **Selección Aleatoria**:
   - Se selecciona un **NÚMERO** aleatorio de `numeros_rifa`
   - El número debe estar `VENDIDO` y asociado a un ticket `APROBADO`
   - **EXCLUYE**: Números que ya ganaron OTRO premio de la misma rifa
   - **PERMITE**: Que el mismo número participe múltiples veces en el mismo premio hasta que haya ganador

4. **Determinación del Ganador**:
   - Si `nuevo_intento >= intento_ganador` → Es ganador
   - Se actualiza `intentos_juego.es_ganador = 1`
   - Se muestra formulario para registrar datos adicionales

### Paso 4: Registrar Ganador
**Proceso interno (`register_ganador`)**:

1. **Guardar en `ganadores`**:
   - Información de la persona
   - Premio ganado
   - **Número ganador específico** (`numero_id`)
   - Intento ganador
   - Dirección de envío (opcional)
   - Flag de publicación web

2. **Actualizar Ticket**:
   - Solo el ticket asociado al número ganador → `estado = 'GANADOR'`
   - Solo ese número específico es ganador (no todos los números de la persona)

3. **Actualizar Dirección** (si se proporciona):
   - Se actualiza la dirección en la tabla `personas`

---

## 🎯 SELECCIÓN POR NÚMEROS (LÓGICA ACTUAL)

### ¿Cómo funciona?

**Se selecciona un NÚMERO aleatorio, no una persona.**

### Ventajas:
1. ✅ **Más equitativo**: Cada número tiene la misma probabilidad
2. ✅ **Transparente**: Se muestra exactamente qué número ganó
3. ✅ **Justo**: Una persona con más números tiene más oportunidades (uno por cada número), pero cada número individual tiene la misma probabilidad
4. ✅ **Claro**: Solo un número específico es el ganador

### Ejemplo:

**Escenario:**
- Juan Pérez: tiene números R-0001, R-0002, R-0003 (3 números)
- María García: tiene número R-0005 (1 número)
- Pedro Sánchez: tiene números R-0010 a R-0109 (100 números)

**Proceso:**
1. Intento 1 → Selecciona número aleatorio → **R-0001** (Juan Pérez)
2. Intento 2 → Selecciona número aleatorio → **R-0005** (María García)
3. Intento 3 → Selecciona número aleatorio → **R-0045** (Pedro Sánchez)
4. Intento 4 → Selecciona número aleatorio → **R-0002** (Juan Pérez)
5. Intento 5 → Selecciona número aleatorio → **R-0078** (Pedro Sánchez) → **GANADOR**

**Resultado:**
- Número ganador: **R-0078**
- Ganador: Pedro Sánchez
- Solo el número R-0078 es ganador (no todos sus números)

**Probabilidades:**
- Cada número tiene la misma probabilidad de ser seleccionado
- Pedro tiene 100 oportunidades (una por cada número)
- Juan tiene 3 oportunidades
- María tiene 1 oportunidad
- Pero cada número individual tiene la misma probabilidad que cualquier otro

---

## 📊 ESTRUCTURA DE DATOS

### Tabla `intentos_juego`
```sql
- rifa_id
- rifa_premio_id
- persona_id (persona dueña del número seleccionado)
- numero_id (número seleccionado) ← NUEVO
- intento_numero
- es_ganador
- fecha_intento
- jugado_por
```

### Tabla `ganadores`
```sql
- rifa_id
- rifa_premio_id
- premio_id
- persona_id (persona ganadora)
- ticket_id (ticket del número ganador)
- numero_id (número ganador específico) ← NUEVO
- nombre_completo
- documento_completo
- intento_ganador
- fecha_ganador
- direccion_envio (opcional)
- publicar_web
```

---

## 🔍 VERIFICACIONES EN EL CÓDIGO

### En `jugar_premio_rifa`:
```sql
-- Seleccionar número aleatorio
SELECT 
    nr.id,
    nr.numero_formateado,
    t.persona_id,
    t.id AS ticket_id
FROM numeros_rifa nr
INNER JOIN tickets t ON t.id = nr.ticket_id
WHERE nr.rifa_id = p_rifa_id
  AND nr.estado = 'VENDIDO'
  AND t.estado IN ('APROBADO', 'PARTICIPANDO')
  -- Excluir números que ya ganaron OTRO premio
  AND nr.id NOT IN (
      SELECT g.numero_id
      FROM ganadores g
      WHERE g.rifa_id = p_rifa_id
        AND g.rifa_premio_id <> p_rifa_premio_id
        AND g.numero_id IS NOT NULL
  )
ORDER BY RAND()
LIMIT 1;
```

### En `register_ganador`:
```sql
-- Actualizar SOLO el ticket del número ganador
UPDATE tickets
SET estado = 'GANADOR'
WHERE id = p_ticket_id;
```

---

## 📝 REGLAS ESPECIALES

### Múltiples Premios:
- Una rifa puede tener múltiples premios
- Cada premio se juega **independientemente**
- Un número puede ganar solo **UN** premio por rifa
- Si un número gana el Premio 1, **NO puede** participar en el Premio 2

### Múltiples Números por Persona:
- Una persona puede tener múltiples números en la misma rifa
- Cuando un número gana, solo ese número específico es ganador
- Los demás números de la persona pueden seguir participando en otros premios

### Múltiples Números por Ticket:
- Un ticket puede tener múltiples números asociados
- Solo el número específico seleccionado es ganador

---

## 🎯 NÚMERO GANADOR

### ¿Qué número es ganador?

**Solo el número específico seleccionado en el intento ganador.**

### Estructura de Relaciones:

```
Número Ganador (numero_id)
  ├── Ticket (ticket_id)
  │     └── Persona (persona_id)
  └── Rifa Premio (rifa_premio_id)
        └── Premio (premio_id)
```

### Consulta SQL para obtener número ganador:

```sql
SELECT 
    nr.numero_formateado,
    nr.numero_entero,
    t.codigo_ticket,
    p.nombre_completo
FROM ganadores g
INNER JOIN numeros_rifa nr ON nr.id = g.numero_id
INNER JOIN tickets t ON t.id = nr.ticket_id
INNER JOIN personas p ON p.id = g.persona_id
WHERE g.id = [ganador_id];
```

---

## ⚠️ NOTAS IMPORTANTES

1. **Se selecciona un NÚMERO, no una persona**
2. **Cuando un número gana, solo ese número específico es ganador**
3. **Los números ganadores se identifican por `numero_id` en la tabla `ganadores`**
4. **Un número solo puede ganar UN premio por rifa**
5. **El contador de intentos es por premio específico**
6. **Cada número tiene la misma probabilidad de ser seleccionado**

---

## 🔄 MIGRACIÓN DESDE SELECCIÓN POR PERSONA

Si tienes datos antiguos con la lógica anterior (selección por persona), necesitas ejecutar:

```sql
-- 1. Agregar numero_id a intentos_juego
ALTER TABLE intentos_juego 
ADD COLUMN numero_id INT NULL COMMENT 'ID del número seleccionado' AFTER persona_id;

ALTER TABLE intentos_juego
ADD FOREIGN KEY (numero_id) REFERENCES numeros_rifa(id) ON DELETE SET NULL,
ADD INDEX idx_intentos_numero (numero_id);

-- 2. Agregar numero_id a ganadores
ALTER TABLE ganadores 
ADD COLUMN numero_id INT NULL COMMENT 'Número ganador específico' AFTER ticket_id;

ALTER TABLE ganadores
ADD FOREIGN KEY (numero_id) REFERENCES numeros_rifa(id) ON DELETE SET NULL,
ADD INDEX idx_ganadores_numero (numero_id);

-- 3. Ejecutar procedimientos actualizados
-- (Ejecutar docs/sql/juegos.sql y docs/sql/ganadores.sql)
```

---

## 📚 REFERENCIAS

- Procedimientos almacenados: `docs/sql/juegos.sql`, `docs/sql/ganadores.sql`
- Modelos PHP: `models/Juego.php`
- Controladores: `controller/JuegoController.php`
- Frontend: `views/juegos/juegos.js`, `views/juegos/form.php`
