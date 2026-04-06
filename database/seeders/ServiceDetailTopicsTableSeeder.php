<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ServiceDetailTopicsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_service_detail_topics')->delete();
        
        \DB::table('cat_service_detail_topics')->insert(array (
            0 => 
            array (
                'id' => 1,
                'code' => 'service_description',
                'service_detail_topic_category_id' => 1,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'code' => 'service_highlights',
                'service_detail_topic_category_id' => 1,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            2 => 
            array (
                'id' => 3,
                'code' => 'service_included',
                'service_detail_topic_category_id' => 1,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            3 => 
            array (
                'id' => 4,
                'code' => 'service_not_included',
                'service_detail_topic_category_id' => 1,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            4 => 
            array (
                'id' => 5,
                'code' => 'service_recommendations',
                'service_detail_topic_category_id' => 1,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            5 => 
            array (
                'id' => 6,
                'code' => 'important_information',
                'service_detail_topic_category_id' => 1,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            6 => 
            array (
                'id' => 7,
                'code' => 'accessibility',
                'service_detail_topic_category_id' => 1,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            7 => 
            array (
                'id' => 8,
                'code' => 'sales_conditions',
                'service_detail_topic_category_id' => 2,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            8 => 
            array (
                'id' => 9,
                'code' => 'payment_conditions',
                'service_detail_topic_category_id' => 2,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            9 => 
            array (
                'id' => 10,
                'code' => 'deposit_policy',
                'service_detail_topic_category_id' => 2,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            10 => 
            array (
                'id' => 11,
                'code' => 'rate_conditions',
                'service_detail_topic_category_id' => 2,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            11 => 
            array (
                'id' => 12,
                'code' => 'taxes_and_fees',
                'service_detail_topic_category_id' => 2,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'code' => 'cancellation_policy',
                'service_detail_topic_category_id' => 3,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            13 => 
            array (
                'id' => 14,
                'code' => 'modification_policy',
                'service_detail_topic_category_id' => 3,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            14 => 
            array (
                'id' => 15,
                'code' => 'no_show_policy',
                'service_detail_topic_category_id' => 3,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            15 => 
            array (
                'id' => 16,
                'code' => 'refund_policy',
                'service_detail_topic_category_id' => 3,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            16 => 
            array (
                'id' => 17,
                'code' => 'operating_hours',
                'service_detail_topic_category_id' => 4,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            17 => 
            array (
                'id' => 18,
                'code' => 'duration',
                'service_detail_topic_category_id' => 4,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            18 => 
            array (
                'id' => 19,
                'code' => 'availability_period',
                'service_detail_topic_category_id' => 4,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            19 => 
            array (
                'id' => 20,
                'code' => 'seasonality',
                'service_detail_topic_category_id' => 4,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            20 => 
            array (
                'id' => 21,
                'code' => 'meeting_point',
                'service_detail_topic_category_id' => 4,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            21 => 
            array (
                'id' => 22,
                'code' => 'pickup_information',
                'service_detail_topic_category_id' => 4,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            22 => 
            array (
                'id' => 23,
                'code' => 'dropoff_information',
                'service_detail_topic_category_id' => 4,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            23 => 
            array (
                'id' => 24,
                'code' => 'children_policy',
                'service_detail_topic_category_id' => 5,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            24 => 
            array (
                'id' => 25,
                'code' => 'infant_policy',
                'service_detail_topic_category_id' => 5,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            25 => 
            array (
                'id' => 26,
                'code' => 'student_policy',
                'service_detail_topic_category_id' => 5,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            26 => 
            array (
                'id' => 27,
                'code' => 'senior_policy',
                'service_detail_topic_category_id' => 5,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            27 => 
            array (
                'id' => 28,
                'code' => 'group_policy',
                'service_detail_topic_category_id' => 5,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            28 => 
            array (
                'id' => 29,
                'code' => 'minimum_age',
                'service_detail_topic_category_id' => 5,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            29 => 
            array (
                'id' => 30,
                'code' => 'maximum_age',
                'service_detail_topic_category_id' => 5,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            30 => 
            array (
                'id' => 31,
                'code' => 'physical_requirements',
                'service_detail_topic_category_id' => 6,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            31 => 
            array (
                'id' => 32,
                'code' => 'health_requirements',
                'service_detail_topic_category_id' => 6,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            32 => 
            array (
                'id' => 33,
                'code' => 'equipment_required',
                'service_detail_topic_category_id' => 6,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            33 => 
            array (
                'id' => 34,
                'code' => 'documentation_required',
                'service_detail_topic_category_id' => 6,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            34 => 
            array (
                'id' => 35,
                'code' => 'what_to_bring',
                'service_detail_topic_category_id' => 7,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            35 => 
            array (
                'id' => 36,
                'code' => 'what_to_wear',
                'service_detail_topic_category_id' => 7,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            36 => 
            array (
                'id' => 37,
                'code' => 'weather_conditions',
                'service_detail_topic_category_id' => 7,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            37 => 
            array (
                'id' => 38,
                'code' => 'safety_information',
                'service_detail_topic_category_id' => 7,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            38 => 
            array (
                'id' => 39,
                'code' => 'transport_included',
                'service_detail_topic_category_id' => 8,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            39 => 
            array (
                'id' => 40,
                'code' => 'vehicle_information',
                'service_detail_topic_category_id' => 8,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            40 => 
            array (
                'id' => 41,
                'code' => 'pickup_area',
                'service_detail_topic_category_id' => 8,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            41 => 
            array (
                'id' => 42,
                'code' => 'transfer_conditions',
                'service_detail_topic_category_id' => 8,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            42 => 
            array (
                'id' => 43,
                'code' => 'checkin_time',
                'service_detail_topic_category_id' => 9,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            43 => 
            array (
                'id' => 44,
                'code' => 'checkout_time',
                'service_detail_topic_category_id' => 9,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            44 => 
            array (
                'id' => 45,
                'code' => 'late_checkout_policy',
                'service_detail_topic_category_id' => 9,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            45 => 
            array (
                'id' => 46,
                'code' => 'early_checkin_policy',
                'service_detail_topic_category_id' => 9,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            46 => 
            array (
                'id' => 47,
                'code' => 'room_cleaning',
                'service_detail_topic_category_id' => 9,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            47 => 
            array (
                'id' => 48,
                'code' => 'meal_plans',
                'service_detail_topic_category_id' => 9,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            48 => 
            array (
                'id' => 49,
                'code' => 'pets_policy',
                'service_detail_topic_category_id' => 9,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            49 => 
            array (
                'id' => 50,
                'code' => 'liability_information',
                'service_detail_topic_category_id' => 10,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            50 => 
            array (
                'id' => 51,
                'code' => 'insurance_information',
                'service_detail_topic_category_id' => 10,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            51 => 
            array (
                'id' => 52,
                'code' => 'terms_and_conditions',
                'service_detail_topic_category_id' => 10,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            52 => 
            array (
                'id' => 53,
                'code' => 'emergency_contact',
                'service_detail_topic_category_id' => 11,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            53 => 
            array (
                'id' => 54,
                'code' => 'operator_contact',
                'service_detail_topic_category_id' => 11,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            54 => 
            array (
                'id' => 55,
                'code' => 'support_information',
                'service_detail_topic_category_id' => 11,
                'visibility' => 'operator',
                'sort_order' => 9999,
                'active' => 1,
            ),
            55 => 
            array (
                'id' => 56,
                'code' => 'environmental_policy',
                'service_detail_topic_category_id' => 12,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            56 => 
            array (
                'id' => 57,
                'code' => 'local_regulations',
                'service_detail_topic_category_id' => 12,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
            57 => 
            array (
                'id' => 58,
                'code' => 'park_rules',
                'service_detail_topic_category_id' => 12,
                'visibility' => 'public',
                'sort_order' => 9999,
                'active' => 1,
            ),
        ));
        
        
    }
}