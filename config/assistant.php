<?php

/**
 * Website account assistant (RAG over ai_knowledge_*).
 *
 * Agent rules (tone, scope, no hallucination) live in system_prompt below.
 * Edit here or override via ASSISTANT_SYSTEM_PROMPT in .env (multiline via \n).
 */
return [

    'enabled' => (bool) env('ASSISTANT_ENABLED', true),

    'top_k' => (int) env('ASSISTANT_TOP_K', 5),

    /** Minimum cosine similarity (0–1) to include a knowledge article in context. */
    'min_score' => (float) env('ASSISTANT_MIN_SCORE', 0.15),

    'max_question_length' => (int) env('ASSISTANT_MAX_QUESTION_LENGTH', 2000),

    'max_history_messages' => (int) env('ASSISTANT_MAX_HISTORY_MESSAGES', 6),

    'history' => [
        /** Number of recent successful Q&A pairs to restore when opening the widget. */
        'load_limit' => (int) env('ASSISTANT_HISTORY_LOAD_LIMIT', 20),
        /** Bump when the browser storage payload shape changes. */
        'storage_version' => (int) env('ASSISTANT_HISTORY_STORAGE_VERSION', 1),
    ],

    /**
     * Optional quick-ask suggestions shown when the chat is empty (Spanish copy; translate later if needed).
     *
     * @var list<string>
     */
    'example_questions' => [
        '¿Cómo funciona la pantalla de Reservas en el panel Operador? Quiero saber qué veo en el listado y cómo confirmar o rechazar una reserva de una agencia.',
    ],

    /**
     * Per-user rate limit (global for now). Account/plan limits can replace this later.
     */
    'rate_limit' => [
        'per_minute' => (int) env('ASSISTANT_RATE_LIMIT_PER_MINUTE', 5),
    ],

    'system_prompt' => env('ASSISTANT_SYSTEM_PROMPT') ?: <<<'PROMPT'
Eres el asistente de ayuda de la plataforma para usuarios de empresas turísticas (prestadores, operadores y agencias).

Reglas obligatorias:
- Responde usando la información del contexto "Base de conocimiento" que se te proporciona en cada turno.
- Si uno o más artículos del contexto son relevantes para la pregunta, construye una respuesta práctica con pasos concretos a partir de ellos.
- Solo di que no tienes información cuando ningún artículo del contexto guarda relación con la pregunta.
- No inventes pantallas, menús, pasos ni políticas que no figuren en el contexto.
- No menciones administración interna, código, bases de datos, APIs ni herramientas de desarrollo.
- Usa español neutro latinoamericano, tono claro y práctico.
- Cuando describas un procedimiento, usa pasos numerados si aplica.
- Si hay URLs públicas en el contexto, puedes mencionarlas como referencia.
- No des asesoramiento legal, fiscal ni médico.

Tu objetivo es ayudar al usuario a usar su cuenta en la web (catálogo, relaciones, precios, reservas, etc.).
PROMPT,

];
