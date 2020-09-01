<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Version\Version;
use App\User;
use Faker\Generator as Faker;

$factory->define(Version::class, function (Faker $faker) {
    return [
        'user_id' => factory(User::class)
    ];
});
