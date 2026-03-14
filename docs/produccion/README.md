# Archivos para Publicación en Hostinger

Sistema de Rifas - GanadoresYa  
**Dominio:** jhsoftperu.com  
**Subdominio:** ganadoresya.jhsoftperu.com

---

## Rutas en Hostinger

| Destino | Directorio |
|---------|------------|
| **Subdominio** ganadoresya.jhsoftperu.com | `/home/u696088465/domains/jhsoftperu.com/public_html/ganadoresya` |
| **Dominio** jhsoftperu.com | `/home/u696088465/domains/jhsoftperu.com/public_html` |

---

## Archivos Incluidos

| Archivo | Uso | Destino |
|---------|-----|---------|
| `Conexion_SUBDOMINIO.php` | BD para subdominio | `config/conexion.php` |
| `Conexion_DOMINIO.php` | BD para dominio | `config/conexion.php` |
| `Enrutamiento_SUBDOMINIO.php` | URLs para subdominio | `config/Enrutamiento.php` |
| `Enrutamiento_DOMINIO.php` | URLs para dominio | `config/Enrutamiento.php` |
| `htaccess_SUBDOMINIO` | Rewrite para subdominio | Raíz del proyecto (renombrar a `.htaccess`) |
| `htaccess_DOMINIO` | Rewrite para dominio | Raíz del proyecto (renombrar a `.htaccess`) |
| `api_htaccess_SUBDOMINIO` | API para subdominio | Carpeta `api/` (renombrar a `.htaccess`) |
| `api_htaccess_DOMINIO` | API para dominio | Carpeta `api/` (renombrar a `.htaccess`) |
| `router_produccion.php` | Router alternativo (opcional; el router principal ya soporta producción) | `web/router.php` |
| `terminos_index_produccion.php` | Términos con Enrutamiento | `views/web/terminos/index.php` |

---

## Pasos para Publicar

### Opción A: Subdominio (ganadoresya.jhsoftperu.com)

1. Subir todo el proyecto a `/public_html/ganadoresya/`
2. Copiar `Conexion_SUBDOMINIO.php` → `config/conexion.php`
3. Copiar `Enrutamiento_SUBDOMINIO.php` → `config/Enrutamiento.php`
4. Renombrar `htaccess_SUBDOMINIO` → `.htaccess` y colocar en la raíz
5. Renombrar `api_htaccess_SUBDOMINIO` → `.htaccess` y colocar dentro de la carpeta `api/`
6. En `config/conexion.php`: reemplazar `TU_PASSWORD_BD` con la contraseña real de la base de datos

### Opción B: Dominio (jhsoftperu.com)

1. Subir todo el proyecto a `/public_html/`
2. Copiar `Conexion_DOMINIO.php` → `config/conexion.php`
3. Copiar `Enrutamiento_DOMINIO.php` → `config/Enrutamiento.php`
4. Renombrar `htaccess_DOMINIO` → `.htaccess` y colocar en la raíz
5. Renombrar `api_htaccess_DOMINIO` → `.htaccess` y colocar dentro de la carpeta `api/`
6. En `config/conexion.php`: reemplazar `TU_PASSWORD_BD` con la contraseña real

---

## Credenciales de Base de Datos (Hostinger)

- **Host:** localhost (o el que indique Hostinger en el panel)
- **Base de datos:** u696088465_bd_sorteos
- **Usuario:** u696088465_user_sorteos
- **Contraseña:** (obtener desde el panel de Hostinger)

---

## Error 500 en admin-login

Si aparece error 500, consulta `DIAGNOSTICO_ERROR_500.md`. Causas frecuentes:
- Archivo de conexión debe llamarse `config/conexion.php` (minúsculas, para Linux)
- Contraseña de BD no configurada (`TU_PASSWORD_BD`)
- Revisar logs de error en el panel de Hostinger

---

## Verificación Post-Despliegue

- [ ] https://ganadoresya.jhsoftperu.com/ (o jhsoftperu.com) carga la landing
- [ ] https://.../admin-login permite iniciar sesión
- [ ] https://.../terminos muestra los términos
- [ ] API responde correctamente (rifas, premios, etc.)
