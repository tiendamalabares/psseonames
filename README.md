# psseonames

Módulo para **PrestaShop 1.7 / 8** que permite definir un título visible alternativo por categoría llamado **Nombre Seo**, sin tocar core ni editar manualmente el tema.

## ¿Qué hace el módulo?

En la ficha de categoría (Backoffice) añade un campo translatable **Nombre Seo** y lo guarda por:

- `id_category`
- `id_lang`
- `id_shop`

En Frontend, si existe valor para la categoría/idioma/tienda actual, ese valor reemplaza el título visible (H1 / etiqueta de listado usada por el tema). Si no existe, se mantiene el nombre nativo de la categoría.

> Alcance: no modifica `meta title` ni otros metadatos.

## Instalación (2 minutos)

1. Copiar la carpeta del módulo en `modules/psseonames`.
2. Instalar desde Backoffice.
3. Confirmar creación de tabla `PREFIX_psseonames_category`.

> Si el módulo no aparece en el listado de módulos, verifica que el archivo principal sea `psseonames.php` y que la clase declarada sea `Psseonames` (mismo nombre del módulo en PascalCase).

## Uso funcional

1. Ir a **Catálogo > Categorías**.
2. Abrir/crear categoría.
3. Completar campo **Nombre Seo** en los idiomas necesarios.
4. Guardar.
5. Navegar a la categoría en Frontend y verificar que el título visible usa el valor personalizado.

## Hooks utilizados

### Backoffice

- `actionCategoryFormBuilderModifier`: añade el campo en formulario Symfony.
- `actionAfterCreateCategoryFormHandler`: persiste datos al crear.
- `actionAfterUpdateCategoryFormHandler`: persiste datos al actualizar.
- `actionObjectCategoryDeleteAfter`: limpia datos al eliminar categoría.

### Frontoffice

- `actionCategoryControllerSetVariables`: sobreescribe el nombre visible cuando hay `Nombre Seo`.
  - Actualiza `category.name` y `listing.label` porque muchos temas (incluido Classic) renderizan el H1 desde `listing.label`.
  - Además publica `{$psseonames_seo_name}` en Smarty como valor explícito para uso opcional en plantilla.

## Tabla creada por el módulo

Tabla: `PREFIX_psseonames_category`

Columnas:

- `id_category` (INT UNSIGNED)
- `id_lang` (INT UNSIGNED)
- `id_shop` (INT UNSIGNED)
- `seo_name` (VARCHAR(255))

Clave primaria compuesta:

- (`id_category`, `id_lang`, `id_shop`)

## Efecto en Frontend

Cuando hay valor personalizado, el módulo sobreescribe:

- `category.name`
- `listing.label`
- `psseonames_seo_name`

Con esto el H1 de la categoría usa el Nombre Seo de forma consistente entre temas.

## Estructura rápida del proyecto

- `psseonames.php`: hooks, validación, orquestación funcional.
- `src/Repository/SeoCategoryNameRepository.php`: acceso a datos.
- `sql/install.php` / `sql/uninstall.php`: ciclo de vida de tabla.
- `ARCHITECTURE.md`: decisiones técnicas y flujo interno.
- `CONTRIBUTING.md`: política obligatoria del repositorio.
