# ✅ Cambios Implementados en Frontend - Sistema de Numeración de Boletos

## 📋 Resumen de Cambios en `index.php`

Se ha integrado completamente el **Sistema de Selección de Números de Boleto** en el modal de compra existente, manteniendo el diseño actual tipo checkout con tabs.

---

## 🆕 Cambios Realizados

### 1. **Tab 1: Información Personal** - Campos de Documento Agregados

**Ubicación:** Líneas 661-687

**Campos Nuevos:**
- ✅ `tipo_documento` (SELECT): DNI, CE, Pasaporte, RUC
- ✅ `numero_documento` (INPUT): Número del documento

```html
<div class="row">
    <div class="col-sm-6">
        <select class="form-select" id="tipo_documento" name="tipo_documento">
            <option value="">Seleccionar...</option>
            <option value="DNI">DNI</option>
            <option value="CE">Carnet de Extranjería</option>
            <option value="PASAPORTE">Pasaporte</option>
            <option value="RUC">RUC</option>
        </select>
    </div>
    <div class="col-sm-6">
        <input type="text" id="numero_documento" name="numero_documento">
    </div>
</div>
```

**Validación Agregada:**
- ✅ Tipo de documento obligatorio
- ✅ Número de documento obligatorio (mínimo 6 caracteres)

---

### 2. **Tab 2: Tu Orden** - Sistema de Selección de Números

**Ubicación:** Líneas 776-847

**Funcionalidades Agregadas:**

#### A) Sección de Selección de Números
```html
<div class="card border border-success mb-4" id="seccion_seleccion_numero">
    <!-- Dos opciones de cards -->
</div>
```

**Opción 1: Elegir Número Específico**
- Card con botón "Ver Números Disponibles"
- Abre modal con grid de números

**Opción 2: Asignar Aleatorio**
- Card con botón "Asignar Número Aleatorio"
- El sistema elige un número disponible al azar

#### B) Display de Número Seleccionado
```html
<div id="numero_seleccionado_display" style="display: none;">
    <div class="alert alert-warning">
        <strong>Tu número:</strong> <span id="numero_elegido_text">0000</span>
        <button onclick="cancelarSeleccionNumero()">Cambiar</button>
        
        Reservado por: <strong id="timer_reserva">10:00</strong>
    </div>
</div>
```

**Características:**
- ✅ Muestra el número seleccionado en badge verde
- ✅ Temporizador de reserva (10 minutos)
- ✅ Botón para cambiar de número
- ✅ Campos ocultos: `numero_reservado`, `numero_formateado`

---

### 3. **Modal Nuevo: Grid de Números Disponibles**

**Ubicación:** Líneas 1190-1303

**Componentes del Modal:**

#### A) Buscador de Números
```html
<input type="text" id="buscar_numero" placeholder="Busca tu número favorito">
<button onclick="buscarNumero()">Buscar</button>
```

#### B) Filtros
- **Todos**: Mostrar todos los números
- **Pares**: Solo números pares
- **Impares**: Solo números impares
- **Múltiplos de 5**: 5, 10, 15, 20, etc.

#### C) Estadísticas en Tiempo Real
```
┌─────────────┬─────────────┬─────────────┬─────────────┐
│ Disponibles │  Vendidos   │ Reservados  │ % Vendido   │
│     75      │     20      │      5      │    20%      │
└─────────────┴─────────────┴─────────────┴─────────────┘
```

#### D) Leyenda de Estados
- 🟢 **Verde**: Disponible (se puede comprar)
- ⚫ **Gris**: Vendido (no disponible)
- 🟡 **Amarillo**: Reservado (en proceso de compra)
- ⚪ **Negro**: Bloqueado (no se vende)

#### E) Grid de Números
- Grid responsive (Bootstrap)
- 100 números mostrados (0001-0100)
- Cada botón clickeable si está disponible
- Estados visuales con colores
- Números especiales con efecto brillante (50, 100, 150, etc.)

---

### 4. **Tab 4: Resumen Final** - Información de Número

**Ubicación:** Línea 1069-1076

**Campo Agregado:**
```html
<tr id="resumen_numero_row" style="display: none;">
    <td>Número de Boleto:</td>
    <td><span class="badge bg-success" id="resumen_numero_boleto">-</span></td>
</tr>
```

**Comportamiento:**
- Solo se muestra si el usuario seleccionó un número
- Si no seleccionó, la fila queda oculta

---

### 5. **CSS Inline para Grid de Números**

**Ubicación:** Líneas 3092-3152

**Estilos Agregados:**
```css
.numero-btn { /* Base para todos los botones */ }
.numero-disponible { /* Verde, clickeable */ }
.numero-vendido { /* Gris, disabled */ }
.numero-reservado { /* Amarillo, disabled */ }
.numero-bloqueado { /* Negro, disabled */ }
.numero-especial { /* Degradado azul con brillo */ }
```

**Efectos Hover:**
- ✅ Transform scale al pasar el mouse
- ✅ Cambio de color (blanco a verde)
- ✅ Shadow para números especiales

---

### 6. **JavaScript: Funciones Nuevas**

**Ubicación:** Líneas 3154-3523

#### Funciones Principales:

| Función | Descripción | Línea |
|---------|-------------|-------|
| `mostrarGridNumeros()` | Abre modal y carga números | 3164 |
| `cargarNumerosDisponibles()` | Carga números desde API | 3177 |
| `generarNumerosSimulados()` | Genera datos de prueba (TEMPORAL) | 3191 |
| `mostrarGridNumeros_Render()` | Renderiza grid y estadísticas | 3212 |
| `seleccionarNumero()` | Reserva número específico | 3263 |
| `asignarNumeroAleatorio()` | Asigna número al azar | 3305 |
| `cancelarSeleccionNumero()` | Cancela selección | 3347 |
| `iniciarTemporizadorReserva()` | Timer de 10 minutos | 3366 |
| `buscarNumero()` | Busca número en el grid | 3401 |
| `filtrarNumeros()` | Filtra por tipo | 3433 |
| `obtenerOGenerarSesionId()` | ID único de sesión | 3448 |

#### Variables Globales:
```javascript
let numerosDisponibles = [];    // Array de números cargados
let numeroSeleccionado = null;  // Número actualmente seleccionado
let timerReserva = null;        // Intervalo del temporizador
let tiempoRestante = 600;       // 10 minutos en segundos
let rifaActual = null;          // ID de rifa actual
```

---

## 🎯 Flujo de Usuario Completo

### Escenario A: Usuario Elige Número Específico (777)

```
1. Abre modal de compra
2. Tab 1: Llena datos personales + documento
3. Click "Continuar a tu Orden"
4. Tab 2: Ve "Selecciona tu Número de la Suerte"
5. Click en "Ver Números Disponibles"
   └─→ Se abre modal con grid de 100 números
6. Busca "777" en el buscador
   └─→ El sistema hace scroll al número 0777
7. Click en el número 0777
   └─→ Se reserva por 10 minutos
   └─→ Se cierra el modal de números
   └─→ Aparece badge verde: "Tu número: 0777"
   └─→ Inicia countdown: "Reservado por: 9:59"
8. Selecciona cantidad de tickets
9. Click "Continuar a Pago"
10. Tab 3: Ve métodos de pago y sube comprobante
11. Click "Revisar Orden"
12. Tab 4: Ve resumen completo
    └─→ Incluye: "Número de Boleto: 0777"
13. Click "Confirmar Compra"
14. ✅ Compra registrada con número 0777
```

### Escenario B: Usuario Prefiere Número Aleatorio

```
1. Abre modal de compra
2. Tab 1: Llena datos personales
3. Click "Continuar a tu Orden"
4. Tab 2: Click en "Asignar Número Aleatorio"
   └─→ Sistema busca número disponible al azar
   └─→ Se asigna (ej: 0234)
   └─→ Aparece badge: "Tu número: 0234"
   └─→ Inicia countdown: "10:00"
5. Continúa con el proceso normal
```

### Escenario C: Usuario NO Selecciona Número

```
1. Usuario llena todo el formulario
2. NO selecciona número (saltea la sección)
3. Al confirmar compra:
   └─→ Backend asigna número automáticamente
   └─→ En la BD: numero_seleccionado_usuario = 0
```

---

## 🔧 Configuración del Sistema

### Estados de Número en el Grid:

| Estado | Color | Clickeable | Descripción |
|--------|-------|------------|-------------|
| **DISPONIBLE** | Verde 🟢 | ✅ Sí | Se puede comprar |
| **VENDIDO** | Gris ⚫ | ❌ No | Ya fue vendido |
| **RESERVADO** | Amarillo 🟡 | ❌ No | Reservado por otro usuario |
| **BLOQUEADO** | Negro ⚪ | ❌ No | Bloqueado por admin |
| **ESPECIAL** | Azul brillante ✨ | ✅ Sí | Número especial (50, 100, 150) |

### Temporizador de Reserva:

```
Tiempo total: 10 minutos (600 segundos)

Alertas:
- A los 2 minutos restantes: Aviso de advertencia
- A los 0 segundos: Reserva expirada → Reload de página

Actualización: Cada 1 segundo
Formato: "9:59", "5:30", "0:15"
```

---

## 📊 Datos que se Envían al Backend

### FormData Actualizado:

```javascript
{
    // Datos existentes
    rifa_id: 1,
    nombre_completo: "Juan Pérez",
    email: "juan@email.com",
    telefono: "+52 1 55 1234 5678",
    ciudad: "Ciudad de México",
    estado: "CDMX",
    direccion_envio: "Calle 123, Col. Centro...",
    cantidad_tickets: 1,
    total: "10.00",
    comprobante_pago: File,
    
    // NUEVOS CAMPOS
    tipo_documento: "DNI",                    // ← NUEVO
    numero_documento: "12345678",             // ← NUEVO
    numero_boleto: 777,                       // ← NUEVO (null si no seleccionó)
    numero_boleto_formateado: "0777",         // ← NUEVO
    numero_seleccionado_usuario: 1            // ← NUEVO (1=sí, 0=no)
}
```

---

## 🎨 Diseño Visual

### Grid de Números (Responsive):

```
Desktop (pantalla grande):
┌────┬────┬────┬────┬────┬────┬────┬────┬────┬────┬────┬────┐
│0001│0002│0003│0004│0005│0006│0007│0008│0009│0010│0011│0012│
├────┼────┼────┼────┼────┼────┼────┼────┼────┼────┼────┼────┤
│0013│0014│0015│0016│0017│0018│0019│0020│0021│0022│0023│0024│
└────┴────┴────┴────┴────┴────┴────┴────┴────┴────┴────┴────┘
        12 números por fila (col-lg-1)

Tablet:
┌────┬────┬────┬────┬────┬────┐
│0001│0002│0003│0004│0005│0006│
└────┴────┴────┴────┴────┴────┘
        6 números por fila (col-md-2)

Móvil:
┌────┬────┬────┐
│0001│0002│0003│
└────┴────┴────┘
        3 números por fila (col-4)
```

### Colores de Estados:

```css
Disponible:   border-color: #28a745 (verde)
              background: white
              
Vendido:      border-color: #6c757d (gris)
              background: #e9ecef
              opacity: 0.5
              
Reservado:    border-color: #ffc107 (amarillo)
              background: #fff3cd
              
Bloqueado:    border-color: #343a40 (negro)
              background: #f8f9fa
              
Especial:     border-color: #17a2b8 (cyan)
              background: gradient azul
              box-shadow: brillante
```

---

## 🔄 Integración con Backend (Próximos Pasos)

### Endpoints API que se necesitan crear:

#### 1. GET `/api/rifas/{id}/numeros-disponibles`

**Reemplazar línea 3181-3187 con:**
```javascript
const response = await fetch(`/api/rifas/${rifaId}/numeros-disponibles`);
const numeros = await response.json();
mostrarGridNumeros_Render(numeros);
```

**Respuesta esperada:**
```json
[
    {
        "numero_entero": 1,
        "numero_formateado": "0001",
        "estado": "DISPONIBLE",
        "es_especial": false
    },
    {
        "numero_entero": 13,
        "numero_formateado": "0013",
        "estado": "VENDIDO",
        "es_especial": false
    },
    ...
]
```

#### 2. POST `/api/rifas/reservar-numero`

**Reemplazar línea 3267-3301 con:**
```javascript
const response = await fetch('/api/rifas/reservar-numero', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        rifa_id: rifaId,
        numero_entero: numeroEntero,
        sesion_id: sesionId,
        minutos_reserva: 10
    })
});

const result = await response.json();

if (result.exito) {
    // Continuar con el proceso
} else {
    alert(result.mensaje); // "El número ya fue vendido"
}
```

**Respuesta esperada:**
```json
{
    "exito": true,
    "mensaje": "Número reservado exitosamente",
    "numero_entero": 777,
    "numero_formateado": "0777",
    "reservado_hasta": "2025-11-04 15:30:00"
}
```

#### 3. POST `/api/rifas/numero-aleatorio`

**Reemplazar línea 3309-3343 con:**
```javascript
const response = await fetch('/api/rifas/numero-aleatorio', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        rifa_id: rifaId,
        sesion_id: sesionId,
        minutos_reserva: 10
    })
});

const result = await response.json();

if (result.exito) {
    // Mostrar número asignado
} else {
    alert('No hay números disponibles');
}
```

**Respuesta esperada:**
```json
{
    "exito": true,
    "mensaje": "Número asignado exitosamente",
    "numero_entero": 234,
    "numero_formateado": "0234",
    "reservado_hasta": "2025-11-04 15:30:00"
}
```

---

## ⚡ Funcionalidades Activas

### ✅ Funcionando Ahora (Simulado):

1. **Grid de Números**: Muestra 100 números de prueba (0001-0100)
2. **Selección Manual**: Click en número → se reserva
3. **Asignación Aleatoria**: Botón → número al azar
4. **Temporizador**: Cuenta regresiva de 10 minutos
5. **Cancelar Selección**: Botón para cambiar de número
6. **Buscador**: Busca número específico y hace scroll
7. **Filtros**: Pares, Impares, Múltiplos de 5
8. **Estadísticas**: Disponibles, Vendidos, Reservados, %
9. **Validaciones**: Documento obligatorio en Tab 1
10. **Resumen**: Muestra número en Tab 4

### ⏳ Pendiente (Requiere Backend):

- Llamada real a API para obtener números
- Reservar número en base de datos
- Liberar reservas expiradas (cron)
- Confirmar venta al aprobar pago

---

## 🧪 Cómo Probar

### 1. Abrir la Landing
```
http://localhost/sistema_rifas/index.php
```

### 2. Click en "Comprar Tickets" de cualquier rifa

### 3. Tab 1: Llenar datos
- Nombre: Juan Pérez
- Email: juan@test.com
- Teléfono: +52 1234567890
- **Tipo Documento: DNI** ← NUEVO
- **Número Documento: 12345678** ← NUEVO
- Ciudad: México
- Estado: CDMX
- Dirección: Calle 123...

### 4. Click "Continuar a tu Orden"

### 5. Tab 2: Seleccionar Número
**Opción A:**
- Click en "Ver Números Disponibles"
- Se abre modal con grid
- Click en número "0777" (si está disponible)
- Modal se cierra
- Aparece: "Tu número: 0777" con timer

**Opción B:**
- Click en "Asignar Número Aleatorio"
- Aparece: "Se te asignó el número: 0234"
- Timer inicia

### 6. Continuar proceso normal
- Tab 3: Pago
- Tab 4: Confirmar
- Ver que en el resumen aparece: "Número de Boleto: 0777"

---

## 📝 Notas Importantes

### 🔴 IMPORTANTE - Simulación Actual:

Los números mostrados (0001-0100) son **SIMULADOS** para pruebas. Para usar datos reales:

1. **Ejecutar scripts de BD:**
   ```bash
   mysql -u root -p sistema_rifas < docs/sql/bd_rifas_mysql.sql
   mysql -u root -p sistema_rifas < docs/sql/sp_numeracion_boletos.sql
   ```

2. **Generar números para una rifa:**
   ```sql
   -- Configurar rifa
   UPDATE rifas SET
       usa_numeracion_boletos = 1,
       numero_inicial = 1,
       numero_final = 500,
       cantidad_digitos = 4,
       permitir_seleccion_numero = 1
   WHERE id = 1;
   
   -- Generar números
   CALL sp_generar_numeros_rifa(1);
   ```

3. **Crear endpoints API** (próximo paso)

4. **Reemplazar funciones simuladas** por llamadas reales

---

## 🎨 Compatibilidad con Template Actual

### ✅ Sin Conflictos CSS:

- **No se agregaron clases nuevas al template**
- Solo se usaron clases de Bootstrap existentes
- CSS inline con prefijos específicos (`#grid_numeros_disponibles .numero-btn`)
- No afecta otros elementos de la página

### ✅ Responsive:

- Grid adaptable: col-lg-1, col-md-2, col-sm-3, col-4
- Mobile-friendly
- Scroll vertical en modal
- Botones touch-friendly

---

## 📊 Mejoras Futuras (Opcionales):

1. **Sistema de Toasts**: Reemplazar `alert()` con notificaciones elegantes
2. **Animaciones**: Agregar animate.css para efectos
3. **Números Destacados**: Resaltar números "bonitos" (111, 222, 777, 888)
4. **Historial**: Mostrar últimos números vendidos
5. **Recomendaciones**: "Números populares", "Números de la suerte"
6. **Vista de Cuadrícula/Lista**: Toggle entre grid y lista
7. **Paginación**: Si hay más de 100 números, paginar
8. **Zoom**: Ampliar número al hacer hover

---

## ✅ Checklist de Implementación Frontend

- [x] Agregar campos de documento (Tab 1)
- [x] Agregar sección de selección de números (Tab 2)
- [x] Crear modal con grid de números
- [x] Implementar selección manual de número
- [x] Implementar asignación aleatoria
- [x] Implementar temporizador de reserva
- [x] Agregar buscador de números
- [x] Agregar filtros (pares, impares, múltiplos)
- [x] Agregar estadísticas en tiempo real
- [x] Mostrar número en resumen final (Tab 4)
- [x] Validar campos de documento
- [x] CSS inline para grid de números
- [x] Función cancelar selección
- [x] Campos ocultos para enviar al backend
- [ ] Conectar con API real (pendiente)

---

## 🚀 Siguiente Paso

**Crear los 3 endpoints API en el backend:**

Archivos a crear:
- `api/rifas/numeros-disponibles.php`
- `api/rifas/reservar-numero.php`
- `api/rifas/numero-aleatorio.php`

Ver documentación completa en: `docs/SISTEMA_NUMERACION_BOLETOS.md`

---

**Fecha:** 2025-11-04  
**Versión:** 1.0  
**Estado:** ✅ Frontend completado (simulación funcional)

**Próximo:** Crear endpoints API para conectar con base de datos real.

