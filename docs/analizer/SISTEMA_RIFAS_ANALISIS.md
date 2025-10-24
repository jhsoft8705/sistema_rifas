# 📊 ANÁLISIS COMPLETO - SISTEMA DE RIFAS MULTISEDE

## 🎯 VISIÓN GENERAL

Sistema profesional de rifas/sorteos escalable para múltiples países con:
- **Landing Page** para compra de tickets por usuarios finales
- **Portal Administrativo** para gestión completa
- **Sistema de Validación** de pagos y comprobantes
- **Motor de Sorteos** configurable y transparente
- **Multi-sede** con configuraciones personalizables por país

---

## 🏗️ ARQUITECTURA DE BASE DE DATOS

### 📦 MÓDULOS PRINCIPALES

#### 1. **MÓDULO DE AUTENTICACIÓN** ✅ (YA IMPLEMENTADO)
- Gestión de usuarios administrativos
- Control de roles y permisos granular
- Sesiones con tokens JWT
- Auditoría de accesos
- Multi-sede con aislamiento de datos

**Tablas:**
- `sedes` - Configuración por país/región
- `usuarios` - Usuarios administrativos
- `roles` - Roles del sistema
- `permisos` - Permisos granulares
- `usuario_roles`, `usuario_permisos`, `rol_permisos` - Relaciones
- `sesiones` - Control de sesiones activas
- `intentos_acceso` - Auditoría de login
- `audit_logs` - Logs de auditoría general

---

#### 2. **MÓDULO DE PREMIOS Y CATEGORÍAS** 🎁

**Entidades:**

##### `categorias_premios`
Clasificación de premios para mejor organización:
- Electrónica (smartphones, laptops, etc.)
- Vehículos (autos, motos, etc.)
- Viajes (paquetes turísticos)
- Dinero en efectivo
- Electrodomésticos
- Otros

**Campos destacados:**
- `icono`: Clase CSS o URL de icono
- `color_hex`: Color para UI
- `orden`: Para ordenamiento en landing

##### `premios`
Catálogo completo de premios disponibles:

**Información básica:**
- `codigo`: Código único (ej: "IPHONE15-001")
- `nombre`: Nombre comercial
- `descripcion`: Descripción detallada HTML
- `valor_estimado`: Valor en moneda local

**Recursos multimedia:**
- `imagen_principal`: Imagen principal del premio
- `imagen_secundaria`: Imagen alternativa
- `galeria_imagenes`: JSON con array de URLs
- `video_url`: URL de video promocional

**Características del producto:**
- `marca`, `modelo`, `color`
- `especificaciones`: JSON con detalles técnicos

**Gestión:**
- `es_destacado`: Para mostrar en home
- `orden_visualizacion`: Orden en listados
- `terminos_condiciones`: Términos específicos del premio

---

#### 3. **MÓDULO DE RIFAS/SORTEOS** 🎲

##### `rifas`
Configuración completa de cada sorteo:

**Identificación:**
- `codigo`: Código único (ej: "RIFA-2025-001")
- `nombre`: Nombre comercial del sorteo

**Configuración del sorteo:**
```sql
numero_intentos INT NOT NULL DEFAULT 5
    -- Total de intentos/rondas antes de determinar ganador
    
intento_ganador INT NOT NULL DEFAULT 5
    -- En qué intento se selecciona al ganador
    -- Ejemplo: En 5 intentos, los primeros 4 no ganan, el 5to gana
```

**Ejemplo de configuración:**
- Si `numero_intentos = 5` y `intento_ganador = 5`:
  - Intento 1: Se sortea pero NO gana
  - Intento 2: Se sortea pero NO gana
  - Intento 3: Se sortea pero NO gana
  - Intento 4: Se sortea pero NO gana
  - Intento 5: El seleccionado ES EL GANADOR ✅

**Control de tickets:**
```sql
precio_ticket DECIMAL(10,2)
    -- Precio por ticket en moneda local
    
cantidad_maxima_tickets INT NULL
    -- NULL = ilimitado
    -- Número = límite de tickets totales
    
tickets_vendidos INT DEFAULT 0
    -- Contador automático
    
cantidad_maxima_por_persona INT DEFAULT 1
    -- Máximo de tickets por DNI/documento
```

**Fechas importantes:**
- `fecha_inicio_venta`: Inicio de venta
- `fecha_fin_venta`: Fin de venta
- `fecha_sorteo`: Fecha del sorteo en vivo
- `fecha_sorteo_realizado`: Cuando se ejecutó (NULL si no se ha realizado)

**Configuración visual (Landing Page):**
```sql
mostrar_contador TINYINT(1) DEFAULT 1
    -- Mostrar contador regresivo
    
mostrar_participantes TINYINT(1) DEFAULT 1
    -- Mostrar lista de participantes
    
mostrar_tickets_vendidos TINYINT(1) DEFAULT 1
    -- Mostrar cuántos tickets se han vendido
```

**Publicidad:**
- `tipo_publicidad`: Banner, Popup, Destacado
- `url_banner`: URL del banner promocional
- `texto_promocional`: Texto para redes sociales

**Estados de la rifa:**
```
BORRADOR          → Creada pero no publicada
PUBLICADA         → Visible pero no se puede comprar aún
EN_VENTA          → Abierta para compra
CERRADA           → Ventas cerradas
SORTEO_REALIZADO  → Sorteo ejecutado
FINALIZADA        → Premio entregado
CANCELADA         → Cancelada
```

---

#### 4. **MÓDULO DE COMPRA Y TICKETS** 🎫

##### `tickets`
Registro de compra de cada usuario:

**Datos del participante:**
```sql
nombres, apellidos
tipo_documento (DNI, CE, Pasaporte, etc.)
numero_documento
email, telefono
direccion, ciudad, pais
```

**Código único:**
```sql
codigo_ticket VARCHAR(50) UNIQUE
    -- Formato: SEDE-YYYYMMDD-NNNNNN
    -- Ejemplo: PERU-20250124-123456
    -- Permite validar autenticidad
```

**Estados del ticket:**
```
PENDIENTE_PAGO    → Ticket creado, esperando pago
PAGO_SUBIDO       → Comprobante subido, esperando validación
VALIDANDO         → En proceso de validación
APROBADO          → Pago validado, puede participar
RECHAZADO         → Pago rechazado
PARTICIPANDO      → En el sorteo activo
GANADOR           → Ganó el premio
EXPIRADO          → Ticket vencido
```

**Sistema de aprobación:**
```sql
aprobado_por VARCHAR(50)
fecha_aprobacion DATETIME
rechazado_por VARCHAR(50)
fecha_rechazo DATETIME
motivo_rechazo TEXT
```

**Notificaciones:**
```sql
notificado_compra TINYINT(1)
    -- Email enviado tras compra
    
notificado_aprobacion TINYINT(1)
    -- Email enviado tras aprobación
    
notificado_sorteo TINYINT(1)
    -- Email enviado con resultado del sorteo
```

**Validación de autenticidad:**
```sql
fecha_validez DATETIME
    -- Fecha hasta la cual el ticket es válido
    
validado TINYINT(1)
    -- Si el usuario validó su ticket
    
fecha_validacion DATETIME
    -- Cuándo se validó
```

---

#### 5. **MÓDULO DE PAGOS Y COMPROBANTES** 💰

##### `metodos_pago`
Métodos de pago disponibles por sede:

**Tipos de métodos:**
1. **Transferencia Bancaria:**
   - `numero_cuenta`, `numero_cci`
   - `titular_cuenta`, `banco`
   - `tipo_cuenta` (Ahorros/Corriente)

2. **Pagos Digitales (Yape, Plin, etc.):**
   - `numero_celular`
   - `qr_code_url`

3. **Pagos Internacionales (PayPal, Stripe):**
   - `email_cuenta`
   - Integración API (futuro)

**Configuración:**
```sql
requiere_comprobante TINYINT(1)
    -- Si requiere subir comprobante
    
instrucciones TEXT
    -- Instrucciones paso a paso para el usuario
    
orden INT
    -- Orden de visualización
```

##### `comprobantes_pago`
Almacenamiento de comprobantes:

```sql
archivo_comprobante VARCHAR(255)
    -- Path del archivo subido
    
tipo_archivo VARCHAR(10)
    -- jpg, png, pdf
    
tamano_archivo INT
    -- Tamaño en bytes para validación
```

**Información de la transacción:**
```sql
numero_operacion VARCHAR(100)
    -- Número de operación del banco
    
monto DECIMAL(10,2)
    -- Monto pagado
    
fecha_pago DATETIME
    -- Fecha de la transacción
    
banco_origen VARCHAR(100)
cuenta_origen VARCHAR(50)
titular_origen VARCHAR(200)
```

**Validación:**
```sql
estado VARCHAR(30)
    -- PENDIENTE, VALIDANDO, APROBADO, RECHAZADO, INVALIDO
    
validado_por VARCHAR(50)
fecha_validacion DATETIME
motivo_rechazo TEXT
```

---

#### 6. **MÓDULO DE SORTEO** 🎰

##### `participantes`
Solo tickets APROBADOS participan:

```sql
numero_participacion INT
    -- Número secuencial para el sorteo (1, 2, 3...)
    -- Este número es el que se sortea
    
fue_seleccionado_intento TINYINT(1)
    -- Si fue seleccionado en algún intento
    
numero_intento_seleccionado INT
    -- En qué intento fue seleccionado (1-5)
```

##### `intentos_sorteo`
Registro de cada intento:

```sql
numero_intento INT
    -- 1, 2, 3, 4, 5...
    
numero_sorteado INT
    -- Número aleatorio que salió
    
es_ganador TINYINT(1)
    -- Si este intento determinó al ganador
    
hash_verificacion VARCHAR(255)
    -- Hash SHA256 para verificar transparencia
    -- Ejemplo: sha256(rifa_id + intento + timestamp + participante_id)
```

**Ejemplo de ejecución de sorteo:**

```javascript
// Rifa configurada: 5 intentos, gana el intento #5
const totalParticipantes = 150; // 150 personas compraron

// INTENTO 1
const numeroAleatorio1 = Math.floor(Math.random() * 150) + 1; // Resultado: 87
// Se marca en intentos_sorteo: numero_intento=1, numero_sorteado=87, es_ganador=0

// INTENTO 2
const numeroAleatorio2 = Math.floor(Math.random() * 150) + 1; // Resultado: 23
// Se marca en intentos_sorteo: numero_intento=2, numero_sorteado=23, es_ganador=0

// ... intentos 3 y 4 ...

// INTENTO 5 (EL GANADOR)
const numeroAleatorio5 = Math.floor(Math.random() * 150) + 1; // Resultado: 42
// Se marca en intentos_sorteo: numero_intento=5, numero_sorteado=42, es_ganador=1
// El participante con numero_participacion=42 GANA
```

##### `ganadores`
Registro del ganador:

```sql
-- Copia de datos del ganador (por inmutabilidad)
nombres_completos VARCHAR(200)
numero_documento VARCHAR(20)
email, telefono

-- Información del premio
premio_nombre VARCHAR(200)
premio_valor DECIMAL(12,2)

-- Entrega del premio
premio_entregado TINYINT(1) DEFAULT 0
fecha_entrega DATETIME
lugar_entrega VARCHAR(500)
entregado_por VARCHAR(100)

-- Documentación
foto_entrega VARCHAR(255)
    -- Foto del ganador recibiendo el premio
    
documento_entrega VARCHAR(255)
    -- Acta de entrega firmada
    
observaciones TEXT

-- Publicación
publicar_ganador TINYINT(1) DEFAULT 1
    -- Si se publica en la web
    
mensaje_felicitacion TEXT
    -- Mensaje para redes sociales
```

---

#### 7. **MÓDULO DE UBICACIONES** 📍

##### `ubicaciones_rifa`
Direcciones físicas donde se juega la rifa:

```sql
nombre VARCHAR(200)
    -- "Lima Centro", "Chiclayo Norte", etc.
    
ciudad VARCHAR(100)
    -- Lima, Chiclayo, Arequipa
    
departamento_region VARCHAR(100)
    -- Lima, Lambayeque, Arequipa
    
pais VARCHAR(100)
    -- Perú, Colombia, Chile

-- Geolocalización
latitud DECIMAL(10,8)
longitud DECIMAL(11,8)
url_mapa VARCHAR(255)
    -- URL de Google Maps

-- Información adicional
horario_atencion VARCHAR(500)
    -- "Lun-Vie 9am-6pm, Sáb 9am-1pm"
```

**Uso:**
- Mostrar en landing page dónde se puede validar tickets
- Dónde se entrega el premio
- Sucursales disponibles

---

#### 8. **MÓDULO DE CONFIGURACIÓN** ⚙️

##### `configuracion_sede`
Personalización por país/sede:

**Ejemplos de configuraciones:**

```sql
-- LANDING PAGE
'landing_titulo'           → 'Gana increíbles premios'
'landing_subtitulo'        → 'Participa en nuestros sorteos'
'landing_color_primario'   → '#FF5722'
'landing_color_secundario' → '#2196F3'
'landing_logo_url'         → '/assets/logo.png'

-- LÍMITES
'max_tickets_por_persona'  → 5
'tiempo_sesion_horas'      → 8
'dias_validez_ticket'      → 90

-- PAGOS
'metodos_pago_activos'     → ['yape', 'plin', 'transferencia']
'moneda_simbolo'           → 'S/.'
'requiere_validacion_manual' → true

-- NOTIFICACIONES
'email_notificaciones'     → 'soporte@rifas.com'
'whatsapp_notificaciones'  → '+51999888777'

-- REDES SOCIALES
'facebook_url'             → 'https://facebook.com/...'
'instagram_url'            → 'https://instagram.com/...'

-- TÉRMINOS Y CONDICIONES
'terminos_generales'       → '<html>...</html>'
'politica_privacidad'      → '<html>...</html>'
```

---

## 🔄 FLUJO COMPLETO DEL SISTEMA

### 📱 LANDING PAGE (Usuario Final)

#### 1️⃣ **Visualización de Rifas**
```
Usuario entra → Ve rifas activas → Filtros por categoría
                                  ↓
                        Ve contador regresivo
                        Ve tickets vendidos
                        Ve lista de premios destacados
```

#### 2️⃣ **Compra de Ticket**
```
Selecciona rifa → Llena formulario → Genera código único
                      ↓                      ↓
                 (Datos personales)   (PERU-20250124-123456)
                 - Nombres
                 - DNI
                 - Email
                 - Teléfono
```

#### 3️⃣ **Proceso de Pago**
```
Recibe instrucciones → Realiza pago → Sube comprobante
        ↓                   ↓              ↓
(Datos bancarios)    (Yape/Plin/    (Foto o PDF)
(Nro. cuenta)         Transferencia)
```

#### 4️⃣ **Validación**
```
Comprobante enviado → ESTADO: PAGO_SUBIDO → Espera validación
                                ↓
                    (Email: "Recibimos tu comprobante")
```

#### 5️⃣ **Validación de Ticket**
```
Usuario entra con código → Sistema valida → Muestra estado
(PERU-20250124-123456)         ↓
                        ✅ APROBADO
                        ❌ RECHAZADO
                        ⏳ EN VALIDACIÓN
```

---

### 🖥️ PORTAL ADMINISTRATIVO

#### 1️⃣ **Módulo de Premios**
```
Crear premio → Subir imágenes → Asignar categoría → Publicar
                    ↓
            (Múltiples imágenes)
            (Video promocional)
            (Especificaciones)
```

#### 2️⃣ **Módulo de Rifas**
```
Crear rifa → Seleccionar premio → Configurar sorteo → Publicar
                                        ↓
                            (Número de intentos: 5)
                            (Intento ganador: 5)
                            (Precio: S/. 10)
                            (Fecha sorteo)
```

#### 3️⃣ **Módulo de Validación de Pagos** ⭐
```
Lista de comprobantes pendientes
    ↓
Ver comprobante → Validar datos → APROBAR / RECHAZAR
    ↓                 ↓                  ↓
(Imagen)    (Nro. operación)    (Automáticamente
(PDF)       (Monto)             se crea participante)
(Datos)     (Fecha)
```

**Validaciones automáticas:**
- ✅ Monto coincide con precio de ticket
- ✅ Fecha de pago es reciente
- ✅ Número de operación no está duplicado
- ✅ Documento del comprador no excede límite de tickets

#### 4️⃣ **Módulo de Sorteo en Vivo** 🎰
```
Iniciar sorteo → Sortea intentos → Determina ganador → Notifica
                        ↓
            Intento 1: Nro. 87 ❌
            Intento 2: Nro. 23 ❌
            Intento 3: Nro. 145 ❌
            Intento 4: Nro. 67 ❌
            Intento 5: Nro. 42 ✅ GANADOR!
```

**Características del sorteo:**
- Se puede hacer en vivo por streaming
- Se genera hash de verificación
- Se registra cada intento
- Transparencia total

#### 5️⃣ **Módulo de Ganadores**
```
Ver ganador → Contactar → Coordinar entrega → Entregar premio
                ↓              ↓                    ↓
           (Email/Tel)    (Fecha/Lugar)    (Foto + Acta)
                                                    ↓
                                            Publicar ganador
```

#### 6️⃣ **Módulo de Reportes** 📊
- Total de tickets vendidos
- Total recaudado
- Tickets pendientes de validación
- Participantes por rifa
- Historial de ganadores
- Reportes financieros

---

## 🌍 ESCALABILIDAD MULTI-PAÍS

### Configuración por País:

#### 🇵🇪 **PERÚ**
```sql
INSERT INTO sedes VALUES (
    codigo: 'PERU-01',
    pais: 'Perú',
    moneda: 'Soles',
    simbolo_moneda: 'S/.',
    codigo_moneda: 'PEN',
    zona_horaria: 'America/Lima'
);

-- Métodos de pago: Yape, Plin, Transferencia BCP/Interbank
-- Documentos: DNI, CE
```

#### 🇨🇴 **COLOMBIA**
```sql
INSERT INTO sedes VALUES (
    codigo: 'COL-01',
    pais: 'Colombia',
    moneda: 'Pesos',
    simbolo_moneda: '$',
    codigo_moneda: 'COP',
    zona_horaria: 'America/Bogota'
);

-- Métodos de pago: Nequi, Daviplata, Transferencia Bancolombia
-- Documentos: CC (Cédula), CE
```

#### 🇨🇱 **CHILE**
```sql
INSERT INTO sedes VALUES (
    codigo: 'CHI-01',
    pais: 'Chile',
    moneda: 'Pesos',
    simbolo_moneda: '$',
    codigo_moneda: 'CLP',
    zona_horaria: 'America/Santiago'
);

-- Métodos de pago: Transferencia Banco de Chile, Santander
-- Documentos: RUT, Pasaporte
```

---

## 🔐 SEGURIDAD

### 1. **Autenticación JWT**
- Tokens con expiración
- Renovación automática
- Invalidación de sesiones anteriores

### 2. **Validación de Comprobantes**
- Validación manual por operadores capacitados
- Verificación de duplicados
- Límite de intentos por IP

### 3. **Transparencia del Sorteo**
- Hash de verificación (SHA256)
- Registro inmutable de intentos
- Posibilidad de auditoría externa

### 4. **Protección de Datos**
- Datos personales encriptados
- Cumplimiento RGPD/LGPD
- Políticas de privacidad por país

---

## 📈 PRÓXIMAS MEJORAS

### Fase 2:
- [ ] Pago con tarjeta (Stripe/PayPal)
- [ ] Chat en vivo para soporte
- [ ] App móvil (React Native)
- [ ] Transmisión en vivo del sorteo
- [ ] Sistema de referidos

### Fase 3:
- [ ] Rifas colaborativas (varios premios)
- [ ] Sistema de puntos y descuentos
- [ ] Marketplace de premios
- [ ] Integración con redes sociales
- [ ] Bot de WhatsApp

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

### Backend:
- [x] Diseño de base de datos MySQL
- [x] Stored procedures de autenticación
- [x] Stored procedures de rifas
- [ ] API REST con PHP
- [ ] Sistema de upload de archivos
- [ ] Generación de PDFs
- [ ] Sistema de emails

### Frontend - Landing Page:
- [ ] Diseño UI/UX moderno
- [ ] Listado de rifas activas
- [ ] Formulario de compra
- [ ] Sistema de validación de tickets
- [ ] Contador regresivo
- [ ] Galería de ganadores

### Frontend - Portal Admin:
- [ ] Dashboard con estadísticas
- [ ] CRUD de premios
- [ ] CRUD de rifas
- [ ] Módulo de validación de pagos
- [ ] Módulo de sorteo en vivo
- [ ] Gestión de ganadores
- [ ] Reportes y gráficos

---

## 🚀 CONCLUSIÓN

Este sistema es **100% escalable y personalizable** para adaptarse a diferentes países, regulaciones y métodos de pago. La arquitectura modular permite agregar funcionalidades sin afectar el core del sistema.

**Ventajas:**
✅ Multi-sede real (datos aislados por país)
✅ Sistema de sorteo transparente y auditable
✅ Validación manual/automática de pagos
✅ Generación de tickets únicos
✅ Notificaciones automatizadas
✅ Reportes en tiempo real
✅ Interfaz moderna y responsive

---

**Autor:** Sistema de Rifas Multisede v1.0
**Fecha:** Enero 2025
**Base de Datos:** MySQL 8.0+
**Backend:** PHP 8.1+
**Frontend:** React / Vue (a definir)

