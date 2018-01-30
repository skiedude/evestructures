<?php

use Illuminate\Database\Seeder;

class DemoStructureStatesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('demo_structure_states')->delete();
        
        \DB::table('demo_structure_states')->insert(array (
            0 => 
            array (
                'id' => 1,
                'structure_id' => 1,
                'character_id' => 2112565794,
                'state_timer_start' => '2018-01-24 07:00',
                'state_timer_end' => '2018-01-31 04:00',
                'created_at' => '2018-01-29 05:17:13',
                'updated_at' => '2018-01-29 05:17:13',
            ),
            1 => 
            array (
                'id' => 2,
                'structure_id' => 2,
                'character_id' => 2112565794,
                'state_timer_start' => '2018-01-01 07:00',
                'state_timer_end' => '2018-01-31 04:00',
                'created_at' => '2018-01-29 05:17:13',
                'updated_at' => '2018-01-29 05:17:13',
            ),
            2 => 
            array (
                'id' => 3,
                'structure_id' => 3,
                'character_id' => 2112565794,
                'state_timer_start' => '2018-05-18 07:00',
                'state_timer_end' => '2018-06-24 04:00',
                'created_at' => '2018-01-29 05:17:13',
                'updated_at' => '2018-01-29 05:17:13',
            ),
            3 => 
            array (
                'id' => 4,
                'structure_id' => 4,
                'character_id' => 1435599763,
                'state_timer_start' => '2018-05-18 07:00',
                'state_timer_end' => '2018-06-24 04:00',
                'created_at' => '2018-01-29 05:17:13',
                'updated_at' => '2018-01-29 05:17:13',
            ),
            4 => 
            array (
                'id' => 5,
                'structure_id' => 5,
                'character_id' => 1435599763,
                'state_timer_start' => '2018-05-18 07:00',
                'state_timer_end' => '2018-06-24 04:00',
                'created_at' => '2018-01-29 05:17:13',
                'updated_at' => '2018-01-29 05:17:13',
            ),
            5 => 
            array (
                'id' => 6,
                'structure_id' => 6,
                'character_id' => 2113690051,
                'state_timer_start' => '2018-05-18 07:00',
                'state_timer_end' => '2018-06-24 04:00',
                'created_at' => '2018-01-29 05:17:13',
                'updated_at' => '2018-01-29 05:17:13',
            ),
        ));
        
        
    }
}