# Recetas Fáciles

Aplicación web donde puedes compartir tus recetas, descubrir otras nuevas, comentar, valorar y seguir a otros usuarios. Todo con una interfaz moderna construida con **Laravel 10**, **Blade**, **Tailwind CSS** y **MySQL**.

Básicamente es una red social de recetas donde los cocineros pueden compartir sus creaciones, interactuar con otros usuarios y guardar las recetas que más les gustan.

---

## Requisitos previos

Lo básico que necesitas en cualquier caso:
- Git instalado en tu máquina

Si quieres usar Docker (lo más fácil):
- [Docker](https://docs.docker.com/get-docker/) versión 20.10 o superior
- [Docker Compose](https://docs.docker.com/compose/install/) versión 1.29 o superior

Si prefieres instalar todo en tu máquina:
- PHP 8.1 o superior (versión 8.3 es mejor)
- Composer para instalar las dependencias de PHP
- Node.js 20.x
- npm que viene incluido con Node.js
- MySQL 8.0 o MariaDB 10.4 para la base de datos

---

## Instalación con Docker

Primer paso es clonar el repositorio:

```bash
git clone https://github.com/tu-usuario/recetas-faciles.git
cd recetas-faciles
```

Luego copia el archivo de configuración del entorno:

```bash
cp .env.example .env
```

El archivo `.env` ya viene configurado con los valores necesarios para Docker. Si quieres cambiar algo, estos son los datos importantes:
- Base de datos: recetas_db
- Usuario: recetas_user  
- Contraseña: recetas_pass
- Puerto de la aplicación: 8080
- Puerto de la base de datos: 3307

Ahora lanza los contenedores:

```bash
docker compose up -d --build
```

Esto va a crear dos servicios corriendo en tu máquina:

| Servicio | Contenedor | Puerto |
|---|---|---|
| Aplicación (PHP 8.3 + Apache) | recetas-app | 8080 |
| Base de datos (MySQL 8.0) | recetas-db | 3307 |

Automáticamente el contenedor va a hacer varias cosas:
- Instala todas las dependencias de PHP
- Instala todas las dependencias de Node.js
- Compila los assets con Vite
- Ejecuta las migraciones de la base de datos
- Carga los datos de prueba

Cuando todo esté listo, abre el navegador y ve a: http://localhost:8080

Si quieres probar la aplicación, tenemos estos usuarios disponibles:

| Usuario | Email | Contraseña |
|---|---|---|
| Administrador | admin@recetasfaciles.com | password |
| Antonio Cocinitas | antonio@email.com | password |
| María Chef | maria@email.com | password |
| VeganLife | vegan@email.com | password |

Algunos comandos que te van a ser útiles:

```bash
# Ver logs de la aplicación
docker compose logs -f app
 que está pasando en la aplicación
docker compose logs -f app

# Ver lo que está pasando en la base de datos
docker compose logs -f db

# Entrar en la consola de Laravel
docker compose exec app php artisan tinker

# Detener todo sin borrar nada
docker compose stop

# Eliminar todo incluida la base de datos
docker compose down -v

# Volver a construir todo desde cero
docker compose up -d --build
```

---

## no quieres usar Docker, puedes instalar todo directamente en tu máquina. Primero clona el repositorio:

```bash
git clone https://github.com/tu-usuario/recetas-faciles.git
cd recetas-faciles
```

Instala todas las dependencias de PHP que necesita Laravel:

```bash
composer install
```

Copia el archivo de configuración:

```bash
cp .env.example .env
```

Ahora abre el archivo `.env` y actualiza estos valores con tus datos:

```env
APP_NAME=RecetasFaciles
APP_ENV=local
APP_KEY=base64:xxx
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=recetas_db
DB_USERNAME=root
DB_PASSWORD=tu_contraseña
```

Genera la clave de la aplicación (esto es importante):

```bash
php artisan key:generate
```

Crea una nueva base de datos en MySQL. Abre tu cliente MySQL y ejecuta:

```bash
mysql -u root -p
CREATE DATABASE recetas_db;
EXIT;
```

Ahora ejecuta las migraciones para crear todas las tablas:

```bash
php artisan migrate
```

Carga los datos de prueba:

```bash
php artisan db:seed
```

Esto va a crear automáticamente 8 usuarios, 3 categorías y 25 recetas de prueba.

Instala las dependencias de Node.js:

```bash
npm install
```

Compila los estilos y JavaScripts. Para desarrollo con actualización automática:

```bash
npm run dev
```

O para producción:

```bash
npm run build
```

Finalmente, inicia el servidor:

```bash
php artisan serve
```

La aplicación va a estar disponible en: http://localhost:8000

---

## 📁 Estructura del Proyecto

```
recetas-faciles/
├── app/
│   ├── Http/Controllers/          # Controladores de la aplicación
│  ─ Models/                     # Modelos de Eloquent (User, Receta, etc.)
│   ├── Notifications/              # Notificaciones (nuevos comentarios, valoraciones, etc.)
│   └── Services/                   # Servicios auxiliares (ej: ImagenRecetaService)
├── routes/
│   ├── web.php                     # Rutas web (vistas)
│   └── api.php                    Controladores de la aplicación
│   ├── Models/                     Modelos de Eloquent
│   ├── Notifications/              Notificaciones del sistema
│   └── Services/                   Servicios auxiliares
├── routes/
│   ├── web.php                     Rutas web
│   └── api.php                     Rutas de la API
├── resources/
│   ├── views/                      Vistas de Blade
│   ├── css/                        Estilos con Tailwind
│   └── js/                         JavaScript del frontend
├── database/
│   ├── migrations/                 Migraciones de la BD
│   ├── seeders/                    Datos iniciales
│   └── factories/                  Datos para tests
├── config/                         Configuraciones
├── docker-compose.yml              Docker Compose
├── Dockerfile                      Imagen Docker
├── vite.config.js                  Configuración de Vite
└── tailwind.config.js              Configuración de Tailwind
Las tablas principales son:

- **users**: Los usuarios que se registran
- **categorias**: Tipos de recetas (Veganos, Carnívoros, Dulceros)
- **recetas**: Las recetas compartidas
- **comentarios**: Comentarios que hacen los usuarios
- **valoraciones**: Las puntuaciones de las recetas
- **favoritos**: Recetas guardadas
- **seguidores**: Quién sigue a quién
- **notifications**: Notificaciones cuando ocurren cosas

Las migraciones que se ejecutan son estas:

| Migración | Qué hace |
|---|---|
| create_users_table | Crea la tabla de usuarios |
| create_categorias_table | Categorías de recetas |
| create_recetas_table | Las recetas |
| create_comentarios_table | Comentarios |
| create_valoraciones_table | Puntuaciones |
| create_favoritos_table | Recetas guardadas |
| create_seguidores_table | Sistema de seguimiento |
| create_notifications_table | Notificaciones |

---

## Testing

Si quieres ejecutar los tests unitarios:

```bash
php artisan test
```

Para ejecutar un test específico:

```bash
php artisan test --filter=TestName
```

Y si quieres ver la cobertura de código:

```bash
php artisan test --coverage
```

---

## Comandos útiles

Con Artisan (el gestor de Laravel) puedes hacer muchas cosas:

```bash
# Crear un nuevo controlador
php artisan make:controller NombreController

# Crear un modelo con su migración
php artisan make:model Nombre -m

# Crear una migración
php artisan make:migration nombre_migracion

# Crear un seeder
php artisan make:seeder NombreSeeder

# Ver todas las rutas
php artisan route:list

# Limpiar el caché
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Consola interactiva
php artisan tinker
```

Con npm manejas el frontend:

```bash
# Desarrollo con actualización automática
npm run dev

# Compilar para producción
npm run build

# Linting de código
npm run lint
```

---

## Solución de problemas

Si la base de datos no existe o da error:

```bash
# Opción rápida: recrear todo con datos
php artisan migrate:fresh --seed

# Opción manual
mysql -u root -p
CREATE DATABASE recetas_db;
```

Si tienes problemas de permisos en los archivos:

Con Docker:
```bash
docker compose exec app chmod -R 777 storage bootstrap/cache
```

En tu máquina:
```bash
chmod -R 777 storage bootstrap/cache
```

Si MySQL desaparece de repente:

```bash
# En Docker, reinicia la base de datos
docker compose restart db

# En local, verifica la conexión
mysql -u usuario -p -h 127.0.0.1
```

Si no cargan los estilos ni JavaScript:

```bash
# Recompila los assets
npm run build

# Limpia el caché de Laravel
php artisan cache:clear
php artisan view:clear
```

Si ves errores "URL no encontrada":

Con Docker, el módulo mod_rewrite debe estar habilitado. Si instalaste Docker con este proyecto, ya está configurado.

En local, asegúrate de que tu servidor redirige a public/index.php

---

## 📝 Funcionalidades principales

### Para usuarios

- ✅ Registro y login con autenticación segura (Laravel Sanctum)
- ✅ Crear, editar y eliminar recetas
- ✅ Compartir recetas en el feed social
- ✅Funcionalidades principales

Cosas que pueden hacer los usuarios:

- Registrarse e iniciar sesión de forma segura
- Crear, editar y borrar sus propias recetas
- Compartir recetas en el feed social
- Comentar en las recetas de otros
- Valorar recetas con puntuaciones
- Guardar recetas que les gustan
- Seguir a otros cocineros
- Recibir notificaciones cuando algo interesante ocurre
- Ver el perfil de otros usuarios

Los administradores además pueden:

- Gestionar las categorías de recetas
- Moderar contenido
- Gestionar usuarios
- Ver estadísticas

---

## Seguridad

La aplicación tiene estas medidas de seguridad:

- Protección contra ataques CSRF
- Contraseñas hasheadas con bcrypt
- Límites en las rutas sensibles
- Validación en el servidor
- Autenticación segura con Laravel Sanctum
- Consultas preparadas contra inyecciones SQL

---

##Parte | Tecnología |
|---|---|
| Backend | Laravel 10.x |
| Frontend | Blade, Tailwind CSS, Alpine.js |
| Base de datos | MySQL 8.0 |
| Compilación | Vite 4.x, PostCSS |
| API | Laravel Sanctum |
| Contenedorización | Docker, Docker Compose |
| PHP | 8.3 |
| Node.js | 20.x |

---

## Documentación adicional

Tenemos más documentos que te pueden ser útiles:

- [Guía de usuario](docs/GUIA_USUARIO.md)
- [Manual de moderación](docs/MANUAL_MODERACION.md)
- [Normas de comunidad](docs/NORMAS_COMUNIDAD.md)

---

## Autores

Migración a Laravel 10 y desarrollo completo del proyecto.

## Licencia

MIT License

---

## Contacto

Si encuentras problemas o tienes
### 6. Otros comandos útiles

```bash
# Ver logs de la app
docker compose logs -f app

# Entrar al contenedor
docker exec -it recetas-app bash

# Ejecutar seeders
docker exec -it recetas-app php artisan db:seed

# Ejecutar tinker
docker exec -it recetas-app php artisan tinker

# Parar los contenedores
docker compose down

# Parar y borrar volúmenes (resetear BD)
docker compose down -v
```

---

## Arranque local sin Docker

### Requisitos

- PHP 8.1+
- Composer
- Node.js 20
- MySQL 8

### Instalación

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

---

## Estructura del proyecto

```
recetas-faciles/
├── app/                    # Lógica de la aplicación (Controllers, Models, Services)
├── database/
│   ├── migrations/         # Migraciones de la base de datos
│   ├── seeders/            # Seeders
│   └── recetas_db.sql      # Datos iniciales (se importa en Docker automáticamente)
├── resources/views/        # Vistas Blade
├── routes/                 # Rutas web y API
├── public/                 # Assets públicos
├── Dockerfile              # Imagen Docker de la app
├── docker-compose.yml      # Orquestación de servicios
├── docker-entrypoint.sh    # Script de arranque del contenedor
├── docs/                   # Documentación de la comunidad
│   ├── GUIA_USUARIO.md     # Guía de uso para usuarios finales
│   ├── NORMAS_COMUNIDAD.md # Normas de la comunidad
│   ├── MANUAL_MODERACION.md # Manual para administradores
│   └── img/                # Capturas de pantalla
└── OPTIMIZACION_SOCIAL.md  # Documentación de optimizaciones RF-16
```

---

## Usuarios de prueba

Todos los usuarios de prueba tienen la contraseña: `password`

| Email | Nombre | Rol |
|---|---|---|
| admin@recetasfaciles.com | Administrador | **Administrador** |
| antonio@email.com | Antonio Cocinitas | Usuario |
| maria@email.com | María Chef | Usuario |
| vegan@email.com | VeganLife | Usuario |
| carlos@email.com | Carlos Parrilla | Usuario |
| laura@email.com | Laura Saludable | Usuario |

> El administrador (user_id=1) puede editar y eliminar cualquier receta.

---

## Documentación

| Documento | Descripción |
|---|---|
| [Guía de usuario](docs/GUIA_USUARIO.md) | Cómo usar la plataforma paso a paso |
| [Normas de la comunidad](docs/NORMAS_COMUNIDAD.md) | Reglas de convivencia y contenido |
| [Manual de moderación](docs/MANUAL_MODERACION.md) | Guía para administradores |
| [Optimización social](OPTIMIZACION_SOCIAL.md) | Detalle técnico de las optimizaciones |

---

## Licencia

MIT
