<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * New provider service type "combo": a parent service whose variants are
     * composed of child service variants (see service_components).
     *
     * The existing "package" type is left untouched; packages remain the
     * operator catalogue concept.
     */
    public function up(): void
    {
        $exists = DB::table('cat_service_types')->where('code', 'combo')->exists();

        if ($exists) {
            return;
        }

        $serviceTypeId = DB::table('cat_service_types')->insertGetId([
            'code' => 'combo',
            'sort_order' => 9999,
            'active' => true,
        ]);

        // Language ids follow the catalogue convention: 1=en, 2=es, 3=pt.
        $translations = [
            1 => ['name' => 'Combo', 'description' => 'Bundled services sold as one product'],
            2 => ['name' => 'Combo', 'description' => 'Servicios combinados que se venden como un solo producto'],
            3 => ['name' => 'Combo', 'description' => 'Serviços combinados vendidos como um único produto'],
        ];

        foreach ($translations as $languageId => $translation) {
            DB::table('cat_service_type_translations')->insert([
                'service_type_id' => $serviceTypeId,
                'language_id' => $languageId,
                'name' => $translation['name'],
                'description' => $translation['description'],
            ]);
        }
    }

    public function down(): void
    {
        $serviceTypeId = DB::table('cat_service_types')->where('code', 'combo')->value('id');

        if ($serviceTypeId === null) {
            return;
        }

        DB::table('cat_service_type_translations')->where('service_type_id', $serviceTypeId)->delete();
        DB::table('cat_service_types')->where('id', $serviceTypeId)->delete();
    }
};
