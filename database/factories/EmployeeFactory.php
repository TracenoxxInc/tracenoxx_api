<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\User;
use App\Models\Employee;
use Faker\Generator as Faker;

$factory->define(Employee::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class),
        'is_active' => $faker->boolean,
        'manager_id' => null
    ];
});
