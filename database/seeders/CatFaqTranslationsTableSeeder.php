<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatFaqTranslationsTableSeeder extends Seeder
{
    public function run(): void
    {
        \DB::table('cat_faq_translations')->delete();

        \DB::table('cat_faq_translations')->insert([
            // FAQ 1 — What is Simple Travel?
            [
                'id' => 1,
                'faq_id' => 1,
                'language_id' => 1,
                'question' => 'What is Simple Travel?',
                'answer' => 'Simple Travel is a B2B platform for tourism businesses. It connects tour operators, service providers and travel agencies in one workspace: shared catalogs, commercial relationships, price lists, and operational tools to sell and coordinate experiences, transfers, lodging, gastronomy and more.',
            ],
            [
                'id' => 2,
                'faq_id' => 1,
                'language_id' => 2,
                'question' => '¿Qué es Simple Travel?',
                'answer' => 'Simple Travel es una plataforma B2B para empresas de turismo. Conecta operadores turísticos, proveedores de servicios y agencias de viajes en un mismo entorno: catálogos compartidos, vínculos comerciales, listas de precios y herramientas operativas para vender y coordinar experiencias, traslados, alojamiento, gastronomía y más.',
            ],
            [
                'id' => 3,
                'faq_id' => 1,
                'language_id' => 3,
                'question' => 'O que é o Simple Travel?',
                'answer' => 'O Simple Travel é uma plataforma B2B para empresas de turismo. Conecta operadores turísticos, fornecedores de serviços e agências de viagens em um único ambiente: catálogos compartilhados, relações comerciais, listas de preços e ferramentas operacionais para vender e coordenar experiências, transfers, hospedagem, gastronomia e muito mais.',
            ],

            // FAQ 2 — Who can use the platform?
            [
                'id' => 4,
                'faq_id' => 2,
                'language_id' => 1,
                'question' => 'Who can use Simple Travel?',
                'answer' => 'The platform is designed for three business profiles: providers who publish and maintain their own services; tour operators who assemble offers from several suppliers; and agencies that work with operators or providers according to their commercial agreements. Each profile has its own dashboard, menus and workflows adapted to its role.',
            ],
            [
                'id' => 5,
                'faq_id' => 2,
                'language_id' => 2,
                'question' => '¿Quién puede usar Simple Travel?',
                'answer' => 'La plataforma está pensada para tres perfiles: proveedores que publican y mantienen sus servicios; operadores turísticos que arman productos a partir de varios suministradores; y agencias que trabajan con operadores o proveedores según sus acuerdos comerciales. Cada perfil tiene su propio panel, menús y flujos adaptados a su rol.',
            ],
            [
                'id' => 6,
                'faq_id' => 2,
                'language_id' => 3,
                'question' => 'Quem pode usar o Simple Travel?',
                'answer' => 'A plataforma foi pensada para três perfis: fornecedores que publicam e mantêm seus serviços; operadores turísticos que montam produtos a partir de vários fornecedores; e agências que trabalham com operadores ou fornecedores conforme seus acordos comerciais. Cada perfil tem seu próprio painel, menus e fluxos adaptados ao seu papel.',
            ],

            // FAQ 3 — Service catalog & wizard
            [
                'id' => 7,
                'faq_id' => 3,
                'language_id' => 1,
                'question' => 'How do I register services on the platform?',
                'answer' => 'Providers use the guided service wizard to create catalog entries step by step: type of service, description, variants, features, availability rules and publication status. Operators and agencies can then browse authorized catalogs, build packages, manage price lists and commercial offers without duplicating master data in spreadsheets.',
            ],
            [
                'id' => 8,
                'faq_id' => 3,
                'language_id' => 2,
                'question' => '¿Cómo registro servicios en la plataforma?',
                'answer' => 'Los proveedores usan el asistente guiado de servicios para cargar el catálogo paso a paso: tipo de servicio, descripción, variantes, características, reglas de disponibilidad y estado de publicación. Operadores y agencias pueden explorar catálogos autorizados, armar paquetes, gestionar listas de precios y ofertas comerciales sin duplicar la información maestra en planillas.',
            ],
            [
                'id' => 9,
                'faq_id' => 3,
                'language_id' => 3,
                'question' => 'Como cadastro serviços na plataforma?',
                'answer' => 'Os fornecedores usam o assistente guiado de serviços para criar o catálogo passo a passo: tipo de serviço, descrição, variantes, características, regras de disponibilidade e status de publicação. Operadores e agências podem consultar catálogos autorizados, montar pacotes, gerenciar listas de preços e ofertas comerciais sem duplicar dados mestres em planilhas.',
            ],

            // FAQ 4 — Provider only
            [
                'id' => 10,
                'faq_id' => 4,
                'language_id' => 1,
                'question' => 'As a provider, how do I publish services and reach operators?',
                'answer' => 'Create each service with the step-by-step wizard (description, variants, features and availability), then set it to published when it is ready. Define provider price lists and commercial offers so linked operators see consistent rates. When an operator accepts an offer, your catalog stays the single source of truth—updates to the service or prices flow from your account without maintaining parallel spreadsheets.',
            ],
            [
                'id' => 11,
                'faq_id' => 4,
                'language_id' => 2,
                'question' => 'Como proveedor, ¿cómo publico servicios y llego a operadores?',
                'answer' => 'Carga cada servicio con el asistente paso a paso (descripción, variantes, características y disponibilidad) y márcalo como publicado cuando esté listo. Define listas de precios de proveedor y ofertas comerciales para que los operadores vinculados vean tarifas coherentes. Cuando un operador acepta una oferta, tu catálogo sigue siendo la fuente única de verdad: los cambios en el servicio o en precios se gestionan desde tu cuenta sin planillas paralelas.',
            ],
            [
                'id' => 12,
                'faq_id' => 4,
                'language_id' => 3,
                'question' => 'Como fornecedor, como publico serviços e alcanço operadores?',
                'answer' => 'Crie cada serviço com o assistente passo a passo (descrição, variantes, características e disponibilidade) e publique quando estiver pronto. Defina listas de preços do fornecedor e ofertas comerciais para que operadores vinculados vejam tarifas consistentes. Quando um operador aceita uma oferta, seu catálogo continua sendo a fonte única da verdade — alterações no serviço ou nos preços partem da sua conta, sem planilhas paralelas.',
            ],

            // FAQ 5 — Operator only
            [
                'id' => 13,
                'faq_id' => 5,
                'language_id' => 1,
                'question' => 'As an operator, how do I use supplier catalogs and build packages?',
                'answer' => 'Establish commercial relationships with providers and review their authorized catalogs and service offers from your operator dashboard. Accept or negotiate offers, then compose operator packages and operator price lists on top of supplier data. Allocations and availability rules help you sell the same underlying service with your own commercial packaging while providers keep master catalog maintenance.',
            ],
            [
                'id' => 14,
                'faq_id' => 5,
                'language_id' => 2,
                'question' => 'Como operador, ¿cómo uso catálogos de proveedores y armo paquetes?',
                'answer' => 'Establece vínculos comerciales con proveedores y revisa sus catálogos autorizados y ofertas desde tu panel de operador. Acepta o negocia ofertas y arma paquetes de operador y listas de precios de operador sobre la base de los datos del proveedor. Las asignaciones y reglas de disponibilidad te permiten comercializar el mismo servicio con tu propia presentación mientras el proveedor mantiene el catálogo maestro.',
            ],
            [
                'id' => 15,
                'faq_id' => 5,
                'language_id' => 3,
                'question' => 'Como operador, como uso catálogos de fornecedores e monto pacotes?',
                'answer' => 'Estabeleça relações comerciais com fornecedores e consulte catálogos autorizados e ofertas no painel do operador. Aceite ou negocie ofertas e monte pacotes de operador e listas de preços de operador com base nos dados do fornecedor. Alocações e regras de disponibilidade permitem comercializar o mesmo serviço com sua própria apresentação enquanto o fornecedor mantém o catálogo mestre.',
            ],
        ]);
    }
}
