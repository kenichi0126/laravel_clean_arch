<?php

use Faker\Generator as Faker;

$factory->define(Smart2\CommandModel\Eloquent\MemberOriginalDiv::class, function (Faker $faker) {
    static $member_id;
    static $menu;
    static $division;
    static $target_date_from;
    static $target_date_to;
    static $display_order;
    static $original_div_edit_flag;
    return [
        'member_id' => $member_id ?? $member_id = $faker->randomDigitNotNull,
        'menu' => $menu ?? $menu = $faker->name,
        'division' => $division ?? $division = $faker->name,
        'target_date_from' => $target_date_from ?? $target_date_from = $faker->dateTime,
        'target_date_to' => $target_date_to ?? $target_date_to = $faker->dateTime,
        'display_order' => $display_order ?? $display_order = $faker->randomDigitNotNull,
        'original_div_edit_flag' => $original_div_edit_flag ?? $original_div_edit_flag = $faker->boolean,
    ];
});
