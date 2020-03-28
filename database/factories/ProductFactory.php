<?php

use Faker\Generator as Faker;
use Smart2\CommandModel\Eloquent\Product;

$factory->define(Product::class, function (Faker $faker) {
    return [
        'company_id' => $faker->unique()->buildingNumber,
        'name' => $faker->unique()->name,
    ];
});
