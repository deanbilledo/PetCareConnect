<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UpdateMigrationsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('migrations')->insert([
            'migration' => 'xxxx_xx_xx_add_profile_photo_to_pets_table',
            'batch' => 1  // Use the appropriate batch number
        ]);
    }
} 