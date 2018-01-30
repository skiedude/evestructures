<?php

use Illuminate\Database\Seeder;

class DemoCharactersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('demo_characters')->delete();
        
        \DB::table('demo_characters')->insert(array (
            0 => 
            array (
                'id' => 1,
                'character_id' => 2112565794,
                'corporation_id' => 98544069,
                'corporation_name' => 'BKH',
                'character_name' => 'Brock_Khans',
                'access_token' => '00000',
                'refresh_token' => '1111111',
                'expires' => 2222222,
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
            1 => 
            array (
                'id' => 5,
                'character_id' => 1435599763,
                'corporation_id' => 1234,
                'corporation_name' => 'eveskillboard',
                'character_name' => 'Brock_Khans',
                'access_token' => '00000',
                'refresh_token' => '1111111',
                'expires' => 2222222,
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
            2 => 
            array (
                'id' => 9,
                'character_id' => 2113338704,
                'corporation_id' => 12345,
                'corporation_name' => 'structures',
                'character_name' => 'Brock_Khans',
                'access_token' => '00000',
                'refresh_token' => '1111111',
                'expires' => 2222222,
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
            3 => 
            array (
                'id' => 10,
                'character_id' => 2113690051,
                'corporation_id' => 123456,
                'corporation_name' => 'Pew Pew',
                'character_name' => 'Brock_Khans',
                'access_token' => '00000',
                'refresh_token' => '1111111',
                'expires' => 2222222,
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
        ));
        
        
    }
}