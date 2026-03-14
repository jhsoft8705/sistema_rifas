# Diagnóstico Error 500 - admin-login

## Causas más probables y soluciones

### 1. **Sensibilidad a mayúsculas (Linux/Hostinger)**

En Linux los nombres de archivo distinguen mayúsculas. El archivo debe llamarse `config/conexion.php` (todo minúsculas) y todos los `require` usan ese nombre.

---

### 2. **Credenciales de base de datos**

Si `config/conexion.php` tiene `TU_PASSWORD_BD` sin reemplazar, o credenciales incorrectas, la conexión falla.

**Verificar:**
- En Hostinger: Panel → Bases de datos MySQL → ver usuario y contraseña
- En `config/conexion.php`: `$ambiente = 'prod'` y credenciales correctas

---

### 3. **Ver el error real (recomendado)**

Para saber el error exacto, activa temporalmente la visualización de errores.

**Opción A – En el proyecto:** Crea o edita `config/errores.php` y añade al inicio de `web/router.php` (línea 1):

```php
<?php
// SOLO PARA DIAGNÓSTICO - QUITAR EN PRODUCCIÓN
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . "/../config/conexion.php";
```

**Opción B – En el servidor:** En Hostinger, revisa los logs de error:
- Panel Hostinger → Archivos → Logs de error
- O archivo `error_log` en la raíz del proyecto

---

### 4. **Archivos que deben existir en producción**

| Archivo | Ruta |
|---------|------|
| conexion.php | config/ |
| Enrutamiento.php | config/ |
| router.php | web/ |
| index.php | raíz |
| .htaccess | raíz |
| api/.htaccess | api/ |

---

### 5. **Configuración de conexion.php para producción**

```php
private $ambiente = 'prod';

// En el bloque else (producción):
$conectar = $this->dbh = new PDO(
    "mysql:host=localhost;dbname=u696088465_bd_sorteos",
    "u696088465_user_sorteos",
    "TU_PASSWORD_REAL"  // ← Reemplazar
);
```

---

### 6. **Orden de carga (flujo admin-login)**

1. `.htaccess` redirige a `web/router.php`
2. `router.php` hace `require config/conexion.php`
3. `config/conexion.php` → `session_start()`
4. Router incluye `views/login/index.php`
5. Login hace `require Enrutamiento.php`

Cualquier fallo en estos pasos puede generar 500.

---

## Error 401 "Error al procesar el login"

Si el login devuelve 401 con ese mensaje, suele ser un **error de base de datos** (PDOException):

1. **sp_Login no existe** en la BD de producción. Ejecutar `docs/sql/auth.sql` en la BD.
2. **Usuario admin no existe** en producción. Crear el usuario o importar datos de ejemplo.
3. **Credenciales de BD incorrectas** en `config/conexion.php` (host, usuario, contraseña).
4. **Host de BD**: En Hostinger suele ser `localhost` o el host que indique el panel. Si usas IP remota (88.223.84.166), verificar que el servidor permita conexiones remotas.

---

## Checklist rápido

- [ ] Subir `config/conexion.php`
- [ ] Reemplazar `TU_PASSWORD_BD` en conexion.php
- [ ] Confirmar `$ambiente = 'prod'` en Conexion y Enrutamiento
- [ ] Revisar logs de error en Hostinger
- [ ] Comprobar que la base de datos existe y el usuario tiene permisos
