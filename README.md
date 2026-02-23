# Sistema de Inventario de Hardware

Sistema desarrollado bajo la metodología Extreme Programming (XP) aplicando Programación Orientada a Objetos (POO) en PHP puro.

## 📝 Detalles del Proyecto

* **Descripción breve:** Plataforma web ligera para el control, registro y gestión de componentes físicos de computadoras mediante persistencia en sesión.
* **Problema que resuelve:** Elimina la falta de trazabilidad en la entrada y salida de piezas de hardware, y automatiza las alertas sobre el stock crítico en el almacén.
* **Usuarios objetivo:** Almaceneros (gestión diaria), Técnicos de ensamblaje (lectura de stock) y Administradores (reportes y bajas).

## ⚙️ Requisitos

* PHP 8.2 o superior
* Composer (Exclusivamente para Autoloading PSR-4)
* Navegador web con soporte para sesiones
* *Nota: En este Sprint (Iteración 1) no se requiere motor de Base de Datos. La persistencia se maneja en memoria/sesión.*

## 🚀 Instalación y Ejecución

1. Clonar el repositorio.
2. Abrir una terminal en la raíz del proyecto y ejecutar: `composer dump-autoload` (para mapear las clases).
3. Iniciar el servidor local de PHP en la raíz del proyecto:
   `php -S localhost:8000`
4. Acceder en el navegador a: `http://localhost:8000/public/hardware/index.php`

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

## 👨‍💻 Autor

* Hugo Marcelo Daza Limari (pongan sus nombres)