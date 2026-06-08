<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatFaqsTableSeeder extends Seeder
{
    /**
     * Sample FAQs for public pages (pricing, etc.). account_type_id null = all audiences.
     */
    public function run(): void
    {
        \DB::table('cat_faq_translations')->delete();
        \DB::table('cat_faqs')->delete();

        \DB::table('cat_faqs')->insert([
            [
                'id' => 1,
                'code' => 'what-is-simple-travel',
                'account_type_id' => null,
                'sort_order' => 10,
                'active' => 1,
                'notes' => 'Pricing / marketing — platform overview',
            ],
            [
                'id' => 2,
                'code' => 'who-can-use-the-platform',
                'account_type_id' => null,
                'sort_order' => 20,
                'active' => 1,
                'notes' => 'Pricing / marketing — account types',
            ],
            [
                'id' => 3,
                'code' => 'how-service-catalog-works',
                'account_type_id' => null,
                'sort_order' => 30,
                'active' => 1,
                'notes' => 'Pricing / marketing — catalog & wizard',
            ],
            [
                'id' => 4,
                'code' => 'provider-publish-and-offers',
                'account_type_id' => 2,
                'sort_order' => 40,
                'active' => 1,
                'notes' => 'Provider only — wizard, publication, commercial offers',
            ],
            [
                'id' => 5,
                'code' => 'operator-catalog-and-packages',
                'account_type_id' => 1,
                'sort_order' => 50,
                'active' => 1,
                'notes' => 'Operator only — relationships, catalogs, packages, price lists',
            ],
        ]);
    }
}
