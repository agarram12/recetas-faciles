#!/bin/bash
set -e

# Crear directorios de storage si no existen (el volume mount puede pisarlos)
mkdir -p storage/framework/{sessions,views,cache}
mkdir -p storage/logs
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Generar APP_KEY si no existe
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Esperar a que MySQL esté listo y ejecutar migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force

# Importar datos iniciales si la BD está vacía
echo "Verificando datos iniciales..."
php artisan db:seed --force 2>/dev/null || true

# Cache de configuración
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permisos
chown -R www-data:www-data storage bootstrap/cache

echo "Aplicación lista."

# Arrancar Apache
exec apache2-foreground
