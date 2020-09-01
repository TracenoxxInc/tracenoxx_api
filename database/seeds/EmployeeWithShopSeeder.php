<?php

use App\Models\Employee;
use App\Models\Shop\Shop;
use Illuminate\Database\Seeder;

class EmployeeWithShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $shopsCount = 10;
        $managersCount = 50;
        $employeesCount = 500;

        // create some shops
        $shops = factory(Shop::class, $shopsCount)->create();
        $this->command->info("{$shopsCount} shops have been created successfully");

        // create some managers
        $managers = factory(Employee::class, $managersCount)->create();
        $this->command->info("{$managersCount} managers have been created successfully");

        // create some employees under the above managers
        $employees = collect();
        for ($i = 0; $i < $employeesCount; $i++) {
            $employee = factory(Employee::class)->create([
                'manager_id' => $managers->random()->id
            ]);
            $employees->push($employee);
        }
        $this->command->info("{$employeesCount} employees have been created successfully");

        // sync the employees with shops
        $employees->each(function ($employee, $key) use ($shops) {
            $employee->shops()->sync($shops->random(rand(1, 3))->pluck('id'));
        });
    }
}
