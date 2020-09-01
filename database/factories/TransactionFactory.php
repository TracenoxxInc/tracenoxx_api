<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Employee;
use App\Models\Shop\Shop;
use App\Models\Transaction\Transaction;
use App\User;
use Faker\Generator as Faker;

$factory->define(Transaction::class, function (Faker $faker) {
    return [
        'shop_id' => factory(Shop::class)->create(),
        'user_id' => factory(User::class)->create(),
        'employee_id' => factory(Employee::class)->create()
    ];
});
