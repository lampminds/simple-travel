# Base de conocimiento (KB) solo para clientes web

Este documento describe **cómo montar y mantener** la base de conocimiento que alimenta el asistente de IA para **personas que usan la web pública** (registro, cuenta, reservas, ayuda general, etc.).  
**No forma parte de este KB** la documentación técnica, de programación, de administración interna (panel Filament), ni detalles de implementación.

---

## 1. Objetivo y alcance

| Sí entra en el KB | No entra en el KB |
|-------------------|-------------------|
| Cómo crear cuenta, iniciar sesión, recuperar contraseña | Código, APIs, SQL, migraciones |
| Cómo elegir cuenta o panel según su rol (lenguaje claro) | Rutas o pantallas de `/smpl_adm` o similares |
| Qué puede hacer en la zona “mi cuenta” (tareas, datos de empresa, etc.) | Nombres de tablas, variables, logs |
| Dudas frecuentes sobre flujos visibles en la web | Configuración de servidores, `.env`, claves |
| Enlaces a páginas públicas (términos, privacidad, ayuda) si aplica | Documentación para desarrolladores |

**Regla simple:** si un cliente final **no lo ve o no lo necesita** en el navegador como usuario de la web, **no lo documentes** en este KB.

---

## 2. Contenido que conviene cubrir (ejemplos)

Priorizá temas según volumen de consultas reales. Ejemplos típicos:

- Acceso: registro, login, cierre de sesión, “olvidé mi contraseña”.
- Cuenta: qué es la empresa asociada, cambiar datos si la web lo permite, invitaciones si existen en producto.
- Navegación: dónde encontrar X (menús visibles al cliente).
- Flujos de negocio explicados en **lenguaje de usuario** (sin jerga interna).
- Políticas o textos legales **solo** como orientación (“podés leer los términos aquí”) con enlace a la página pública correspondiente.

Mantené cada artículo **centrado en una intención** (una duda o una tarea), para que el embedding y las búsquedas funcionen mejor.

---

## 3. Estructura en el sistema (recordatorio)

En base de datos (resumen conceptual):

- **Ítem de conocimiento:** clave estable única (ej. `login_forgot_password`), activo/inactivo.
- **Traducción por idioma:** título, cuerpo, resumen opcional, URL pública opcional, etiquetas opcionales, vector de embedding (se genera al guardar).

En el panel de administración usá el recurso de **Knowledge base (IA)** para crear y editar ítems y sus traducciones. Los embeddings se recalculan cuando cambian título, resumen o cuerpo (no hace falta tocarlos a mano).

---

## 4. Cómo redactar cada artículo (para clientes)

1. **Título:** la pregunta o la tarea, como la diría el usuario (“¿Cómo restablezco mi contraseña?”).
2. **Resumen breve (opcional):** 1–3 frases; ayuda al modelo a contextualizar sin repetir todo el cuerpo.
3. **Cuerpo:** pasos numerados si hay procedimiento; lenguaje directo; **vos** o **usted** según el tono del sitio.
4. **URL:** solo si existe una **página pública** útil (ej. formulario de contacto, términos). No pongas URLs de admin ni entornos internos.
5. **Etiquetas:** pocas palabras clave separadas por comas (`contraseña, acceso, cuenta`) para filtrar o agrupar después.
6. **Idiomas:** completá al menos los idiomas que la web ofrece al cliente (es/en/pt u otros que tengáis en catálogo).

Revisá que el texto **no mencione** herramientas internas (“Filament”, “migración”, “repo”, etc.).

---

## 5. Cómo ir alimentando el KB sin abrumarse

### Opción A — Manual en el panel

Ideal para pocas piezas o ajustes finos: alta directa en el recurso de knowledge base, revisión y publicación (`is_active`).

### Opción B — Borradores guiados por temas (recomendado a mediano plazo)

1. Definís una **lista corta de bloques** (ej. “acceso”, “cuenta y paneles”, “tareas de cuenta”).
2. Por bloque, alguien de producto o contenidos **enumera las dudas** que quieren cubrir.
3. Se redactan o mejoran los textos **pensando solo en la web pública**; si usáis ayuda externa (IA humana o asistente), el **brief** debe repetir: *solo cliente web, nada técnico*.
4. Carga en el panel o importación futura vía seeder/comando, si más adelante lo implementáis.

### Opción C — Revisión periódica

Cuando cambie un flujo visible en la web, actualizá el artículo correspondiente y guardá de nuevo para **regenerar el embedding** del idioma afectado.

---

## 6. Calidad y seguridad

- **Veracidad:** el texto debe coincidir con lo que la web hace hoy; si el producto cambia, actualizá el KB.
- **Datos sensibles:** no incluyas datos reales de clientes ni ejemplos con credenciales.
- **Alcance del asistente:** el modelo solo debe usar este KB como apoyo; si respondéis fuera del KB, igual conviene políticas de “no inventar” en el prompt del producto (fuera de este .md).

---

## 7. Resumen en una frase

**Montá un KB solo con artículos que explican la web pública al cliente final, en sus idiomas, sin documentación técnica ni interna; mantenelo alineado con el producto y revisalo cuando cambien los flujos visibles.**

Si más adelante queréis automatizar **borradores** a partir de textos ya públicos (`lang`, pantallas acordadas), puede hacerse con un proceso o comando acotado por **lista de temas** — siempre con **revisión humana** antes de publicar.
