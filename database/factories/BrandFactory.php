<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Brand\Brand;
use Faker\Generator as Faker;

$factory->define(Brand::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->company
    ];
});
