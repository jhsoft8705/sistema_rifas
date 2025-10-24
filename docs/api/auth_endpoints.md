# API de Autenticación - Endpoints

Documentación de los endpoints de autenticación del sistema.

---

## Base URL
```
http://localhost/CONTROL_ASISTENCIA_CAFED/
```

---

## 1. Login de Usuario

Autentica un usuario y genera un token de sesión.

### Endpoint
```http
POST /api/auth/login
```

### Headers
```
Content-Type: application/json
```

### Body
```json
{
    "username": "admin",
    "password": "123456",
    "sede_id": 1
}
```

**Parámetros:**
- `username` (string, requerido): Usuario o correo electrónico
- `password` (string, requerido): Contraseña del usuario
- `sede_id` (int, opcional): ID de la sede (si el usuario tiene acceso a múltiples sedes)

### Respuestas

#### ✅ Login Exitoso (200 OK)
```json
{
    "ok": true,
    "msj": "Login exitoso",
    "data": {
        "usuario_id": 1,
        "token": "A1B2C3D4-E5F6-G7H8-I9J0-K1L2M3N4O5P6",
        "fecha_expiracion": "2025-10-14 18:30:00",
        "sede_id": 1,
        "empleado_id": 5,
        "nombre_completo": "Juan Pérez García",
        "debe_cambiar_password": null
    }
}
```

#### ❌ Usuario No Encontrado (404 Not Found)
```json
{
    "ok": false,
    "msj": "Usuario no encontrado",
    "data": null
}
```

#### ❌ Contraseña Incorrecta (401 Unauthorized)
```json
{
    "ok": false,
    "msj": "Contraseña incorrecta",
    "data": null
}
```

#### ❌ Usuario Inactivo (403 Forbidden)
```json
{
    "ok": false,
    "msj": "Usuario inactivo",
    "data": null
}
```

#### ❌ Cuenta Bloqueada (403 Forbidden)
```json
{
    "ok": false,
    "msj": "Cuenta bloqueada",
    "data": null
}
```

#### ❌ Usuario No Pertenece a la Sede (401 Unauthorized)
```json
{
    "ok": false,
    "msj": "Usuario no pertenece a esta sede",
    "data": null
}
```

#### ❌ Múltiples Intentos Fallidos (401 Unauthorized)
```json
{
    "ok": false,
    "msj": "Contraseña incorrecta. Cuenta bloqueada por múltiples intentos fallidos",
    "data": null
}
```

---

## 2. Logout de Usuario

Cierra la sesión activa de un usuario.

### Endpoint
```http
POST /api/auth/logout
```

### Headers
```
Content-Type: application/json
```

### Body
```json
{
    "token": "A1B2C3D4-E5F6-G7H8-I9J0-K1L2M3N4O5P6"
}
```

**Parámetros:**
- `token` (string, requerido): Token de sesión del usuario

### Respuestas

#### ✅ Logout Exitoso (200 OK)
```json
{
    "ok": true,
    "msj": "Sesión cerrada correctamente"
}
```

#### ❌ Token Inválido (400 Bad Request)
```json
{
    "ok": false,
    "msj": "Token de sesión no válido o ya expirado"
}
```

---

## 3. Verificar Sesión

Verifica si un token de sesión es válido y está activo.

### Endpoint
```http
POST /api/auth/verificar
```

### Headers
```
Content-Type: application/json
```

### Body
```json
{
    "token": "A1B2C3D4-E5F6-G7H8-I9J0-K1L2M3N4O5P6"
}
```

**Parámetros:**
- `token` (string, requerido): Token de sesión a verificar

### Respuestas

#### ✅ Sesión Válida (200 OK)
```json
{
    "ok": true,
    "msj": "Sesión válida",
    "data": {
        "usuario_id": 1,
        "sede_id": 1,
        "empleado_id": 5,
        "nombre_completo": "Juan Pérez García"
    }
}
```

#### ❌ Sesión Inválida (401 Unauthorized)
```json
{
    "ok": false,
    "msj": "Sesión inválida o expirada",
    "data": null
}
```

---

## Seguridad

### Control de Intentos Fallidos
- El sistema registra todos los intentos de login (exitosos y fallidos)
- Después de **5 intentos fallidos**, la cuenta se bloquea automáticamente
- Los intentos fallidos incluyen:
  - Contraseña incorrecta
  - Usuario no encontrado
  - Usuario inactivo

### Bloqueo de Cuenta
Cuando una cuenta es bloqueada:
- Se establece el campo `cuenta_bloqueada = 1`
- Se registra la `fecha_bloqueo`
- El usuario no puede iniciar sesión hasta que un administrador lo desbloquee

### Tokens de Sesión
- Los tokens se generan automáticamente usando `NEWID()` (UUID)
- Tiempo de expiración: **8 horas** por defecto
- Los tokens se invalidan al hacer logout
- Un token solo puede estar asociado a una sesión activa

### Registro de Actividad
Todas las actividades de autenticación se registran en la tabla `intentos_acceso`:
- Username utilizado
- IP del cliente
- User agent del navegador
- Si fue exitoso o fallido
- Motivo del fallo (si aplica)
- Fecha y hora del intento

---

## Ejemplos de Uso

### Ejemplo con cURL

#### Login
```bash
curl -X POST http://localhost/CONTROL_ASISTENCIA_CAFED/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "123456",
    "sede_id": 1
  }'
```

#### Logout
```bash
curl -X POST http://localhost/CONTROL_ASISTENCIA_CAFED/api/auth/logout \
  -H "Content-Type: application/json" \
  -d '{
    "token": "A1B2C3D4-E5F6-G7H8-I9J0-K1L2M3N4O5P6"
  }'
```

#### Verificar Sesión
```bash
curl -X POST http://localhost/CONTROL_ASISTENCIA_CAFED/api/auth/verificar \
  -H "Content-Type: application/json" \
  -d '{
    "token": "A1B2C3D4-E5F6-G7H8-I9J0-K1L2M3N4O5P6"
  }'
```

### Ejemplo con JavaScript (Fetch API)

```javascript
// Login
async function login(username, password, sedeId = null) {
    try {
        const response = await fetch('http://localhost/CONTROL_ASISTENCIA_CAFED/api/auth/login', {
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
            localStorage.setItem('token', data.data.token);
            localStorage.setItem('usuario', JSON.stringify(data.data));
            console.log('Login exitoso:', data);
        } else {
            console.error('Error en login:', data.msj);
        }
        
        return data;
    } catch (error) {
        console.error('Error de red:', error);
    }
}

// Logout
async function logout(token) {
    try {
        const response = await fetch('http://localhost/CONTROL_ASISTENCIA_CAFED/api/auth/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                token: token
            })
        });

        const data = await response.json();
        
        if (data.ok) {
            // Limpiar localStorage
            localStorage.removeItem('token');
            localStorage.removeItem('usuario');
            console.log('Logout exitoso');
        }
        
        return data;
    } catch (error) {
        console.error('Error de red:', error);
    }
}

// Verificar Sesión
async function verificarSesion(token) {
    try {
        const response = await fetch('http://localhost/CONTROL_ASISTENCIA_CAFED/api/auth/verificar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                token: token
            })
        });

        const data = await response.json();
        return data;
    } catch (error) {
        console.error('Error de red:', error);
    }
}

// Uso
login('admin', '123456', 1).then(result => {
    if (result && result.ok) {
        const token = result.data.token;
        // Usar el token en futuras peticiones
    }
});
```

---

## Notas Importantes

1. **Contraseñas**: El stored procedure recibe la contraseña en texto plano. Asegúrate de usar HTTPS en producción.

2. **IP y User Agent**: Se capturan automáticamente desde `$_SERVER['REMOTE_ADDR']` y `$_SERVER['HTTP_USER_AGENT']`.

3. **Sesión PHP**: Además del token, se guarda información en `$_SESSION['usuario']` y `$_SESSION['token']`.

4. **Multi-sede**: Si un usuario tiene acceso a múltiples sedes, puede especificar `sede_id` en el login. Si no se especifica, se usará la sede del usuario.

5. **Expiración**: Los tokens expiran automáticamente después de 8 horas de inactividad.

---

## Códigos HTTP

| Código | Descripción |
|--------|-------------|
| 200 | Operación exitosa |
| 400 | Petición mal formada (JSON inválido, campos faltantes) |
| 401 | No autorizado (credenciales incorrectas, sesión inválida) |
| 403 | Acceso prohibido (usuario inactivo, cuenta bloqueada) |
| 404 | Usuario no encontrado |
| 405 | Método HTTP no permitido |
| 500 | Error interno del servidor |


