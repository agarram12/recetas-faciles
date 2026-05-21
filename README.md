# 🍳 Recetas Fáciles

Aplicación web de recetas con feed social, comentarios, valoraciones, favoritos y sistema de seguidores. Construida con Laravel 10, Blade y MySQL.

---

## Arranque con Docker

### Requisitos previos

- [Docker](https://docs.docker.com/get-docker/) y [Docker Compose](https://docs.docker.com/compose/install/) instalados.

### 1. Clonar el repositorio

```bash
git clone https://github.com/tu-usuario/recetas-faciles.git
cd recetas-faciles
```

### 2. Copiar el archivo de entorno

```bash
cp .env.example .env
```

### 3. Levantar los contenedores

```bash
docker compose up -d --build
```

Esto levanta dos servicios:

| Servicio | Contenedor | Puerto |
|---|---|---|
| **app** (PHP 8.3 + Apache) | `recetas-app` | `localhost:8080` |
| **db** (MySQL 8.0) | `recetas-db` | `localhost:3307` |

El contenedor de la app:
- Instala dependencias (Composer + NPM)
- Compila assets con Vite
- Ejecuta migraciones automáticamente al arrancar
- Carga datos iniciales con el seeder

### 4. Acceder a la aplicación

Abrir en el navegador: **http://localhost:8080**

### 5. Ejecutar migraciones manualmente (opcional)

```bash
docker exec -it recetas-app php artisan migrate --force
```

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

## 💻 Arranque local (sin Docker)

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
