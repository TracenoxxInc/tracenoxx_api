<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product\Product;
use App\Models\Shop\Shop;
use App\Models\Stock\Stock;
use Faker\Generator as Faker;

$factory->define(Stock::class, function (Faker $faker) {
    return [
        'quantity' => $faker->numberBetween(1, 5000),
        'shop_id' => factory(Shop::class)->create(),
        'product_id' => factory(Product::class)->create()
    ];
});
