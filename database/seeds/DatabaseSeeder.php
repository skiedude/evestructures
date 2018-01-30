<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(DemoCharactersTableSeeder::class);
        $this->call(DemoStructureServicesTableSeeder::class);
        $this->call(DemoStructureStatesTableSeeder::class);
        $this->call(DemoStructuresTableSeeder::class);
    }
}
