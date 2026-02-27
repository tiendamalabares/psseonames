# ARCHITECTURE

## 1. Objetivo técnico

Implementar un módulo desacoplado para gestionar un nombre visible alternativo de categoría, manteniendo compatibilidad con PrestaShop 1.7/8, multiidioma y multitienda, sin tocar core.

## 2. Estructura de carpetas

- `psseonames.php`
  - Punto de entrada del módulo.
  - Registro de hooks.
  - Coordinación Backoffice/Frontend.
- `src/Repository/SeoCategoryNameRepository.php`
  - Capa de acceso a datos.
  - Lectura/escritura/limpieza sobre tabla del módulo.
- `sql/install.php`
  - Creación inicial de estructura.
- `sql/uninstall.php`
  - Eliminación de estructura.

## 3. Flujo de datos (Backoffice → BBDD → Frontend)

1. **Backoffice / formulario categoría**
   - Hook `actionCategoryFormBuilderModifier` inserta campo translatable `Nombre Seo`.

2. **Persistencia**
   - Hooks `actionAfterCreateCategoryFormHandler` y `actionAfterUpdateCategoryFormHandler` capturan datos.
   - Se sanitiza (texto plano, max 255).
   - Se persiste por `(id_category, id_lang, id_shop)` en tabla propia.

3. **Lectura en Frontend**
   - Hook `actionCategoryControllerSetVariables` busca valor para categoría/idioma/tienda actual.
   - Si existe, sobreescribe variables del template usadas por el título visible.
   - Si no existe, aplica fallback natural al nombre original de categoría.

## 4. Hooks usados y por qué

- `actionCategoryFormBuilderModifier`
  - Integración limpia con formulario Symfony de categoría.
- `actionAfterCreateCategoryFormHandler`
  - Persistencia tras creación.
- `actionAfterUpdateCategoryFormHandler`
  - Persistencia tras edición.
- `actionObjectCategoryDeleteAfter`
  - Limpieza de datos huérfanos.
- `actionCategoryControllerSetVariables`
  - Reescritura de variables visibles sin modificar tema/core.

## 5. Estrategia de datos y rendimiento

- Tabla dedicada `PREFIX_psseonames_category`.
- PK compuesta para unicidad por categoría+idioma+tienda.
- Capa `Repository` para evitar SQL disperso en hooks.
- Caché en memoria por request para evitar lecturas repetidas de una misma categoría.
- Regla obligatoria: evitar queries dentro de bucles.

## 6. Criterios de diseño

- No tocar core de PrestaShop.
- Compatibilidad PS 1.7 / 8.
- Soporte multiidioma y multitienda obligatorio.
- Evitar duplicación de lógica.
- Código legible y desacoplado.

## 7. Cambios de BBDD

Cuando cambie la estructura de datos:

- Mantener scripts de instalación base.
- Añadir upgrade del módulo para instancias ya instaladas.
- Documentar el cambio y su motivación en este archivo.

## 8. Regla de mejora continua (obligatoria)

Cada vez que se detecte un patrón, práctica o decisión que pueda evitar errores futuros o acelerar el desarrollo, se debe añadir aquí como conocimiento operativo del proyecto.
