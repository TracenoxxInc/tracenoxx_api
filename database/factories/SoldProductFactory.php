<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Product\Product;
use App\Models\SoldProduct\SoldProduct;
use App\Models\Transaction\Transaction;
use Faker\Generator as Faker;

$factory->define(SoldProduct::class, function (Faker $faker) {
    return [
        'quantity' => $faker->numberBetween(1, 50),
        'list_price' => $faker->numberBetween(1, 100000),
        'discount' => $faker->numberBetween(1, 100000),
        'product_id' => factory(Product::class)->create(),
        'transaction_id' => factory(Transaction::class)->create()
    ];
});
