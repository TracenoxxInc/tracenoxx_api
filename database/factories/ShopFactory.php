<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Shop\Shop;
use Faker\Generator as Faker;

$factory->define(Shop::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(2, true)
    ];
});
