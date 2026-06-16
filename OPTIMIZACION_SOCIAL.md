# Optimización Social (RF-16)

Documento de optimizaciones aplicadas al feed social y consultas relacionadas.

## RF-109 — Eliminación de N+1

### RecetaController::index()
- **Antes**: `DB::table('recetas')->join(...)` con joins manuales. Cada acceso a autor/categoría en las vistas generaba consultas adicionales implícitas.
- **Después**: `Receta::with(['autor', 'categoria'])` — Eloquent con eager loading. 2 queries (recetas + autores + categorías precargados) en lugar de 1 + N.

### RecetaController::show()
- **Antes**: `Receta::with(['autor', 'categoria'])->findOrFail($id)` + query separada para comentarios + query separada para media de valoraciones.
- **Después**: `Receta::with(['autor', 'categoria', 'comentarios.autor'])->withAvg('valoraciones', 'puntuacion')->withCount('comentarios')->findOrFail($id)` — todo en una sola ronda de queries.

### InteraccionController (comentar / valorar)
- **Antes**: `Receta::findOrFail($id)` seguido de `$receta->autor->notify(...)` = 2 queries.
- **Después**: `Receta::with('autor')->findOrFail($id)` = 1 query.

### ProfileController (seguidores / seguidos)
- **Antes**: `->with('seguidores')` / `->with('seguidos')` cargaba relaciones recursivas innecesarias.
- **Después**: Sin eager loading innecesario, solo los datos necesarios para la vista.

---

## RF-110 — Índices añadidos

Migración: `2026_05_21_000001_add_optimization_indexes.php`

| Tabla | Índice | Tipo | Uso |
|---|---|---|---|
| recetas | `idx_recetas_dificultad` | INDEX | Filtro por dificultad en feed |
| recetas | `idx_recetas_tiempo_coccion` | INDEX | Filtro por rango de tiempo |
| comentarios | `idx_comentarios_receta_id` | INDEX | Listado de comentarios por receta |
| valoraciones | `idx_valoraciones_receta_id` | INDEX | Cálculo de media por receta |
| valoraciones | `uq_valoraciones_usuario_receta` | UNIQUE | updateOrCreate en valorar() |
| favoritos | `idx_favoritos_receta_id` | INDEX | Consulta inversa de favoritos |
| seguidores | `idx_seguidores_seguido_id` | INDEX | Buscar seguidores de un usuario |

---

## RF-111 — Caché implementada

| Clave | TTL | Contenido | Invalidación |
|---|---|---|---|
| `categorias_all` | 60 min | Todas las categorías | Manual (raramente cambian) |
| `recetas_populares` | 60 min | Top 3 recetas por valoración media | Al crear/actualizar/borrar receta o al valorar |
| `seguidos_user_{id}` | 60 min | IDs de los usuarios que sigue el usuario | Al seguir/dejar de seguir a alguien |

Se invalidan con `Cache::forget()` en:
- `RecetaController::store()`, `update()`, `destroy()`
- `InteraccionController::valorar()`
- `ProfileController::toggleFollow()` (invalidación de `seguidos_user_{id}`)

---

## RF-112 — Mejora de tiempos

Las optimizaciones de RF-109, RF-110 y RF-111 reducen:
- Número de queries por petición en el feed (de ~N+2 a ~3)
- Tiempo de respuesta en consultas filtradas (índices)
- Carga en queries repetitivas del sidebar (caché)

## RF-113 — Compatibilidad

- La migración es aditiva (solo añade índices).
- Las vistas se actualizaron para usar accesores Eloquent (`$receta->autor->name` en vez de `$receta->autor_nombre`).
- Todos los filtros, ordenación y paginación mantienen la misma lógica.
