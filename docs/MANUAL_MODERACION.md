# Manual de moderación — Recetas Fáciles

Guía para el administrador del sistema sobre cómo moderar el contenido de la plataforma.

---

## Cuenta de administrador

| Campo | Valor |
|---|---|
| **Email** | `admin@recetasfaciles.com` |
| **Contraseña** | `password` |
| **ID de usuario** | `1` |
| **Nombre** | Administrador |

> **Nota**: El sistema identifica al administrador por `user_id = 1`. Cualquier usuario con ese ID tiene permisos de moderación sobre todas las recetas.

---

## Permisos del administrador

El administrador puede realizar las siguientes acciones que los usuarios normales no pueden:

### Editar cualquier receta
1. Navega a la receta que quieres moderar.
2. Haz clic en el icono de **editar** (✏️) en la tarjeta o accede a `/receta/{id}/editar`.
3. Modifica los campos necesarios (título, descripción, pasos, imagen, categoría, etc.).
4. Guarda los cambios.

### Eliminar cualquier receta
1. Haz clic en el icono de **eliminar** (🗑️) en la tarjeta de la receta.
2. Confirma la eliminación en el diálogo.
3. La receta, sus comentarios, valoraciones y favoritos se eliminan en cascada.

> **Importante**: La eliminación es permanente. No existe papelera de reciclaje.

---

## Flujo de moderación recomendado

```
Contenido reportado o detectado
        │
        ▼
¿Incumple las normas de la comunidad?
        │
   Sí ──┤── No → No actuar
        │
        ▼
¿Es contenido ofensivo/spam?
        │
   Sí ──┤── No → Editar para corregir
        │
        ▼
   Eliminar receta
```

---

## Qué moderar

| Tipo | Acción recomendada |
|---|---|
| Receta con contenido ofensivo | Eliminar |
| Receta con título/descripción spam | Editar o eliminar |
| Imagen inapropiada | Eliminar la receta |
| Receta duplicada del mismo usuario | Eliminar la copia |
| Receta copiada de otro usuario | Contactar al usuario, eliminar si no responde |

---

## Qué NO moderar

- Recetas de baja calidad (pero bien intencionadas) → Dejar que la comunidad las valore.
- Comentarios con opiniones negativas pero respetuosas → Son válidos.
- Valoraciones bajas → Reflejan la opinión legítima del usuario.

---

## Gestión de usuarios

Actualmente el sistema no dispone de un panel de administración dedicado para gestionar usuarios. Para casos extremos (banear un usuario), se debe actuar directamente en la base de datos:

```bash
# Entrar al contenedor
docker exec -it recetas-app bash

# Usar tinker para eliminar un usuario
php artisan tinker
>>> User::find($id)->delete();
```

Esto eliminará el usuario y, por cascada, todas sus recetas, comentarios, valoraciones, favoritos y relaciones de seguimiento.

---

## Acceso a la base de datos

```bash
# Desde el host
docker exec -it recetas-db mysql -u recetas_user -precetas_pass recetas_db

# Consultas útiles
SELECT id, name, email FROM users;
SELECT id, titulo, usuario_id FROM recetas;
SELECT * FROM comentarios WHERE contenido LIKE '%spam%';
```
