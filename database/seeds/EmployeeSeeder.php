<?php

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $managersCount = 50;
        $employeesCount = 500;

        // create some managers
        $managers = factory(Employee::class, $managersCount)->create();
        $this->command->info("{$managersCount} managers have been created successfully");

        // create some employees under the above managers
        for ($i = 0; $i < $employeesCount; $i++) {
            factory(Employee::class)->create([
                'manager_id' => $managers->random()->id
            ]);
        }
        $this->command->info("{$employeesCount} employees have been created successfully");
    }
}
