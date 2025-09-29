# 📸 Fototeca

**Fototeca** es una aplicación sencilla en PHP para subir, visualizar y almacenar fotos localmente. Ideal para proyectos caseros, documentación visual o gestión de imágenes sin depender de servicios externos.

---

## 🚀 Características

- ✅ Subida de imágenes desde el navegador
- ✅ Almacenamiento local en la carpeta `media/`
- ✅ Visualización de fotos subidas
- ✅ Backend en PHP puro, sin frameworks

---

## 🧰 Tecnologías usadas

- 🐘 PHP
- 🖼️ HTML / CSS / JS
- 📁 File System (para guardar imágenes)
- 🧠 Lógica modular en `controller/`

---

## 📦 Instalación

```bash
git clone https://github.com/Francisco-Sole/fototeca.git
```

Coloca el proyecto en tu servidor local (ej. htdocs o www)

Asegúrate de que la carpeta media/ tenga permisos de escritura:

```bash
chmod -R 775 media/
```
Accede desde tu navegador:
http://localhost/fototeca

# 📘 Instalación y base de datos
Este proyecto requiere una base de datos ```MySQL/MariaDB``` para funcionar. 

Antes de ejecutar la aplicación, es necesario crear las tablas correspondientes.

En la carpeta ```/SQL``` encontrarás los scripts necesarios para generar la estructura de la base de datos (fotos y album).

Importa esos archivos en tu gestor de base de datos (```phpMyAdmin, consola MySQL, etc.```) para preparar el entorno.

Asegúrate de configurar correctamente las credenciales de conexión en tu archivo config.php.

Ejemplo rápido en consola:

```bash
mysql -u usuario -p fototeca < SQL/schema.sql
```


# Estructura del proyecto
```bash
fototeca/
├── config/              # Configuración general
├── controller/          # Lógica de subida y gestión
├── media/               # Carpeta donde se guardan las fotos
├── SQL/                 # Scripts o estructura de base de datos
├── index.php            # Punto de entrada principal
└── README.md
```

# 📤 Cómo usar
Abre la interfaz en el navegador

Sube una imagen desde el formulario

La imagen se guarda en media/

Se muestra en la galería

# 🛡️ Consideraciones
Las imágenes se guardan en local, no hay respaldo en la nube

No hay compresión ni validación avanzada

Ideal para uso personal, pruebas o documentación visual

# 🌱 Mejoras futuras (ideas)
🔍 Vista previa de imágenes antes de subir

🗂️ Organización por carpetas o etiquetas

🧼 Validación de formatos y tamaño

🧑‍💻 Panel de administración para gestionar fotos

☁️ Subir imagenes a un cloud o servidor casero

# 🚧 Estado del proyecto
Este proyecto no está terminado. Actualmente se encuentra en fase de desarrollo y pruebas, por lo que:

La estructura de tablas puede cambiar.

Algunas funcionalidades aún no están implementadas.

El código está pensado como prototipo/demostración.

# 👤 Autor
Creado por Francisco Solé

📍 El Vendrell, España 

💡 Apasionado por la infraestructura, domotica casera, la documentación clara y la mejora continua

# 📘 Licencia
Este proyecto está bajo la licencia MIT. Puedes usarlo, modificarlo y compartirlo libremente.

