# psseonames

Módulo para **PrestaShop 1.7 / 8** que permite definir un título visible alternativo por categoría llamado **Nombre Seo**, sin tocar core ni editar manualmente el tema.

## ¿Qué hace el módulo?

En la ficha de categoría (Backoffice) añade un campo translatable **Nombre Seo** y lo guarda por:

- `id_category`
- `id_lang`
- `id_shop`

En Frontend, si existe valor para la categoría/idioma/tienda actual, ese valor reemplaza el título visible de categoría (H1) y también se aplica en `listing.label`. Si no existe, se mantiene el comportamiento nativo del tema.

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

- `actionCategoryControllerSetVariables`: reemplaza el nombre visible de categoría en la página de categoría (`category.name`).
  - Ajusta `listing.label` cuando aplica y asigna `{$psseonames_seo_name}` para compatibilidad de plantillas.

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

Cuando hay valor personalizado, el módulo actualiza:

- `category.name` (título visible/H1 en categoría)
- `listing.label`
- `psseonames_seo_name` (variable Smarty)

Así logra que el H1 de categoría use el Nombre Seo sin tocar core del tema.

## Estructura rápida del proyecto

- `psseonames.php`: hooks, validación, orquestación funcional.
- `src/Repository/SeoCategoryNameRepository.php`: acceso a datos.
- `sql/install.php` / `sql/uninstall.php`: ciclo de vida de tabla.
- `ARCHITECTURE.md`: decisiones técnicas y flujo interno.
- `CONTRIBUTING.md`: política obligatoria del repositorio.
