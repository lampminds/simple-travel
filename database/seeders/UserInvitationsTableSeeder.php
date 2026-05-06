<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserInvitationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('user_invitations')->delete();



    }
}
