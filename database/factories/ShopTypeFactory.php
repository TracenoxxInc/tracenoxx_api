<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Shop\ShopType as ShopShopType;
use Faker\Generator as Faker;

$factory->define(ShopShopType::class, function (Faker $faker) {
    return [
        'name' => $faker->unique()->sentence(2, true),
        'description' => $faker->paragraph(4, true),
        'image' => $faker->imageUrl(640, 480)
    ];
});
