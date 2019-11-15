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
        $this->call(BehaviorsSeeder::class);
        $this->call(CulturesSeeder::class);
        $this->call(ActionStepsSeeder::class);
    }
}
