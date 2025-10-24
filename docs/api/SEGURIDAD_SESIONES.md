# Seguridad de Sesiones - Invalidación de Tokens Anteriores

## Problema Identificado

❌ **Problema**: Cuando un usuario hacía login múltiples veces, todos los tokens anteriores seguían siendo válidos, permitiendo el acceso con tokens antiguos.

**Riesgo de Seguridad:**
- Un usuario podía tener múltiples sesiones activas simultáneamente
- Si un token era robado, seguía funcionando incluso después de un nuevo login
- No había forma de invalidar sesiones anteriores automáticamente

---

## Solución Implementada

✅ **Solución**: Al hacer login, se invalidan automáticamente todas las sesiones anteriores del usuario.

**Comportamiento Actual:**
1. Usuario hace login → Recibe Token A
2. Usuario hace login nuevamente → Recibe Token B
3. Token A se invalida automáticamente
4. Solo Token B funciona
5. Intentar usar Token A retorna error 401

---

## Cambios Realizados

### 📝 Stored Procedure `sp_Login`

Se agregó esta línea antes de crear la nueva sesión:

```sql
-- INVALIDAR TODAS LAS SESIONES ANTERIORES DEL USUARIO
UPDATE sesiones 
SET activa = 0,
    fecha_modificacion = GETDATE()
WHERE usuario_id = @usuario_id 
  AND activa = 1;
```

**Ubicación en el procedimiento:**
- Se ejecuta DESPUÉS de validar la contraseña
- Se ejecuta ANTES de crear la nueva sesión
- Solo afecta las sesiones del usuario que está haciendo login

---

## Cómo Funciona

### Flujo de Invalidación

```
1. Usuario hace Login (1er vez)
   └─> Se crea Sesión A (activa = 1)
       └─> Token A es válido ✓

2. Usuario hace Login (2da vez)
   └─> Se INVALIDAN todas las sesiones anteriores
       └─> Sesión A: activa = 0 ✗
   └─> Se crea Sesión B (activa = 1)
       └─> Token B es válido ✓
       └─> Token A ahora es inválido ✗

3. Usuario intenta usar Token A
   └─> AuthMiddleware verifica token
       └─> sesiones WHERE token = A AND activa = 1
           └─> No encuentra resultados
               └─> Retorna 401 Unauthorized ✗
```

---

## Verificación Manual

### Pasos para Probar:

#### 1. Primer Login
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
    "data": {
        "token": "TOKEN_1_AQUI",
        ...
    }
}
```

**Guardar TOKEN_1**

---

#### 2. Probar TOKEN_1 (debe funcionar)
```bash
curl -X GET "http://localhost/CONTROL_ASISTENCIA_CAFED/api/cargos/getAll?sede_id=1" \
  -H "Authorization: Bearer TOKEN_1_AQUI"
```

**Resultado esperado:** ✓ Funciona (200 OK)

---

#### 3. Segundo Login
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
    "data": {
        "token": "TOKEN_2_DIFERENTE",
        ...
    }
}
```

**Guardar TOKEN_2**

---

#### 4. Probar TOKEN_1 nuevamente (debe fallar)
```bash
curl -X GET "http://localhost/CONTROL_ASISTENCIA_CAFED/api/cargos/getAll?sede_id=1" \
  -H "Authorization: Bearer TOKEN_1_AQUI"
```

**Resultado esperado:** ✗ Error 401
```json
{
    "ok": false,
    "msj": "Acceso denegado: Token inválido o expirado"
}
```

---

#### 5. Probar TOKEN_2 (debe funcionar)
```bash
curl -X GET "http://localhost/CONTROL_ASISTENCIA_CAFED/api/cargos/getAll?sede_id=1" \
  -H "Authorization: Bearer TOKEN_2_DIFERENTE"
```

**Resultado esperado:** ✓ Funciona (200 OK)

---

## Verificación en Base de Datos

### Ver sesiones del usuario:
```sql
SELECT 
    id,
    token_sesion,
    CASE 
        WHEN activa = 1 THEN 'ACTIVA ✓'
        ELSE 'INACTIVA ✗'
    END AS Estado,
    fecha_creacion,
    fecha_expiracion,
    ip_address
FROM sesiones 
WHERE usuario_id = 1
ORDER BY fecha_creacion DESC;
```

**Resultado esperado:**
```
id  token_sesion     Estado        fecha_creacion
--  ---------------  -----------   ----------------
2   TOKEN_2...       ACTIVA ✓      2025-10-14 15:30
1   TOKEN_1...       INACTIVA ✗    2025-10-14 15:20
```

Solo debe haber **UNA sesión activa** por usuario.

---

## Script de Prueba Automatizado

Ejecutar el script de prueba:

```sql
-- Ejecutar en SQL Server Management Studio:
-- docs\sql\test_invalidar_sesiones.sql
```

Este script:
1. Hace dos logins consecutivos
2. Verifica que solo hay 1 sesión activa
3. Verifica que las anteriores están inactivas
4. Muestra resultado: PRUEBA EXITOSA o FALLIDA

---

## Consultas Útiles

### Ver todas las sesiones activas
```sql
SELECT 
    u.username,
    s.token_sesion,
    s.fecha_creacion,
    s.fecha_expiracion,
    s.ip_address
FROM sesiones s
INNER JOIN usuarios u ON s.usuario_id = u.id
WHERE s.activa = 1
ORDER BY s.fecha_creacion DESC;
```

### Contar sesiones por usuario
```sql
SELECT 
    u.username,
    COUNT(*) AS total_sesiones,
    SUM(CASE WHEN s.activa = 1 THEN 1 ELSE 0 END) AS sesiones_activas,
    SUM(CASE WHEN s.activa = 0 THEN 1 ELSE 0 END) AS sesiones_inactivas
FROM sesiones s
INNER JOIN usuarios u ON s.usuario_id = u.id
GROUP BY u.username;
```

### Invalidar manualmente todas las sesiones de un usuario
```sql
UPDATE sesiones 
SET activa = 0 
WHERE usuario_id = 1;
```

---

## Casos de Uso

### ✅ Caso 1: Login desde diferentes dispositivos
```
Usuario hace login desde PC → Token PC
Usuario hace login desde móvil → Token Móvil
Token PC se invalida automáticamente
Solo Token Móvil funciona
```

### ✅ Caso 2: Token comprometido
```
Token del usuario es robado → Atacante usa Token Robado
Usuario nota actividad sospechosa → Hace login nuevamente
Token Robado se invalida automáticamente
Atacante pierde acceso
Usuario tiene nuevo token seguro
```

### ✅ Caso 3: Cambio de contraseña
```
Usuario sospecha compromiso → Hace login con nueva contraseña
Todos los tokens anteriores se invalidan
Solo el nuevo token funciona
```

---

## Configuración de Sesiones

| Parámetro | Valor | Descripción |
|-----------|-------|-------------|
| **Duración** | 8 horas | Tiempo de validez del token |
| **Renovación** | Automática | Se actualiza `fecha_ultima_actividad` con cada petición |
| **Sesiones simultáneas** | 1 | Solo una sesión activa por usuario |
| **Invalidación** | Automática | Al hacer nuevo login |
| **Tipo de token** | UUID | Generado con `NEWID()` |

---

## Recomendaciones de Seguridad

### ✅ Implementado
- ✓ Invalidación automática de sesiones anteriores
- ✓ Token único por usuario
- ✓ Expiración de 8 horas
- ✓ Renovación automática con actividad
- ✓ Registro de todos los intentos de acceso

### 📋 Recomendaciones Adicionales
- 🔒 Implementar HTTPS en producción
- 🔒 Agregar refresh tokens para renovación segura
- 🔒 Limitar intentos de login por IP
- 🔒 Notificar al usuario de logins desde nuevos dispositivos
- 🔒 Permitir al usuario ver y cerrar sesiones activas desde su perfil

---

## Troubleshooting

### Problema: "Token inválido" después de hacer login
**Causa**: Estás usando un token anterior
**Solución**: Usa el token más reciente que recibiste en el último login

### Problema: Usuario se desconecta inesperadamente
**Causa**: Hizo login desde otro dispositivo/navegador
**Solución**: Esto es correcto, solo puede tener una sesión activa

### Problema: Token expira muy rápido
**Causa**: El token expira después de 8 horas de inactividad
**Solución**: Configurable en el stored procedure (línea: `DATEADD(HOUR, 8, GETDATE())`)

---

## Resumen

### Antes del Fix ❌
```
Login 1 → Token A ✓ válido
Login 2 → Token B ✓ válido
Token A ✓ sigue válido (PROBLEMA)
Token B ✓ válido
```

### Después del Fix ✅
```
Login 1 → Token A ✓ válido
Login 2 → Token B ✓ válido
Token A ✗ INVÁLIDO (CORRECTO)
Token B ✓ válido
```

---

## Archivos Modificados

- ✅ `docs\sql\auth_actualizado.sql` - Stored procedure actualizado
- ✅ `docs\sql\test_invalidar_sesiones.sql` - Script de prueba
- ✅ `docs\api\SEGURIDAD_SESIONES.md` - Esta documentación

---

## Conclusión

El sistema ahora es más seguro:
- ✅ Solo un token válido por usuario
- ✅ Tokens anteriores se invalidan automáticamente
- ✅ Mejor control de sesiones
- ✅ Prevención de uso de tokens antiguos o robados

🔒 **Tu API está más segura ahora!**


