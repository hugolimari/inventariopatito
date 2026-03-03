# Sistema de Inventario de Hardware

Sistema desarrollado bajo la metodología Extreme Programming (XP) aplicando Programación Orientada a Objetos (POO) en PHP puro.

## 📝 Detalles del Proyecto

* **Descripción breve:** Plataforma web ligera creada siguiendo patrón MVC para el control, registro y gestión de componentes físicos de computadoras. La persistencia de datos es en memoria/sesión.
* **Problema que resuelve:** Elimina la falta de trazabilidad en la entrada y salida de piezas de hardware, y automatiza las alertas sobre el stock crítico en el almacén.
* **Usuarios objetivo:** Almaceneros (gestión diaria), Técnicos de ensamblaje (lectura de stock) y Administradores (reportes, bajas y ahora también edición de componentes).

## ⚙️ Requisitos

* PHP 8.2 o superior
* Composer (Exclusivamente para Autoloading PSR-4)
* Navegador web con soporte para sesiones
* *Nota: En este Sprint (Iteración 1) no se requiere motor de Base de Datos. La persistencia se maneja en memoria/sesión.*

## 🚀 Instalación y Ejecución

### Paso 1: Preparar la base de datos

1. Asegúrate de que tienes MariaDB/MySQL ejecutándose.
2. Clonar el repositorio.
3. Ejecutar los scripts SQL en el siguiente orden:
   ```bash
   # Crear tablas
   mysql -u root < inventario_hardware.sql
   
   # Insertar usuarios de prueba
   mysql -u root < insert_test_users.sql
   ```
   > Si `root` no tiene contraseña, usa `-p` para que te la pida; si la tiene, usa `-u root -p`.

4. Verificar que todo funciona:
   ```bash
   php scripts/check_db.php
   ```
   Este script te mostrará si la conexión es correcta y qué usuarios hay.

### Paso 2: Instalar dependencias y arrancar

1. Regenerar autoload:
   ```bash
   composer dump-autoload
   ```
2. Iniciar servidor PHP:
   ```bash
   php -S localhost:8000
   ```
3. Acceder en el navegador:
   * **Login**: `http://localhost:8000/public/auth/login.php`
   * **Inventario** (tras login): `http://localhost:8000/public/hardware/index.php`

### Credenciales de prueba

Usa esta para entrar:

| Usuario | Contraseña |
|---------|-----------|
| `admin` | `Admin@2026` |

> **Nota**: Aunque `insert_test_users.sql` contiene otros usuarios, el que actualmente está en tu BD es `admin` con contraseña `Admin@2026`.

## 📁 Estructura del Proyecto

proyecto-hardware/
├── src/
│   ├── Models/         # Clases POO (Hardware, Procesador, TarjetaGrafica)
│   ├── Repositories/   # Lógica de persistencia en memoria/sesión (HardwareRepository)
│   └── Enums/          # Enumeraciones (CategoriaHardware)
├── public/
│   └── hardware/       # Vistas e interfaz de usuario (index.php, crear.php, eliminar.php)
├── vendor/             # Dependencias de Composer (Autoload)
└── composer.json       # Configuración PSR-4

## 🎯 Módulos y Estado (Sprint 1)

**Iteración 1: MVP Core (Completado ✅)**
* `US-03`: Ingreso de nuevo hardware (CRUD - Create)
* `US-14`: Catálogo general con polimorfismo (CRUD - Read)
* `US-05`: Dar de baja un componente (CRUD - Delete)
* `US-10`: Alerta visual de stock crítico

**Iteración 2: (En planificación ⏳)**
* Autenticación, búsquedas filtradas y Base de Datos (MySQL).

> El proyecto ahora incluye un sistema de login con hashing de contraseñas, verificación,
> control de sesión y logout seguro. El esquema de la base de datos está en `inventario_hardware.sql`.
> Nota: Se han eliminado todos los artefactos relacionados con estudiantes, ya que no forman
> parte del sistema de inventario de hardware.
## 👨‍💻 Autor

* Hugo Marcelo Daza Limari (pongan sus nombres)