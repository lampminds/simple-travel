# Slider behavior on home page
_Exported on 2/25/2026 at 23:57:49 GMT-3 from Cursor (2.5.25)_

---

**User**

Quisiera que si el usuario tiene el foco (o el mouse) sobre el slider de la home page, el slider no avance automáticamente

---

**Cursor**


Comprobando qué vista usa la home para saber qué slider aplicar:



La home usa la vista `landings.saas`, que incluye el **hero-swiper**. Swiper permite pausar el autoplay al pasar el ratón con `pauseOnMouseEnter: true`. Añadiendo también pausa al recibir foco (teclado/tab) para cubrir ambos casos.



Resumen de los cambios en el slider de la home (vista `landings.saas`, hero-swiper):

1. **`pauseOnMouseEnter: true`** en la opción `autoplay`: el autoplay se pausa cuando el puntero entra sobre el slider y se reanuda al salir (comportamiento nativo de Swiper).

2. **Pausa por foco**: listeners `focusin` / `focusout` en el contenedor del hero-swiper para que, si el usuario da foco al slider (p. ej. con Tab), se pare el autoplay y se reanude al perder el foco.

El archivo tocado es `resources/js/swiper.js`. Para que se aplique en el navegador hay que recompilar los assets. En tu entorno Debian:

```bash
cd /volume_html/simple-travel && npm run build
```

O, en desarrollo:

```bash
cd /volume_html/simple-travel && npm run dev
```

Tras recompilar, al pasar el ratón o dar foco al slider de la home el avance automático se detendrá y se reanudará al quitar el ratón o el foco.

---

**User**

Necesito permitir que se cambie el idioma desde el website. Algo similar a lo que hicimos con la parte de admin en filament. Necesito un selector de idiomas en el header, y traducir todos los textos estáticos actuales de la home page utilizando files de traduccion (uno por cada pagina)

---

**Cursor**


Revisando la estructura de `lang`, el middleware web y el resto de la vista saas.



Implementando: creando la lista de tareas y aplicando los cambios.



Creating translation files: adding `nav` (en, es, pt) and `saas` (en, es, pt).



Creating `lang/en/nav.php`, `lang/es/nav.php`, `lang/pt/nav.php`, then the `saas` translation files.



Updating the navbar to use `__('nav.*')` and replacing all static texts in `saas.blade.php` with translation calls.



Updating the saas blade: title and hero section.



Replacing the pricing and FAQ sections:



Resumen de lo implementado:

---

## 1. **Selector de idioma en el header**
- **Ruta:** `GET /locale/{language}` (nombre: `locale`), con `language` = ID del registro en `languages`.
- **Controlador:** `App\Http\Controllers\SetLocaleController` — guarda el código de idioma en sesión y redirige atrás (misma lógica que el panel Filament).
- **Middleware:** `SetLocaleFromSession` se añadió al grupo `web` en `bootstrap/app.php` para que el front use el idioma de la sesión.
- **Vista:** `resources/views/layouts/partials/language-switcher.blade.php` — dropdown con los idiomas de la tabla `languages` (usando `lmpLanguage` para nombre y código).
- El switcher se incluye en la navbar (antes de Ingresar/Crear cuenta) y recibe `$languages` por un `View::composer` en `AppServiceProvider` para `layouts.partials.navbar`.

## 2. **Traducciones del header (nav)**
- **Un archivo por “área”:** `lang/{locale}/nav.php` (en, es, pt).
- Claves: `language`, `home`, `landings`, `pages`, `ui_kit`, `login`, `register`, `my_account`, `logout`, `search_placeholder`, `toggle_nav`.
- La navbar usa `__('nav.xxx')` en enlaces y botones (Home, Landings, Pages, UI Kit, Ingresar, Crear cuenta, Mi cuenta, Salir, placeholder de búsqueda, aria-label del toggler).

## 3. **Traducciones de la home (saas)**
- **Un archivo por página:** `lang/{locale}/saas.php` (en, es, pt) para la vista `landings.saas` (home).
- Todas las cadenas estáticas de la home pasan a `__('saas.xxx')` o `{!! __("saas.xxx") !!}` cuando llevan HTML (títulos del hero, subtítulos, etc.).
- Incluye: título de página, hero (3 slides), features, bloque “Smart Payroll”, lista de features, clientes, testimonios, planes (Starter/Professional/Enterprise), FAQ y “Contact Us”.

## 4. **Configuración**
- `config/app.php` ya tiene `locale` por defecto (p. ej. `es`) y `fallback_locale` (p. ej. `en`). Si no hay idioma en sesión, se usa el locale por defecto.

---

**Cómo probar en el servidor Debian**

1. Limpiar caché de configuración y vistas:
```bash
cd /volume_html/simple-travel && php artisan config:clear && php artisan view:clear
```

2. Entrar a la home y usar el selector de idioma del header (dropdown con idiomas). Al elegir uno se hace `GET /locale/{id}`, se guarda el idioma en sesión y la página se muestra en ese idioma.

**Nota:** Los idiomas disponibles son los que existan en la tabla `languages` (con su relación a `lmp_languages`). Si en el seeder solo hay español, inglés y portugués, solo esos tres aparecerán en el selector. Para más idiomas, añade registros en `languages` y crea los archivos `lang/{code}/nav.php` y `lang/{code}/saas.php` correspondientes.

