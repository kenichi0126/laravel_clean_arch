<?php

use Faker\Generator as Faker;
use Smart2\CommandModel\Eloquent\Commercial;

$factory->define(Commercial::class, function (Faker $faker) {
    static $company_id;
    static $prog_id;

    // 放送開始日時
    $started_at = new DateTime();
    $started_at->setDate(2017, 1, 1);
    // 放送終了日時
    $ended_at = new DateTime();
    $ended_at->setDate(2017, 12, 31);

    return [
        'product_id' => $faker->unique()->buildingNumber,
        'cm_id' => $faker->unique()->buildingNumber,
        // TODO - shibuya: 番組名APIのテストのため
        'prog_id' => $prog_id ?? $prog_id = $faker->text($maxNbChars = 32),
        'started_at' => $started_at,
        'ended_at' => $ended_at,
        'date' => $started_at->format('Y-m-d'),
        // TODO - shibuya: 番組名APIテストのため
        'region_id' => 1,
        'time_box_id' => $faker->buildingNumber,
        'channel_id' => $faker->buildingNumber,
        'company_id' => $company_id ?? $faker->buildingNumber,
        'scene_id' => null,
        'duration' => $faker->buildingNumber,
        'program_title' => $faker->name,
        'genre_id' => null,
        'setting' => null,
        'talent' => null,
        'remarks' => null,
        'bgm' => null,
        'memo' => null,
        'first_date' => null,
        'ts_update' => $faker->dateTime,
        'calculated_at' => $faker->dateTime,
        'personal_viewing_number' => null,
        'personal_viewing_rate' => null,
        'household_viewing_number' => null,
        'household_viewing_rate' => null,
        'cm_type' => 0,
        'cm_type_updated_at' => $faker->dateTime,
    ];
});
