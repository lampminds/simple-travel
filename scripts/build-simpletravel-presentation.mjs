/**
 * Builds SimpleTravel sales deck (operators & agencies).
 * Run: node scripts/build-simpletravel-presentation.mjs
 */
import pptxgen from 'pptxgenjs';
import { fileURLToPath } from 'url';
import path from 'path';
import fs from 'fs';

const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '..');
const outFile = path.join(root, 'docs', 'presentacion-simpletravel.pptx');
const img = (rel) => path.join(root, 'public', rel);

const C = {
    primary: '5369F8',
    primaryDark: '1E3A5F',
    dark: '0F172A',
    text: '1E293B',
    muted: '64748B',
    white: 'FFFFFF',
    sinBg: 'FEF2F2',
    sinText: '7F1D1D',
    sinTitle: 'B91C1C',
    conBg: 'ECFDF5',
    conText: '064E3B',
    conTitle: '047857',
};

function exists(p) {
    try {
        return fs.existsSync(p);
    } catch {
        return false;
    }
}

function addHeader(slide, kicker, title, subtitle = null) {
    slide.addText(kicker, {
        x: 0.6,
        y: 0.45,
        w: 12,
        h: 0.35,
        fontSize: 10,
        bold: true,
        color: C.primary,
        charSpacing: 2,
        uppercase: true,
    });
    slide.addText(title, {
        x: 0.6,
        y: 0.85,
        w: 12,
        h: 0.7,
        fontSize: 28,
        bold: true,
        color: C.text,
    });
    if (subtitle) {
        slide.addText(subtitle, {
            x: 0.6,
            y: 1.55,
            w: 11.5,
            h: 0.55,
            fontSize: 14,
            color: C.muted,
        });
    }
}

function addComparisonSlide(pres, { title, sin, con, imageRel }) {
    const slide = pres.addSlide();
    slide.background = { color: C.white };
    addHeader(slide, 'Comparación', title);

    const imagePath = imageRel ? img(imageRel) : null;
    const imageY = 2.15;
    if (imagePath && exists(imagePath)) {
        slide.addImage({
            path: imagePath,
            x: 3.2,
            y: imageY,
            w: 6.9,
            h: 2.35,
            sizing: { type: 'contain', w: 6.9, h: 2.35 },
        });
    }

    const boxY = imagePath && exists(imagePath) ? 4.65 : 2.2;
    const boxH = imagePath && exists(imagePath) ? 2.15 : 4.5;

    slide.addShape(pres.ShapeType.roundRect, {
        x: 0.6,
        y: boxY,
        w: 6.1,
        h: boxH,
        fill: { color: C.sinBg },
        line: { color: 'FECACA', width: 1 },
        rectRadius: 0.08,
    });
    slide.addText('Sin Simple Travel', {
        x: 0.85,
        y: boxY + 0.15,
        w: 5.6,
        h: 0.35,
        fontSize: 11,
        bold: true,
        color: C.sinTitle,
        uppercase: true,
    });
    slide.addText(sin, {
        x: 0.85,
        y: boxY + 0.5,
        w: 5.6,
        h: boxH - 0.65,
        fontSize: 12,
        color: C.sinText,
        valign: 'top',
    });

    slide.addShape(pres.ShapeType.roundRect, {
        x: 6.95,
        y: boxY,
        w: 6.1,
        h: boxH,
        fill: { color: C.conBg },
        line: { color: 'A7F3D0', width: 1 },
        rectRadius: 0.08,
    });
    slide.addText('Con Simple Travel', {
        x: 7.2,
        y: boxY + 0.15,
        w: 5.6,
        h: 0.35,
        fontSize: 11,
        bold: true,
        color: C.conTitle,
        uppercase: true,
    });
    slide.addText(con, {
        x: 7.2,
        y: boxY + 0.5,
        w: 5.6,
        h: boxH - 0.65,
        fontSize: 12,
        color: C.conText,
        valign: 'top',
    });
}

function addBulletList(slide, items, opts = {}) {
    const bullets = items.map((t) => ({ text: t, options: { bullet: true, breakLine: true } }));
    slide.addText(bullets, {
        x: opts.x ?? 0.6,
        y: opts.y ?? 2.2,
        w: opts.w ?? 6,
        h: opts.h ?? 4.5,
        fontSize: opts.fontSize ?? 15,
        color: opts.color ?? C.text,
        valign: 'top',
    });
}

async function main() {
    const pres = new pptxgen();
    pres.layout = 'LAYOUT_WIDE';
    pres.author = 'SimpleTravel';
    pres.title = 'SimpleTravel — Presentación';
    pres.subject = 'Sistema de gestión para operadores y agencias turísticas';

    // 1. Portada
    {
        const slide = pres.addSlide();
        slide.background = { color: C.dark };
        const logo = img('images/logo-light.png');
        if (exists(logo)) {
            slide.addImage({ path: logo, x: 5.9, y: 1.2, w: 1.5, h: 0.75, sizing: { type: 'contain', w: 1.5, h: 0.75 } });
        }
        slide.addText('Sistema de Gestión de Reservas Turísticas', {
            x: 0.8,
            y: 2.35,
            w: 11.7,
            h: 1.2,
            fontSize: 34,
            bold: true,
            color: C.white,
            align: 'center',
        });
        slide.addText(
            'Plataforma digital para operadores y agencias turísticas en Latinoamérica',
            {
                x: 1.5,
                y: 3.55,
                w: 10.3,
                h: 0.8,
                fontSize: 18,
                color: '94A3B8',
                align: 'center',
            },
        );
        slide.addShape(pres.ShapeType.roundRect, {
            x: 5.55,
            y: 4.55,
            w: 2.2,
            h: 0.45,
            fill: { color: '1E3A5F' },
            line: { color: C.primary, width: 1 },
            rectRadius: 0.2,
        });
        slide.addText('SimpleTravel', {
            x: 5.55,
            y: 4.58,
            w: 2.2,
            h: 0.4,
            fontSize: 12,
            color: 'A5B4FC',
            align: 'center',
        });
    }

    // 2. Intro
    {
        const slide = pres.addSlide();
        slide.background = { color: C.white };
        addHeader(
            slide,
            'El desafío',
            'Digitalizar la gestión de tu operación turística',
            'Cómo funciona hoy tu operador o agencia — y cómo puede transformarse con un sistema diseñado para operadores emisivos.',
        );
        slide.addText(
            [
                {
                    text: 'Muchos operadores y agencias dependen de ',
                    options: { color: C.text, fontSize: 15 },
                },
                {
                    text: 'Excel, WhatsApp y procesos dispersos',
                    options: { color: C.text, fontSize: 15, bold: true },
                },
                {
                    text: ' que ponen en riesgo márgenes, tiempos de respuesta y la coordinación del equipo.',
                    options: { color: C.text, fontSize: 15, breakLine: true },
                },
                {
                    text: 'En las siguientes diapositivas verás la diferencia entre operar sin y con SimpleTravel.',
                    options: { color: C.muted, fontSize: 14, breakLine: true },
                },
            ],
            { x: 0.6, y: 2.35, w: 6.2, h: 3.5, valign: 'top' },
        );
        const introImg = img('images/comparison/1-quoting.png');
        if (exists(introImg)) {
            slide.addImage({
                path: introImg,
                x: 7.1,
                y: 2.2,
                w: 5.8,
                h: 4.2,
                sizing: { type: 'contain', w: 5.8, h: 4.2 },
            });
        }
    }

    // 3–7 Comparaciones (orden digitalizar)
    const comparisons = [
        {
            title: 'Cotizaciones',
            imageRel: 'images/comparison/1-quoting.png',
            sin: 'Cotizaciones en Excel, múltiples versiones del mismo archivo y cálculos manuales que dependen de quien los armó. Cada modificación implica revisar fórmulas y conversiones de moneda.',
            con: 'Itinerarios estructurados en minutos, con tarifas actualizadas, conversiones automáticas y márgenes definidos por regla. Cada propuesta queda guardada, versionada y lista para enviar.',
        },
        {
            title: 'Control de márgenes',
            imageRel: 'images/comparison/3-profit.png',
            sin: 'Decisiones comerciales tomadas "a ojo". Márgenes poco claros y diferencias cambiarias que afectan la rentabilidad sin detectarlo a tiempo.',
            con: 'Visualización inmediata de rentabilidad por pasajero, grupo u operación completa. Reglas de markup y control de margen mínimo antes de confirmar una venta.',
        },
        {
            title: 'Liquidaciones y pagos',
            imageRel: 'images/comparison/4-payments.png',
            sin: 'Señas, saldos y pagos gestionados en documentos separados. Dificultad para conciliar lo cobrado con lo que se debe pagar a cada prestador.',
            con: 'Cada reserva conecta automáticamente ingresos y obligaciones. Cuentas corrientes claras, pagos parciales registrados y trazabilidad completa en múltiples monedas.',
        },
        {
            title: 'Tarifas y prestadores',
            imageRel: 'images/comparison/2-pricing-providers.png',
            sin: 'Precios guardados en correos, chats o planillas aisladas. Dudas constantes sobre cuál es la tarifa vigente y riesgo de cotizar valores desactualizados.',
            con: 'Tus prestadores registran y actualizan sus servicios en la plataforma. Tú seleccionas, aplicas tu markup y trabajas siempre sobre información centralizada y vigente.',
        },
        {
            title: 'Trabajo en equipo',
            imageRel: 'images/comparison/5-teamwork.png',
            sin: 'Información dispersa en conversaciones individuales. Confirmaciones y cambios que no quedan registrados para todo el equipo.',
            con: 'Vendedores, operaciones y administración trabajan sobre la misma base de datos. Estados actualizados en tiempo real y comunicación operativa centralizada.',
        },
    ];
    comparisons.forEach((c) => addComparisonSlide(pres, c));

    // 8. Solución
    {
        const slide = pres.addSlide();
        slide.background = { color: C.white };
        addHeader(
            slide,
            'SimpleTravel',
            'Infraestructura digital para operadores y agencias',
            'Sistema moderno, fácil de usar y personalizable que centraliza tu operación comercial.',
        );
        addBulletList(slide, [
            'Un solo lugar para cotizar, reservar, cobrar y liquidar',
            'Multi-moneda y multi-idioma (español, portugués e inglés)',
            'Diseñado para operadores emisivos en Latinoamérica',
            'Reemplaza Excel, WhatsApp y procesos dispersos',
        ], { y: 2.15, w: 6.3 });
        const hero = img('images/hero/saas1-es.png');
        const fallback = img('images/comparison/5-teamwork.png');
        const heroPath = exists(hero) ? hero : fallback;
        if (exists(heroPath)) {
            slide.addImage({
                path: heroPath,
                x: 7,
                y: 2,
                w: 5.9,
                h: 4.5,
                sizing: { type: 'contain', w: 5.9, h: 4.5 },
            });
        }
    }

    // 9. Gestión simple
    {
        const slide = pres.addSlide();
        slide.background = { color: C.white };
        addHeader(
            slide,
            'Ventajas',
            'Herramientas para una gestión simple',
            'Centraliza tu operación en una sola plataforma. Reportes y estadísticas en tiempo real.',
        );
        addBulletList(slide, [
            'Gestión unificada de operaciones y ventas',
            'Relación con clientes y coordinación con prestadores',
            'Sitio web de promoción y panel operativo: todo en uno',
            'Múltiples monedas e idiomas',
            'Cobranzas y pagos a prestadores integrados',
        ], { y: 2.15, w: 6.2 });
        const hero2 = img('images/hero/saas2.jpg');
        const fb = img('images/comparison/3-profit.png');
        const p = exists(hero2) ? hero2 : fb;
        if (exists(p)) {
            slide.addImage({ path: p, x: 7, y: 2, w: 5.9, h: 4.5, sizing: { type: 'contain', w: 5.9, h: 4.5 } });
        }
    }

    // 10. Características 1-3
    {
        const slide = pres.addSlide();
        slide.background = { color: C.white };
        addHeader(slide, 'Características', 'Mejor gestión. Mejor rendimiento', 'Funcionalidades para la operación turística profesional.');
        const features = [
            {
                t: 'Gestión integral para operadores y agencias LATAM',
                d: 'Centraliza reservas, cotizaciones, clientes, pagos y reportes en una sola plataforma estructurada.',
            },
            {
                t: 'Cotizaciones ágiles y precisas',
                d: 'Itinerarios en minutos con tarifas actualizadas, conversiones automáticas y propuestas versionadas.',
            },
            {
                t: 'Control total de márgenes',
                d: 'Márgenes mínimos, markups por producto o canal y rentabilidad visible antes de confirmar.',
            },
        ];
        features.forEach((f, i) => {
            const y = 2.1 + i * 1.55;
            slide.addShape(pres.ShapeType.rect, {
                x: 0.6,
                y,
                w: 0.08,
                h: 1.25,
                fill: { color: C.primary },
                line: { width: 0 },
            });
            slide.addText(f.t, { x: 0.85, y, w: 11.8, h: 0.4, fontSize: 14, bold: true, color: C.text });
            slide.addText(f.d, { x: 0.85, y: y + 0.42, w: 11.8, h: 0.75, fontSize: 12, color: C.muted });
        });
    }

    // 11. Características 4-5
    {
        const slide = pres.addSlide();
        slide.background = { color: C.white };
        addHeader(slide, 'Características', 'Liquidaciones e integración con prestadores');
        const items = [
            {
                t: 'Liquidaciones claras y sin inconsistencias',
                d: 'Cuentas corrientes de clientes y prestadores en el mismo sistema. Señas, pagos parciales y trazabilidad en múltiples monedas.',
            },
            {
                t: 'Integración con tus prestadores',
                d: 'Invita prestadores para que actualicen servicios y tarifas. Usa su información en cotizaciones sin duplicar datos ni perder control comercial.',
            },
        ];
        items.forEach((f, i) => {
            const y = 2.1 + i * 1.7;
            slide.addShape(pres.ShapeType.rect, {
                x: 0.6,
                y,
                w: 0.08,
                h: 1.4,
                fill: { color: C.primary },
                line: { width: 0 },
            });
            slide.addText(f.t, { x: 0.85, y, w: 6.2, h: 0.45, fontSize: 14, bold: true, color: C.text });
            slide.addText(f.d, { x: 0.85, y: y + 0.48, w: 6.2, h: 1, fontSize: 12, color: C.muted, valign: 'top' });
        });
        const hero3 = img('images/hero/saas3.jpg');
        const fb3 = img('images/comparison/4-payments.png');
        const p3 = exists(hero3) ? hero3 : fb3;
        if (exists(p3)) {
            slide.addImage({ path: p3, x: 7.2, y: 2.05, w: 5.7, h: 4.6, sizing: { type: 'contain', w: 5.7, h: 4.6 } });
        }
    }

    // 12. Modelo flexible (sin precios)
    {
        const slide = pres.addSlide();
        slide.background = { color: C.white };
        addHeader(
            slide,
            'Plataforma',
            'Modular, flexible y a la medida de tu operación',
            'SimpleTravel se adapta al tamaño y las necesidades de cada operador o agencia.',
        );
        const cards = [
            {
                title: 'Sistema modularizado',
                text: 'Activás solo las capacidades que tu operación necesita hoy, sin cargar funciones que no usás.',
            },
            {
                title: 'Flexible y escalable',
                text: 'Crece con tu equipo y tu volumen de operaciones, sin límites artificiales ni sorpresas.',
            },
            {
                title: 'Licenciamiento claro',
                text: 'Modelo por usuario y/o por módulo. Sin comisiones por venta ni cargos por transacción.',
            },
        ];
        cards.forEach((card, i) => {
            const x = 0.6 + i * 4.2;
            slide.addShape(pres.ShapeType.roundRect, {
                x,
                y: 2.35,
                w: 3.9,
                h: 3.6,
                fill: { color: 'F8FAFC' },
                line: { color: 'E2E8F0', width: 1 },
                rectRadius: 0.1,
            });
            slide.addText(card.title, {
                x: x + 0.25,
                y: 2.6,
                w: 3.4,
                h: 0.55,
                fontSize: 15,
                bold: true,
                color: C.primary,
                align: 'center',
            });
            slide.addText(card.text, {
                x: x + 0.25,
                y: 3.25,
                w: 3.4,
                h: 2.4,
                fontSize: 13,
                color: C.muted,
                align: 'center',
                valign: 'top',
            });
        });
        slide.addText(
            'Pagás de acuerdo a tu necesidad real: usuarios activos y módulos contratados.',
            {
                x: 0.6,
                y: 6.35,
                w: 12.1,
                h: 0.45,
                fontSize: 14,
                color: C.text,
                align: 'center',
                italic: true,
            },
        );
    }

    // 13. CTA
    {
        const slide = pres.addSlide();
        slide.background = { color: C.white };
        addHeader(slide, 'Próximo paso', 'Empezá a operar con SimpleTravel');
        slide.addShape(pres.ShapeType.roundRect, {
            x: 1.2,
            y: 2.2,
            w: 10.9,
            h: 3.5,
            fill: { color: 'EEF2FF' },
            line: { color: 'C7D2FE', width: 1 },
            rectRadius: 0.15,
        });
        slide.addText('Conocé la plataforma en acción', {
            x: 1.2,
            y: 2.65,
            w: 10.9,
            h: 0.55,
            fontSize: 22,
            bold: true,
            color: C.text,
            align: 'center',
        });
        slide.addText('Solicitá una demo y descubrí cómo centralizar cotizaciones, reservas y liquidaciones.', {
            x: 2,
            y: 3.25,
            w: 9.3,
            h: 0.6,
            fontSize: 14,
            color: C.muted,
            align: 'center',
        });
        slide.addText('simple-travel.com  ·  /pages/digitalizar-operador-turistico', {
            x: 1.2,
            y: 4.1,
            w: 10.9,
            h: 0.4,
            fontSize: 12,
            color: C.primary,
            align: 'center',
        });
        const logo = img('images/logo.png');
        if (exists(logo)) {
            slide.addImage({
                path: logo,
                x: 6.15,
                y: 5.95,
                w: 1.2,
                h: 0.6,
                sizing: { type: 'contain', w: 1.2, h: 0.6 },
            });
        }
    }

    fs.mkdirSync(path.dirname(outFile), { recursive: true });
    await pres.writeFile({ fileName: outFile });
    console.log('Written:', outFile);
}

main().catch((err) => {
    console.error(err);
    process.exit(1);
});
