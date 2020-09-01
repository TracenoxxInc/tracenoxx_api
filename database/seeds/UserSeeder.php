<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $usersCount = 1000;

        factory(App\User::class, $usersCount)->create();
        $this->command->info("{$usersCount} users have been created successfully");
    }
}
