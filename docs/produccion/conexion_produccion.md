# Configuración de Conexión para Producción

Sistema de Rifas - GanadoresYa  
**Dominio:** jhsoftperu.com  
**Subdominio:** https://ganadoresya.jhsoftperu.com/

---

## 1. URLs de Producción

| Tipo       | URL                              |
|------------|----------------------------------|
| Dominio    | https://jhsoftperu.com           |
| Subdominio | https://ganadoresya.jhsoftperu.com/ |

El sistema en producción se aloja en el **subdominio** `ganadoresya.jhsoftperu.com`.

---

## 2. Archivos a Configurar

### 2.1 `config/Conexion.php`

Para producción, establecer:

```php
private $ambiente = 'prod';
```

Y en el bloque `else` (PRODUCCION):

```php
} else {
    // PRODUCCION - ganadoresya.jhsoftperu.com
    $conectar = $this->dbh = new PDO(
        "mysql:host=HOST_BD;dbname=NOMBRE_BD",
        "USUARIO_BD",
        "PASSWORD_BD"
    );
    $conectar->exec("SET time_zone = '-05:00';");
}
```

**Importante:** Reemplazar `HOST_BD`, `NOMBRE_BD`, `USUARIO_BD` y `PASSWORD_BD` con las credenciales reales del servidor de producción.

---

### 2.2 `config/Enrutamiento.php`

Para producción, establecer:

```php
private $ambiente = 'prod';
```

Y en el bloque `else`:

```php
} else {
    return "https://ganadoresya.jhsoftperu.com"; // prod
}
```

---

### 2.3 `Conectar::obtenerBaseUrl()`

En `config/Conexion.php`, método `obtenerBaseUrl()`:

```php
} else {
    return "/"; // prod - raíz del subdominio ganadoresya.jhsoftperu.com
}
```

Si la aplicación está en un subdirectorio (ej: `/sistema_rifas/`), ajustar según la estructura del servidor.

---

## 3. Resumen de Cambios para Producción

| Archivo              | Variable/Campo | Valor Producción                    |
|----------------------|---------------|-------------------------------------|
| `config/Conexion.php`| `$ambiente`    | `'prod'`                            |
| `config/Conexion.php`| PDO (prod)    | Credenciales BD del hosting         |
| `config/Conexion.php`| `obtenerBaseUrl()` | `/` o ruta según instalación   |
| `config/Enrutamiento.php` | `$ambiente` | `'prod'`                     |
| `config/Enrutamiento.php` | `dominio()` | `https://ganadoresya.jhsoftperu.com` |

---

## 4. Checklist Pre-Despliegue

- [ ] Cambiar `$ambiente` a `'prod'` en `Conexion.php` y `Enrutamiento.php`
- [ ] Configurar credenciales de base de datos de producción
- [ ] Verificar que la URL base coincida con la instalación (subdominio o subcarpeta)
- [ ] Comprobar zona horaria `-05:00` (Perú)
- [ ] Revisar permisos de archivos y carpetas en el servidor
- [ ] Configurar HTTPS en el servidor (certificado SSL)

---

## 5. Notas

- **Dominio principal:** jhsoftperu.com (puede albergar otros servicios)
- **Aplicación:** ganadoresya.jhsoftperu.com (subdominio dedicado al sistema de rifas)
- No subir credenciales reales a repositorios; usar variables de entorno o archivos fuera del control de versiones si es posible.
