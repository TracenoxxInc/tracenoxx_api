<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\Product\ProductUnit;

$factory->define(ProductUnit::class, function (Faker $faker) {

    $multipliers = [1, 4, 12, 50, 100, 200, 500, 1000];

    return [
        'name' => $faker->unique()->company,
        'multiplier' => $multipliers[$faker->numberBetween(0, count($multipliers) - 1)]
    ];
});
