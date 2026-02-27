# CONTRIBUTING

Este documento define la **política obligatoria** de contribución para `psseonames`.

## 1) Principios obligatorios

- No tocar core de PrestaShop.
- Mantener compatibilidad con **PrestaShop 1.7 / 8**.
- Mantener soporte **multiidioma** y **multitienda** en toda funcionalidad.
- Mantener código claro, desacoplado y mantenible.
- Evitar deuda técnica no justificada.

## 2) Regla de documentación obligatoria

Toda contribución debe mantener la documentación alineada con el comportamiento real del módulo.

### Obligatorio

1. Toda nueva funcionalidad debe actualizar `README.md`.
2. Toda decisión técnica relevante debe documentarse en `ARCHITECTURE.md`.
3. No se puede cerrar una tarea si la documentación no está alineada con el código entregado.

### Norma clave de mantenimiento vivo

- Cada vez que cambie el comportamiento funcional del módulo, se debe actualizar `README.md`.
- Cada vez que se detecte un patrón que evite errores futuros o acelere el desarrollo, se debe documentar en `ARCHITECTURE.md`.

## 3) Reglas de arquitectura y diseño

Toda PR debe cumplir estas reglas:

1. **Sin core changes**: no modificar archivos core de PrestaShop.
2. **Compatibilidad PS 1.7/8**: evitar APIs no compatibles o dependencias no soportadas.
3. **Multiidioma y multitienda**: obligatorio por diseño, no opcional.
4. **Sin queries en bucles**: diseñar acceso a datos para minimizar consultas repetidas.
5. **Repositorio/capa de acceso a datos**: centralizar lecturas/escrituras en repositorio.
6. **Upgrades de BBDD**: cuando cambie estructura de base de datos, usar sistema de upgrade del módulo (además de scripts de instalación inicial).
7. **No duplicar lógica**: reutilizar métodos y abstraer reglas comunes.
8. **Rendimiento**: evitar consultas innecesarias y aplicar caché por request cuando aplique.

## 4) Definition of Done (DoD)

Una tarea **NO** se considera terminada si ocurre cualquiera de estas condiciones:

- No está documentada.
- Introduce duplicación innecesaria.
- Reduce la claridad arquitectónica.
- Añade deuda técnica no justificada.
- Rompe o degrada compatibilidad PS 1.7/8.
- Ignora requisitos multiidioma/multitienda.

## 5) Checklist mínimo antes de cerrar una tarea

- [ ] Código sin tocar core.
- [ ] Funcionalidad validada en PS 1.7/8 (al menos verificación de compatibilidad técnica).
- [ ] Soporte multiidioma/multitienda preservado.
- [ ] Sin queries innecesarias ni queries dentro de bucles críticos.
- [ ] Persistencia implementada mediante repositorio/capa de datos.
- [ ] Si cambió la BBDD: migración/upgrade documentada e implementada.
- [ ] `README.md` actualizado si cambió comportamiento funcional.
- [ ] `ARCHITECTURE.md` actualizado si hubo decisión técnica o nuevo patrón útil.
- [ ] Checks ejecutados (mínimo `php -l` en archivos PHP modificados).

## 6) Flujo de trabajo recomendado

1. Implementar cambio mínimo viable.
2. Refactorizar para evitar duplicación.
3. Validar comportamiento y sintaxis.
4. Actualizar documentación obligatoria (`README.md`, `ARCHITECTURE.md`).
5. Preparar commit/PR con resumen funcional, técnico y de impacto.

---

Si una contribución no cumple este documento, debe considerarse incompleta hasta su corrección.
