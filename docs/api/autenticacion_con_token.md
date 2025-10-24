# Autenticación con Token

## Descripción
El sistema utiliza autenticación basada en tokens (Bearer Token) para proteger los endpoints de la API.

---

## Flujo de Autenticación

### 1. Login
El usuario se autentica y recibe un token de sesión válido por 8 horas.

### 2. Usar el Token
El token debe incluirse en cada petición a endpoints protegidos mediante el header `Authorization`.

### 3. Renovación
El token se renueva automáticamente con cada petición exitosa (actualiza `fecha_ultima_actividad`).

### 4. Expiración
Después de 8 horas sin actividad, el token expira y debe hacer login nuevamente.

---

## Cómo Usar el Token

### Opción 1: Header Authorization (Recomendado)
```
Authorization: Bearer 8D26B824-2780-4F7E-A0DD-2DA4877D2458
```

### Opción 2: Header X-Auth-Token
```
X-Auth-Token: 8D26B824-2780-4F7E-A0DD-2DA4877D2458
```

---

## Ejemplos de Uso

### 📌 Paso 1: Hacer Login

```bash
curl -X POST http://localhost/CONTROL_ASISTENCIA_CAFED/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123",
    "sede_id": 1
  }'
```

**Respuesta:**
```json
{
    "ok": true,
    "msj": "Login exitoso",
    "data": {
        "usuario_id": "1",
        "token": "8D26B824-2780-4F7E-A0DD-2DA4877D2458",
        "fecha_expiracion": "2025-10-14 23:50:11.710",
        "sede_id": "1",
        "sede_nombre": "Sede Principal - CAFED",
        "empleado_id": null,
        "nombre_completo": "Administrador Sistema",
        "rol_id": "1",
        "rol_nombre": "Administrador",
        "debe_cambiar_password": null
    }
}
```

---

### 📌 Paso 2: Usar el Token en Endpoints Protegidos

```bash
curl -X GET "http://localhost/CONTROL_ASISTENCIA_CAFED/api/cargos/getAll?sede_id=1" \
  -H "Authorization: Bearer 8D26B824-2780-4F7E-A0DD-2DA4877D2458"
```

**Respuesta exitosa:**
```json
{
    "ok": true,
    "msj": "Cargos obtenidos correctamente",
    "data": [ ... ]
}
```

---

### 📌 Paso 3: Manejar Errores de Autenticación

#### Sin Token
```bash
curl -X GET "http://localhost/CONTROL_ASISTENCIA_CAFED/api/cargos/getAll?sede_id=1"
```

**Respuesta (401 Unauthorized):**
```json
{
    "ok": false,
    "msj": "Acceso denegado: Token no proporcionado"
}
```

#### Token Inválido o Expirado
```bash
curl -X GET "http://localhost/CONTROL_ASISTENCIA_CAFED/api/cargos/getAll?sede_id=1" \
  -H "Authorization: Bearer TOKEN_INVALIDO"
```

**Respuesta (401 Unauthorized):**
```json
{
    "ok": false,
    "msj": "Acceso denegado: Token inválido o expirado"
}
```

---

## Ejemplos con JavaScript (Fetch API)

### Ejemplo Completo

```javascript
// Configuración base
const API_BASE_URL = 'http://localhost/CONTROL_ASISTENCIA_CAFED/api';
let authToken = null;

// ============================================
// 1. FUNCIÓN DE LOGIN
// ============================================
async function login(username, password, sedeId = 1) {
    try {
        const response = await fetch(`${API_BASE_URL}/auth/login`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                username: username,
                password: password,
                sede_id: sedeId
            })
        });

        const data = await response.json();
        
        if (data.ok) {
            // Guardar token en localStorage
            authToken = data.data.token;
            localStorage.setItem('token', authToken);
            localStorage.setItem('usuario', JSON.stringify(data.data));
            
            console.log('Login exitoso:', data.data);
            return data;
        } else {
            console.error('Error en login:', data.msj);
            return data;
        }
    } catch (error) {
        console.error('Error de red:', error);
        return { ok: false, msj: 'Error de conexión' };
    }
}

// ============================================
// 2. FUNCIÓN PARA PETICIONES AUTENTICADAS
// ============================================
async function fetchWithAuth(url, options = {}) {
    // Obtener token de localStorage
    const token = localStorage.getItem('token');
    
    if (!token) {
        console.error('No hay token. Debe hacer login primero.');
        return { ok: false, msj: 'No autenticado' };
    }

    // Agregar header de autorización
    const headers = {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
        ...options.headers
    };

    try {
        const response = await fetch(url, {
            ...options,
            headers: headers
        });

        const data = await response.json();

        // Si el token es inválido, limpiar localStorage
        if (response.status === 401) {
            localStorage.removeItem('token');
            localStorage.removeItem('usuario');
            authToken = null;
            console.error('Sesión expirada. Debe hacer login nuevamente.');
        }

        return data;
    } catch (error) {
        console.error('Error de red:', error);
        return { ok: false, msj: 'Error de conexión' };
    }
}

// ============================================
// 3. EJEMPLOS DE USO
// ============================================

// Login
async function ejemploLogin() {
    const resultado = await login('admin', 'admin123', 1);
    
    if (resultado.ok) {
        console.log('Usuario:', resultado.data.nombre_completo);
        console.log('Rol:', resultado.data.rol_nombre);
        console.log('Sede:', resultado.data.sede_nombre);
        console.log('Token:', resultado.data.token);
    }
}

// Listar cargos (requiere autenticación)
async function listarCargos(sedeId) {
    const url = `${API_BASE_URL}/cargos/getAll?sede_id=${sedeId}`;
    const resultado = await fetchWithAuth(url, { method: 'GET' });
    
    if (resultado.ok) {
        console.log('Cargos:', resultado.data);
        return resultado.data;
    } else {
        console.error('Error:', resultado.msj);
        return null;
    }
}

// Registrar cargo (requiere autenticación)
async function registrarCargo(sedeId, nombreCargo, descripcion, salarioBase, creadoPor) {
    const url = `${API_BASE_URL}/cargos/register`;
    const resultado = await fetchWithAuth(url, {
        method: 'POST',
        body: JSON.stringify({
            sede_id: sedeId,
            nombre_cargo: nombreCargo,
            descripcion: descripcion,
            salario_base: salarioBase,
            creado_por: creadoPor
        })
    });
    
    if (resultado.ok) {
        console.log('Cargo registrado:', resultado.msj);
        return resultado;
    } else {
        console.error('Error:', resultado.msj);
        return null;
    }
}

// Logout
async function logout() {
    const token = localStorage.getItem('token');
    
    if (token) {
        const url = `${API_BASE_URL}/auth/logout`;
        await fetchWithAuth(url, {
            method: 'POST',
            body: JSON.stringify({ token: token })
        });
    }
    
    // Limpiar datos locales
    localStorage.removeItem('token');
    localStorage.removeItem('usuario');
    authToken = null;
    console.log('Sesión cerrada');
}

// ============================================
// 4. USO PRÁCTICO
// ============================================

// Al cargar la aplicación, verificar si hay token guardado
window.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    const usuario = JSON.parse(localStorage.getItem('usuario') || 'null');
    
    if (token && usuario) {
        authToken = token;
        console.log('Sesión activa:', usuario.nombre_completo);
        // Continuar con la aplicación...
    } else {
        console.log('No hay sesión activa. Mostrando login...');
        // Mostrar formulario de login...
    }
});

// Ejemplo de flujo completo
async function ejemploCompleto() {
    // 1. Login
    console.log('=== PASO 1: LOGIN ===');
    const loginResult = await login('admin', 'admin123', 1);
    
    if (!loginResult.ok) {
        console.error('No se pudo hacer login');
        return;
    }
    
    // 2. Listar cargos
    console.log('\n=== PASO 2: LISTAR CARGOS ===');
    const cargos = await listarCargos(1);
    
    // 3. Registrar un cargo
    console.log('\n=== PASO 3: REGISTRAR CARGO ===');
    await registrarCargo(
        1,
        'Gerente de Ventas',
        'Responsable del área de ventas',
        3500.00,
        'admin'
    );
    
    // 4. Logout
    console.log('\n=== PASO 4: LOGOUT ===');
    await logout();
}
```

---

## Rutas Públicas (No requieren token)

Estas rutas pueden accederse sin autenticación:

- `POST /api/auth/login` - Login de usuario
- `POST /api/auth/verificar` - Verificar si un token es válido

---

## Rutas Protegidas (Requieren token)

Todas las demás rutas requieren autenticación:

- `GET /api/cargos/getAll` - Listar cargos
- `GET /api/cargos/getById` - Obtener cargo por ID
- `POST /api/cargos/register` - Registrar cargo
- `POST /api/cargos/update` - Actualizar cargo
- `POST /api/cargos/delete` - Eliminar cargo
- `POST /api/auth/logout` - Cerrar sesión
- ... (todas las demás rutas)

---

## Códigos de Estado HTTP

| Código | Descripción |
|--------|-------------|
| 200 | Petición exitosa |
| 201 | Recurso creado exitosamente |
| 400 | Petición mal formada |
| 401 | No autenticado o token inválido |
| 403 | No autorizado (sin permisos) |
| 404 | Recurso no encontrado |
| 500 | Error del servidor |

---

## Mejores Prácticas

### ✅ Hacer

1. **Guardar el token de forma segura** (localStorage o sessionStorage)
2. **Incluir el token en TODAS las peticiones protegidas**
3. **Manejar errores 401** (redirigir al login)
4. **Limpiar el token al hacer logout**
5. **Verificar la expiración del token** periódicamente

### ❌ Evitar

1. ❌ Exponer el token en la URL (query params)
2. ❌ Guardar el token en cookies sin flag httpOnly
3. ❌ No validar respuestas 401
4. ❌ Reutilizar tokens expirados
5. ❌ No hacer logout al cerrar la aplicación

---

## Seguridad

### Información del Token

- **Formato**: UUID (Universally Unique Identifier)
- **Generación**: `NEWID()` en SQL Server
- **Validez**: 8 horas desde el último uso
- **Almacenamiento**: Tabla `sesiones` en la base de datos
- **Renovación**: Automática con cada petición exitosa

### Recomendaciones de Seguridad

1. **HTTPS en producción** - Siempre usar HTTPS para evitar que el token sea interceptado
2. **No compartir tokens** - Cada usuario debe tener su propio token
3. **Cerrar sesión** - Hacer logout al terminar de usar la aplicación
4. **Timeouts** - El token expira después de 8 horas de inactividad
5. **Validación constante** - El servidor valida el token en cada petición

---

## Troubleshooting

### Problema: "Token no proporcionado"
**Solución**: Verifica que estás enviando el header `Authorization` con el formato correcto: `Bearer TOKEN`

### Problema: "Token inválido o expirado"
**Solución**: El token puede haber expirado (8 horas) o ser inválido. Haz login nuevamente.

### Problema: "Sesión expirada"
**Solución**: El token ha expirado. El usuario debe autenticarse nuevamente.

### Problema: CORS error
**Solución**: Los headers CORS ya están configurados en `api/index.php`. Verifica que estés haciendo las peticiones desde un origen permitido.

---

## Resumen

1. ✅ Haz **login** y guarda el **token**
2. ✅ Incluye el **token** en el header `Authorization: Bearer TOKEN` en todas las peticiones
3. ✅ Maneja errores **401** (redirige al login)
4. ✅ Haz **logout** al cerrar la aplicación
5. ✅ El token expira en **8 horas** de inactividad

¡Ya tienes tu API protegida con autenticación por token! 🔒


