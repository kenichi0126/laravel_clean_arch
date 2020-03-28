<?php

use Faker\Generator as Faker;

$factory->define(Smart2\CommandModel\Eloquent\MemberSystemSetting::class, function (Faker $faker) {
    static $member_id;
    static $conv_15_sec_flag;
    static $aggregate_setting;
    return [
        'member_id' => $member_id ?? $member_id = $faker->randomDigitNotNull,
        'conv_15_sec_flag' => $conv_15_sec_flag ?? $conv_15_sec_flag = $faker->boolean,
        'aggregate_setting' => $aggregate_setting ?? $aggregate_setting = $faker->name,
    ];
});
