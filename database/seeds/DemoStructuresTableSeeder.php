<?php

use Illuminate\Database\Seeder;

class DemoStructuresTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('demo_structures')->delete();
        
        \DB::table('demo_structures')->insert(array (
            0 => 
            array (
                'id' => 1,
                'character_id' => 2112565794,
                'corporation_id' => 98544069,
                'structure_id' => 1,
                'structure_name' => 'Thera - My New Home',
                'type_id' => 40340,
                'system_id' => 10001,
                'system_name' => 'Thera',
                'profile_id' => 1,
                'fuel_expires' => 'n/a',
                'fuel_time_left' => null,
                'fuel_days_left' => null,
                'unanchors_at' => 'n/a',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
                'type_name' => 'Upwell Palatine Keepstart',
            ),
            1 => 
            array (
                'id' => 2,
                'character_id' => 2112565794,
                'corporation_id' => 98544069,
                'structure_id' => 2,
                'structure_name' => 'Jita - Trade Hub',
                'type_id' => 35825,
                'system_id' => 10001,
                'system_name' => 'Jita',
                'profile_id' => 1,
                'fuel_expires' => '2018-01-29 17:53',
                'fuel_time_left' => '3d 10:06:42',
                'fuel_days_left' => 3,
                'unanchors_at' => 'n/a',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
                'type_name' => 'Raitaru',
            ),
            2 => 
            array (
                'id' => 3,
                'character_id' => 2112565794,
                'corporation_id' => 98544069,
                'structure_id' => 3,
                'structure_name' => 'Perimeter - Clothing Barn',
                'type_id' => 35826,
                'system_id' => 10001,
                'system_name' => 'Perimeter',
                'profile_id' => 1,
                'fuel_expires' => 'n/a',
                'fuel_time_left' => null,
                'fuel_days_left' => null,
                'unanchors_at' => '2019-01-31 04:20',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
                'type_name' => 'Azbel',
            ),
            3 => 
            array (
                'id' => 4,
                'character_id' => 1435599763,
                'corporation_id' => 1234,
                'structure_id' => 4,
                'structure_name' => 'Kinakka - Tears',
                'type_id' => 35834,
                'system_id' => 10001,
                'system_name' => 'Kinakka',
                'profile_id' => 1,
                'fuel_expires' => '2018-09-23 03:30',
                'fuel_time_left' => '35d 12:12:12',
                'fuel_days_left' => 35,
                'unanchors_at' => 'n/a',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
                'type_name' => 'Keepstar',
            ),
            4 => 
            array (
                'id' => 5,
                'character_id' => 1435599763,
                'corporation_id' => 1234,
                'structure_id' => 5,
                'structure_name' => 'Kinakka - Church',
                'type_id' => 35834,
                'system_id' => 10001,
                'system_name' => 'Kinakka',
                'profile_id' => 1,
                'fuel_expires' => 'n/a',
                'fuel_time_left' => null,
                'fuel_days_left' => null,
                'unanchors_at' => 'n/a',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
                'type_name' => 'Keepstar',
            ),
            5 => 
            array (
                'id' => 6,
                'character_id' => 2113690051,
                'corporation_id' => 123456,
                'structure_id' => 6,
                'structure_name' => 'Rancer - BRONY PALACE',
                'type_id' => 35833,
                'system_id' => 10001,
                'system_name' => 'Rancer',
                'profile_id' => 1,
                'fuel_expires' => '2018-11-25 01:30',
                'fuel_time_left' => '0d 02:40:12',
                'fuel_days_left' => 0,
                'unanchors_at' => '2018-11-25 01:31',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
                'type_name' => 'Fortizar',
            ),
        ));
        
        
    }
}
