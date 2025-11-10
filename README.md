# 🎰 SISTEMA DE RIFAS MULTISEDE - PROFESIONAL

> Sistema completo de rifas/sorteos escalable para múltiples países con landing page, portal administrativo y sistema de validación de pagos.

[![MySQL](https://img.shields.io/badge/MySQL-8.0+-blue.svg)](https://www.mysql.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1+-purple.svg)](https://www.php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

---

## 📋 TABLA DE CONTENIDOS

- [Características](#-características)
- [Arquitectura](#-arquitectura)
- [Requisitos](#-requisitos)
- [Instalación](#-instalación)
- [Configuración](#-configuración)
- [Documentación](#-documentación)
- [Módulos](#-módulos)
- [API](#-api)
- [Roadmap](#-roadmap)

---

## ✨ CARACTERÍSTICAS

### 🌍 Multi-sede / Multi-país
- ✅ Soporte para múltiples países con configuraciones independientes
- ✅ Monedas personalizables (Soles, Pesos, Dólares, etc.)
- ✅ Zonas horarias configurables
- ✅ Métodos de pago específicos por país (Yape, Plin, Nequi, etc.)

### 🎁 Gestión de Premios
- ✅ Categorías de premios configurables
- ✅ Galería de imágenes múltiples
- ✅ Videos promocionales
- ✅ Especificaciones técnicas (JSON)
- ✅ Premios destacados en landing

### 🎲 Sistema de Rifas
- ✅ Configuración de múltiples intentos antes del ganador
- ✅ Ejemplo: 5 intentos, gana el intento #5 (transparencia)
- ✅ Límite de tickets totales y por persona
- ✅ Contador regresivo en tiempo real
- ✅ Visualización de participantes

### 🎫 Compra de Tickets
- ✅ Generación de código único por ticket
- ✅ Validación de autenticidad con código
- ✅ Múltiples métodos de pago
- ✅ Subida de comprobantes de pago
- ✅ Sistema de notificaciones email/SMS

### 💰 Validación de Pagos
- ✅ Módulo administrativo de validación
- ✅ Aprobación/rechazo manual de comprobantes
- ✅ Validaciones automáticas (monto, duplicados, etc.)
- ✅ Registro automático como participante tras aprobación

### 🎰 Motor de Sorteo
- ✅ Sorteo en vivo con múltiples intentos
- ✅ Hash de verificación para transparencia
- ✅ Registro inmutable de cada intento
- ✅ Auditoría completa del proceso

### 🏆 Gestión de Ganadores
- ✅ Notificación automática al ganador
- ✅ Coordinación de entrega de premio
- ✅ Documentación con fotos y actas
- ✅ Publicación de ganadores en landing

### 🔐 Seguridad
- ✅ Autenticación con JWT
- ✅ Roles y permisos granulares
- ✅ Control de sesiones
- ✅ Auditoría completa de operaciones
- ✅ Intentos de login limitados

---

## 🏗️ ARQUITECTURA

```
┌─────────────────────────────────────────────────────────────┐
│                    SISTEMA DE RIFAS                          │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌─────────────────┐         ┌──────────────────┐          │
│  │  LANDING PAGE   │         │  PORTAL ADMIN    │          │
│  │  (Usuarios)     │         │  (Gestión)       │          │
│  ├─────────────────┤         ├──────────────────┤          │
│  │ • Ver rifas     │         │ • Gestión premios│          │
│  │ • Comprar       │         │ • Gestión rifas  │          │
│  │ • Validar       │         │ • Validar pagos  │          │
│  │   tickets       │         │ • Sorteos        │          │
│  │ • Ver ganadores │         │ • Reportes       │          │
│  └────────┬────────┘         └────────┬─────────┘          │
│           │                           │                     │
│           └───────────┬───────────────┘                     │
│                       │                                     │
│              ┌────────▼────────┐                           │
│              │   API REST      │                           │
│              │   (PHP 8.1+)    │                           │
│              └────────┬────────┘                           │
│                       │                                     │
│              ┌────────▼────────┐                           │
│              │   MySQL 8.0+    │                           │
│              │   (Base Datos)  │                           │
│              └─────────────────┘                           │
└─────────────────────────────────────────────────────────────┘
```

---

## 📋 REQUISITOS

### Software:
- **MySQL:** 8.0 o superior
- **PHP:** 8.1 o superior
- **Apache/Nginx:** Servidor web
- **Composer:** Gestor de dependencias PHP
- **Node.js:** 18+ (para frontend)

### Extensiones PHP requeridas:
```bash
php-mysql
php-pdo
php-mbstring
php-json
php-fileinfo
php-gd (para procesamiento de imágenes)
php-curl
```

---

## 🚀 INSTALACIÓN

### 1. Clonar el repositorio
```bash
git clone https://github.com/tu-usuario/sistema-rifas.git
cd sistema-rifas
```

### 2. Instalar dependencias PHP
```bash
composer install
```

### 3. Crear base de datos
```bash
mysql -u root -p
CREATE DATABASE sistema_rifas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit;
```

### 4. Ejecutar scripts SQL
```bash
# Estructura de base de datos
mysql -u root -p sistema_rifas < docs/sql/bd_rifas_mysql.sql

# Procedimientos almacenados
mysql -u root -p sistema_rifas < docs/sql/sp_rifas_mysql.sql
```

### 5. Configurar archivo de conexión
```php
// config/Conexion.php
<?php
class Conexion {
    private $host = "localhost";
    private $db = "sistema_rifas";
    private $user = "root";
    private $pass = "tu_password";
    private $charset = "utf8mb4";
    
    // ... resto del código
}
```

### 6. Configurar permisos de carpetas
```bash
chmod -R 755 assets/uploads/
chmod -R 755 assets/comprobantes/
chmod -R 755 assets/premios/
```

### 7. Usuario por defecto
```
Usuario: admin
Contraseña: admin123
Sede: Sede Principal Lima

⚠️ IMPORTANTE: Cambiar contraseña en primer acceso
```

---

## ⚙️ CONFIGURACIÓN

### Configuración por sede (tabla `configuracion_sede`):

```sql
-- Ejemplo de configuraciones
INSERT INTO configuracion_sede (sede_id, clave, valor, tipo_dato) VALUES
(1, 'landing_color_primario', '#FF5722', 'STRING'),
(1, 'landing_color_secundario', '#2196F3', 'STRING'),
(1, 'max_tickets_por_persona', '5', 'INTEGER'),
(1, 'dias_validez_ticket', '90', 'INTEGER'),
(1, 'email_notificaciones', 'soporte@rifas.com', 'STRING'),
(1, 'whatsapp_notificaciones', '+51999888777', 'STRING');
```

### Métodos de pago por país:

#### 🇵🇪 Perú:
```sql
INSERT INTO metodos_pago (sede_id, nombre, numero_celular, requiere_comprobante) VALUES
(1, 'Yape', '999888777', 1),
(1, 'Plin', '999888777', 1);

INSERT INTO metodos_pago (sede_id, nombre, banco, numero_cuenta, numero_cci) VALUES
(1, 'Transferencia BCP', 'BCP', '19400123456789', '00219400123456789012');
```

#### 🇨🇴 Colombia:
```sql
INSERT INTO metodos_pago (sede_id, nombre, numero_celular) VALUES
(2, 'Nequi', '3001234567', 1),
(2, 'Daviplata', '3001234567', 1);
```

---

## 📚 DOCUMENTACIÓN

### Documentos disponibles:

1. **[ANÁLISIS COMPLETO](docs/analizer/SISTEMA_RIFAS_ANALISIS.md)**
   - Visión general del sistema
   - Módulos detallados
   - Flujos de trabajo
   - Casos de uso

2. **[DIAGRAMA DE RELACIONES](docs/analizer/DIAGRAMA_RELACIONES.md)**
   - Estructura de base de datos
   - Relaciones entre tablas
   - Integridad referencial
   - Índices y constraints

3. **[SEGURIDAD Y SESIONES](docs/api/SEGURIDAD_SESIONES.md)**
   - Sistema de autenticación
   - Manejo de tokens
   - Roles y permisos

---

## 🎯 MÓDULOS

### 1. Landing Page (Usuarios Finales)
```
📱 Funcionalidades:
• Ver rifas activas con contador
• Ver premios destacados
• Comprar tickets
• Subir comprobante de pago
• Validar código de ticket
• Ver ganadores publicados
```

### 2. Portal Administrativo
```
🖥️ Módulos:
• Dashboard con estadísticas
• Gestión de premios y categorías
• Gestión de rifas
• Validación de pagos ⭐
• Sorteo en vivo
• Gestión de ganadores
• Reportes y gráficos
• Configuración del sistema
```

### 3. API REST
```
🔌 Endpoints principales:
• /api/auth/login
• /api/rifas/activas
• /api/tickets/comprar
• /api/tickets/validar
• /api/comprobantes/subir
• /api/admin/validar-pago
• /api/admin/sorteo
```

---

## 🔌 API

### Autenticación
```bash
POST /api/auth/login
Content-Type: application/json

{
  "username": "admin",
  "password": "admin123",
  "sede_id": 1
}

Response:
{
  "resultado": 1,
  "mensaje": "Login exitoso",
  "token_sesion": "uuid-token",
  "usuario_id": 1,
  "sede_id": 1,
  "rol_nombre": "ADMIN"
}
```

### Listar rifas activas
```bash
GET /api/rifas/activas?sede_id=1
Authorization: Bearer {token}

Response:
{
  "resultado": 1,
  "rifas": [
    {
      "id": 1,
      "codigo": "RIFA-2025-001",
      "nombre": "Gana un iPhone 15 Pro",
      "precio_ticket": 10.00,
      "tickets_vendidos": 45,
      "dias_restantes": 7,
      "premio_nombre": "iPhone 15 Pro Max 256GB",
      "premio_imagen": "/assets/premios/iphone15.jpg"
    }
  ]
}
```

### Comprar ticket
```bash
POST /api/tickets/comprar
Content-Type: application/json

{
  "sede_id": 1,
  "rifa_id": 1,
  "nombres": "Juan",
  "apellidos": "Pérez García",
  "tipo_documento": "DNI",
  "numero_documento": "12345678",
  "email": "juan@email.com",
  "telefono": "999888777",
  "precio_pagado": 10.00
}

Response:
{
  "resultado": 1,
  "mensaje": "Ticket registrado exitosamente",
  "codigo_ticket": "PERU-20250124-123456"
}
```

### Validar ticket
```bash
GET /api/tickets/validar?codigo=PERU-20250124-123456

Response:
{
  "valido": 1,
  "mensaje": "Ticket encontrado",
  "codigo_ticket": "PERU-20250124-123456",
  "nombres": "Juan Pérez García",
  "estado": "APROBADO",
  "rifa_nombre": "Gana un iPhone 15 Pro",
  "fecha_sorteo": "2025-02-01 19:00:00"
}
```

---

## 🗺️ ROADMAP

### ✅ Fase 1 - MVP (ACTUAL)
- [x] Diseño de base de datos MySQL
- [x] Sistema de autenticación
- [x] Gestión de premios y rifas
- [x] Compra de tickets
- [x] Validación de pagos
- [x] Sistema de sorteo

### 🔄 Fase 2 - Mejoras (En desarrollo)
- [ ] API REST completa
- [ ] Landing page responsive
- [ ] Portal administrativo completo
- [ ] Sistema de notificaciones email
- [ ] Generación de reportes PDF

### 🚀 Fase 3 - Avanzado
- [ ] Pago con tarjeta (Stripe/PayPal)
- [ ] App móvil (React Native)
- [ ] Transmisión en vivo del sorteo
- [ ] Sistema de referidos
- [ ] Chat en vivo
- [ ] Bot de WhatsApp

### 💎 Fase 4 - Premium
- [ ] Rifas colaborativas
- [ ] Marketplace de premios
- [ ] Sistema de puntos
- [ ] Integración con redes sociales
- [ ] Dashboard analytics avanzado

---

## 📊 ESTRUCTURA DEL PROYECTO

```
sistema_rifas/
├── api/                    # API REST
│   ├── index.php
│   └── routes/
│       ├── routes_auth.php
│       ├── routes_rifas.php
│       ├── routes_tickets.php
│       └── routes_admin.php
├── assets/                 # Recursos estáticos
│   ├── css/
│   ├── js/
│   ├── images/
│   ├── uploads/
│   │   ├── comprobantes/
│   │   └── premios/
├── config/                 # Configuración
│   ├── Conexion.php
│   └── Enrutamiento.php
├── controller/             # Controladores
│   ├── AuthController.php
│   ├── RifaController.php
│   ├── TicketController.php
│   └── AdminController.php
├── models/                 # Modelos
│   ├── Auth.php
│   ├── Rifa.php
│   ├── Ticket.php
│   └── Premio.php
├── views/                  # Vistas
│   ├── landing/
│   ├── admin/
│   └── components/
├── helpers/                # Utilidades
│   ├── AuthMiddleware.php
│   └── Validator.php
├── docs/                   # Documentación
│   ├── sql/
│   │   ├── bd_rifas_mysql.sql
│   │   └── sp_rifas_mysql.sql
│   ├── analizer/
│   │   ├── SISTEMA_RIFAS_ANALISIS.md
│   │   └── DIAGRAMA_RELACIONES.md
│   └── api/
├── index.php
├── README.md
└── .htaccess
```

---

## 🤝 CONTRIBUIR

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add: Amazing Feature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

---

## 📝 LICENCIA

Este proyecto está bajo la Licencia MIT. Ver archivo `LICENSE` para más detalles.

---

## 👥 EQUIPO

- **Desarrollador Principal:** [Tu Nombre]
- **Diseño UI/UX:** [Nombre]
- **QA/Testing:** [Nombre]

---

## 📞 SOPORTE

- **Email:** soporte@sistema-rifas.com
- **Documentación:** [Docs completa](docs/)
- **Issues:** [GitHub Issues](https://github.com/tu-usuario/sistema-rifas/issues)

---

## 🎉 AGRADECIMIENTOS

- Inspirado en sistemas de rifas de todo el mundo
- Construido con ❤️ para la comunidad
- Gracias a todos los contribuidores

---

<p align="center">
  <strong>Sistema de Rifas Multisede v1.0</strong><br>
  Escalable • Profesional • Multi-país
</p>

<p align="center">
  Hecho con ❤️ en Perú 🇵🇪
</p>



-1 sorteo o rifa , puede tener 1 o muchos premios
-1 sorteo o rifa, puede tener muchos números tipo cartillas que el usuario puede elegir y comprar
-1 sorteo o rifa , se podrá exportar cartillas , ejemplo 10 sorteos , 20 sorteos etc, con el fin de publicitar y hacer que el usuario compre un numero de su preferencia
-1. el administrador cuando un cliente se compro una rifa, en el modulo de admin seleccionara el numero en la cartilla y realizara el registro del participante, con eso ya se inscribe al participante y ese numero
ya no esta disponible
- ya que exista sorteos disponibles , se podrá visualizar en la landing donde los usuarios en general podrán registrarse y comprar los tickets de su preferencia o tickets que se generen atomaticamnete
- creo el proceso para crear las cartillas o números seria al momento de crear el sorteo , elegir tipo ¿Cuántos números quieres vender o cuantos números deseas crear la rifa? en base  a eso se generaría la cuadricula general de números
