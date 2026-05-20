# Proyecto de plataforma de turismo operativa (web)  
**Documento orientado a inversión — visión técnica y de producto**

*Versión orientativa. Las cifras monetarias, porcentajes y fechas concretas se indican como placeholders `[…]` para completar con acuerdo entre partes.*

---

## 1. Resumen ejecutivo

El proyecto consiste en el desarrollo de un **sistema web** orientado a la **operación y gestión** de servicios de turismo (reservas, catálogo, operación interna y administración), con foco en **eficiencia operativa** y **escalabilidad** frente a un crecimiento orgánico de demanda.

Al **mes [N] aproximadamente**, el avance de implementación alcanzó en torno al **30%** del alcance del desarrollo inicial, con un equipo reducido y experto, apoyado en **herramientas de inteligencia artificial** para acelerar tareas de diseño, revisión y productividad (sin sustituir el criterio técnico ni la responsabilidad del equipo humano).

La **estrategia de go-to-market inicial** no prevé inversión en publicidad: un **mentor** con presencia relevante en el **sector turístico de la región de Mesopotamia (Argentina)** facilita el **arranque operativo** sobre empresas del propio ecosistema, y la difusión posterior se apoya en **recomendación y boca a boca** en el sector.

Se busca un **inversor** que aporte **capital** a cambio de un **porcentaje de participación en el capital [X %]** (a definir en términos legales y de valoración). El capital se destina principalmente a **completar el desarrollo** entre aproximadamente el **mes 4 y el mes 12**, y a **cubrir costos recurrentes** (equipo e infraestructura) hasta alcanzar **autosostenimiento**: ingresos que **cubran gastos** (nómina de desarrollo e infraestructura) y, en la medida de lo posible, **los excedan**.

**Meta de producto:** un **MVP (producto mínimo viable) alrededor del mes 4**; luego **iteración y nuevas funcionalidades hasta aproximadamente el mes 12**, momento en el que se estima un **equilibrio o superávit** en la operación. El producto continuará evolucionando con nuevas capacidades según aparezcan oportunidades y demanda del mercado.

---

## 2. Propuesta de valor (alto nivel)

- **Operación centralizada en la web:** un solo sistema para flujos internos (administración, catálogo, operación) y experiencia alineada con un modelo de negocio B2B/B2B2C según se defina en comercialización.
- **Enfoque sectorial:** construido sobre necesidades reales de operadores turísticos, con **validación temprana** mediante operación en empresas del entorno del mentor.
- **Crecimiento orgánico:** boca a boca y red del sector, reduciendo la dependencia de adquisición de clientes vía pauta en la fase inicial.

---

## 3. Stack técnico (síntesis)

El producto se implementa como **aplicación web** sobre un stack moderno y ampliamente adoptado en la industria:

| Área | Tecnología (referencia) |
|------|-------------------------|
| Lenguaje / runtime | PHP 8.2+ |
| Framework | Laravel 12.x |
| Panel de administración | Filament 5.x |
| Base de datos | MySQL / MariaDB (entorno típico) |
| Complementos | Bibliotecas estándar para medios, permisos, registro de actividad, etc. |

*Detalle: el repositorio incluye integración con componentes de personalización y buenas prácticas de estructura Laravel (migraciones, modelos, recursos de administración).*

Esta base permite **iterar con rapidez**, **auditar cambios** y **escalar** el backend sin reescribir desde cero al incorporar integraciones (pagos, terceros, reporting) en fases posteriores.

---

## 4. Estado del desarrollo y equipo

- **Forma del producto:** hoy el esfuerzo se concentra en un **sistema 100% web** (no hay app nativa móvil en el alcance descrito; el acceso es vía navegador y diseño responsive según evolución del front).
- **Equipo actual:** **dos analistas programadores senior**, con apoyo de **herramientas de IA** en el flujo de trabajo (código, documentación, revisión).
- **Progreso referencial:** alrededor de **30%** del desarrollo inicial estimado, tras aproximadamente **dos meses** de trabajo sostenido — coherente con un arranque de producto y definición de dominio (entidades, reglas, administración).
- **Inversión en marketing:** **cero** en publicidad paga a la fecha; el canal inicial es el **ecosistema del mentor** (Mesopotamia) y luego **recomendación**.

---

## 5. Hoja de ruta orientada a inversión (mes 4 → mes 12)

Los hitos son **orientativos**; las fechas exactas y el desglose por sprint se ajustan con el plan de trabajo y el capital disponible.

| Fase | Ventana aproximada | Objetivo |
|------|--------------------|--------|
| **MVP** | Hacia el **mes 4** | Conjunto mínimo de funcionalidades que permita **operar en entorno real** (pilotaje con operador/es del ecosistema), con criterios de aceptación acordados. |
| **Post-MVP** | **Mes 4 → mes 12** | Incorporar funcionalidades de mayor profundidad (operación, reportes, integraciones, refinamiento UX, etc.) según prioridad de negocio. |
| **Sostenimiento** | **Hacia el mes 12** (estimación) | **Ingresos ≥ gastos** recurrentes (principalmente sueldos de desarrollo e infraestructura); objetivo de **superávit** cuando el volumen y el pricing lo permitan. |

Tras el mes 12, se espera **seguir desarrollando** nuevas capacidades; la narrativa de inversión se centra en **llegar con la “balanza positiva”** para reducir dependencia de capital externo en operación corriente.

---

## 6. Uso de capital y estructura de inversión (placeholders)

- **Aporte buscado:** `[MONTO]` (moneda: `[ARS / USD / …]`)  
- **Contrapartida:** participación en el capital social o instrumento equivalente, del orden de **`[X] %`** (a formalizar con asesoramiento legal y valoración).  
- **Destinos típicos del capital en la fase descrita:**  
  - Nómina del equipo de desarrollo (y eventual ampliación controlada).  
  - Infraestructura (hosting, bases de datos, backups, monitoreo, dominios, herramientas).  
  - Contingencia y ajustes de alcance.  

*No se incluyen en esta versión cifras de quemado mensual ni burn rate: conviene acordarlas con planilla de gastos real.*

---

## 7. Riesgos y mitigación

Ningún plan de producto está exento de incertidumbre. La intención de esta sección no es listar todo lo que podría fallar, sino **transparentar** los riesgos que el equipo considera más relevantes en la fase actual (arranque con validación sectorial, desarrollo web, equipo acotado) y **cómo se abordan** de forma práctica. Las medidas concretas se refinarán con el avance del roadmap y con el apoyo del capital.

### 7.1 Dependencia de pocos clientes iniciales

**En qué consiste:** al comenzar con operadores vinculados al ecosistema del mentor, una parte relevante de la carga y del aprendizaje recae en **pocas cuentas**. Eso acelera la validación, pero concentra el riesgo: un cambio de prioridades en un cliente clave o un retraso en la adopción puede impactar de forma desproporcionada en la percepción de avance y en el feedback del producto.

**Mitigación:** pilotos con **alcance y criterios de éxito** acotados; **ciclos cortos** de feedback con el negocio; registro explícito de requisitos y decisiones; y, a medida que el producto lo permita, **diversificación** controlada hacia nuevos operadores (siempre priorizando calidad de implementación sobre cantidad de logos).

### 7.2 Scope creep (expansión descontrolada del alcance)

**En qué consiste:** en proyectos B2B es frecuente que surjan pedidos de funcionalidades adicionales “para ayer”. Sin disciplina, el producto **se ensancha** sin cerrar un núcleo estable, retrasando el MVP y elevando costo y complejidad.

**Mitigación:** **roadmap priorizado** con visibilidad compartida; **MVP claramente delimitado**; clasificación de peticiones en *must have*, *should have* y *nice to have*; y decisión consciente de qué queda **fuera** de la siguiente entrega. El capital se alinea a hitos, no a una lista infinita de deseos.

### 7.3 Riesgos técnicos (deuda, calidad, incidentes)

**En qué consiste:** un stack moderno (Laravel, Filament, etc.) reduce riesgo, pero siguen existiendo **deuda técnica** si se prioriza velocidad sin criterio, **errores en producción** o **dificultad para evolucionar** el código más adelante.

**Mitigación:** convenciones de proyecto, **migraciones y modelo de datos** cuidado, pruebas automatizadas **donde aporten más valor** (reglas críticas), **entornos separados** (desarrollo / staging / producción según corresponda), y revisiones con apoyo de herramientas e IA **sin reemplazar** criterio del equipo. Backups, monitoreo básico y planes de respuesta frente a incidentes se incorporan de forma acorde a la criticidad del servicio.

### 7.4 Ritmo de ingresos y modelo comercial

**En qué consiste:** aun con un arranque por **boca a boca** y red sectorial, el **timing** al que arriban ingresos recurrentes puede no coincidir con el calendario de gastos (equipo, infraestructura). Eso afecta el camino hacia el **equilibrio** descrito en este documento.

**Mitigación:** definición clara de **unidad de valor** (qué paga el cliente y por qué), **esquema de precios** alineado a esa propuesta, y medición sencilla de señales comerciales (adopción, retención, satisfacción). Crecimiento orgánico se **fomenta y documenta** (referidos, casos de uso) para no depender solo de la intuición. Los placeholders de montos y plazos deben bajar a planilla cuando el equipo tenga cifras consensuadas con el inversor.

| Resumen | Mitigación en una frase |
|--------|-------------------------|
| Concentración en pocos clientes al inicio | Pilotos acotados, criterios de éxito, feedback iterativo, diversificación gradual. |
| Scope creep | MVP definido, prioridades explícitas, roadmap acotado a hitos financiados. |
| Técnica / operación | Buenas prácticas, entornos separados, pruebas focalizadas, cuidado de deuda. |
| Ingresos vs. gastos | Valor y pricing claros, métricas simples, crecimiento orgánico medido. |

---

## 8. Próximos pasos para inversor y equipo

1. Completar placeholders: **`[MONTO]`**, **`[X] %`**, y definición legal de instrumento.  
2. Alinear **definición de MVP** (lista corta de capacidades y criterios de “listo para producción” en pilotos).  
3. Revisar **proyección de gastos mensuales** (interna, no necesariamente pública en este documento) frente a **hipótesis de ingresos** por cliente / plan.  
4. Formalizar acuerdo con asesoría contable y legal.

---

*Documento preparado para acompañar conversaciones con inversores; no constituye oferta de inversión ni asesoramiento legal o financiero.*
