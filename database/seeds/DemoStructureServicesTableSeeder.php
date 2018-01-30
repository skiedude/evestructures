<?php

use Illuminate\Database\Seeder;

class DemoStructureServicesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('demo_structure_services')->delete();
        
        \DB::table('demo_structure_services')->insert(array (
            0 => 
            array (
                'id' => 1,
                'structure_id' => 1,
                'character_id' => 2112565794,
                'name' => 'Reprocessing',
                'state' => 'online',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
            1 => 
            array (
                'id' => 2,
                'structure_id' => 1,
                'character_id' => 2112565794,
                'name' => 'Clone Bay',
                'state' => 'online',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
            2 => 
            array (
                'id' => 3,
                'structure_id' => 1,
                'character_id' => 2112565794,
                'name' => 'Blueprint Copying',
                'state' => 'offline',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
            3 => 
            array (
                'id' => 4,
                'structure_id' => 2,
                'character_id' => 2112565794,
                'name' => 'Invention',
                'state' => 'offline',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
            4 => 
            array (
                'id' => 5,
                'structure_id' => 2,
                'character_id' => 2112565794,
                'name' => 'Market',
                'state' => 'offline',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
            5 => 
            array (
                'id' => 6,
                'structure_id' => 3,
                'character_id' => 2112565794,
                'name' => 'Time Efficiency Research',
                'state' => 'online',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
            6 => 
            array (
                'id' => 7,
                'structure_id' => 4,
                'character_id' => 1435599763,
                'name' => 'Invention',
                'state' => 'online',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
            7 => 
            array (
                'id' => 8,
                'structure_id' => 5,
                'character_id' => 1435599763,
                'name' => 'Blueprint Copying',
                'state' => 'offline',
                'created_at' => '2018-01-28 06:08:28',
                'updated_at' => '2018-01-29 17:53:18',
            ),
        ));
        
        
    }
}