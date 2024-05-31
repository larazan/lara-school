<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('settings')->insert([
            [
                'type' => 'school_name',
                'message' => 'e-School',
            ],
            [
                'type' => 'school_email',
                'message' => 'eschool@gmail.com',
            ],
            [
                'type' => 'school_phone',
                'message' => '9876543210',
            ],
            [
                'type' => 'school_address',
                'message' => 'India',
            ],
            [
                'type' => 'time_zone',
                'message' => 'Asia/Kolkata',
            ],
            [
                'type' => 'date_formate',
                'message' => 'd-m-Y',
            ],
            [
                'type' => 'time_formate',
                'message' => 'h:i A',
            ],
            [
                'type' => 'theme_color',
                'message' => '#4C5EA6',
            ],
            [
                'type' => 'update_warning_modal',
                'message' => 1
            ]
        ]);
    }
}
