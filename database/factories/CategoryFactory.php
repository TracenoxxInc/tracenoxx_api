<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Category\Category;
use Faker\Generator as Faker;

$factory->define(Category::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->sentence(3, true),
        'parent_category_id' => null
    ];
});
