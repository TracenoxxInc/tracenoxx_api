<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Brand\Brand;
use App\Models\Product\Product;
use App\Models\Product\ProductUnit;
use Faker\Generator as Faker;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(2, true),
        'model_number' => $faker->uuid,
        'product_unit_id' => factory(ProductUnit::class)->create(),
        'brand_id' => factory(Brand::class)->create()
    ];
});
