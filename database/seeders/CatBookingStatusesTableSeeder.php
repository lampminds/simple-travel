<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CatBookingStatusesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('cat_booking_statuses')->delete();
        
        \DB::table('cat_booking_statuses')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'main',
                'code' => 'pending_validation',
                'active' => 1,
                'sort_order' => 1,
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'main',
                'code' => 'pending_availability',
                'active' => 1,
                'sort_order' => 2,
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 'main',
                'code' => 'pending_payment',
                'active' => 1,
                'sort_order' => 3,
            ),
            3 => 
            array (
                'id' => 4,
                'type' => 'main',
                'code' => 'pending_confirmation',
                'active' => 1,
                'sort_order' => 4,
            ),
            4 => 
            array (
                'id' => 5,
                'type' => 'main',
                'code' => 'partially_confirmed',
                'active' => 1,
                'sort_order' => 5,
            ),
            5 => 
            array (
                'id' => 6,
                'type' => 'main',
                'code' => 'confirmed',
                'active' => 1,
                'sort_order' => 6,
            ),
            6 => 
            array (
                'id' => 7,
                'type' => 'main',
                'code' => 'in_progress',
                'active' => 1,
                'sort_order' => 7,
            ),
            7 => 
            array (
                'id' => 8,
                'type' => 'main',
                'code' => 'completed',
                'active' => 1,
                'sort_order' => 8,
            ),
            8 => 
            array (
                'id' => 9,
                'type' => 'main',
                'code' => 'cancelled',
                'active' => 1,
                'sort_order' => 9,
            ),
            9 => 
            array (
                'id' => 10,
                'type' => 'main',
                'code' => 'expired',
                'active' => 1,
                'sort_order' => 10,
            ),
            10 => 
            array (
                'id' => 11,
                'type' => 'main',
                'code' => 'rejected',
                'active' => 1,
                'sort_order' => 11,
            ),
            11 => 
            array (
                'id' => 12,
                'type' => 'item',
                'code' => 'draft',
                'active' => 1,
                'sort_order' => 1,
            ),
            12 => 
            array (
                'id' => 13,
                'type' => 'item',
                'code' => 'pending',
                'active' => 1,
                'sort_order' => 2,
            ),
            13 => 
            array (
                'id' => 14,
                'type' => 'item',
                'code' => 'requested',
                'active' => 1,
                'sort_order' => 3,
            ),
            14 => 
            array (
                'id' => 15,
                'type' => 'item',
                'code' => 'on_hold',
                'active' => 1,
                'sort_order' => 4,
            ),
            15 => 
            array (
                'id' => 16,
                'type' => 'item',
                'code' => 'confirmed',
                'active' => 1,
                'sort_order' => 5,
            ),
            16 => 
            array (
                'id' => 17,
                'type' => 'item',
                'code' => 'reconfirmed',
                'active' => 1,
                'sort_order' => 6,
            ),
            17 => 
            array (
                'id' => 18,
                'type' => 'item',
                'code' => 'cancelled',
                'active' => 1,
                'sort_order' => 7,
            ),
            18 => 
            array (
                'id' => 19,
                'type' => 'item',
                'code' => 'rejected',
                'active' => 1,
                'sort_order' => 8,
            ),
            19 => 
            array (
                'id' => 20,
                'type' => 'item',
                'code' => 'failed',
                'active' => 1,
                'sort_order' => 9,
            ),
            20 => 
            array (
                'id' => 21,
                'type' => 'item',
                'code' => 'expired',
                'active' => 1,
                'sort_order' => 10,
            ),
        ));
        
        
    }
}