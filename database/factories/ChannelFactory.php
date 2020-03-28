<?php

use Faker\Generator as Faker;
use Smart2\CommandModel\Eloquent\Channel;

$factory->define(Channel::class, function (Faker $faker) {
    return [
        'region_id' => 1,
        'type' => 'dt',
        'button_number' => null,
        'code_name' => $faker->unique()->name,
        'display_name' => $faker->unique()->name,
        'created_at' => null,
        'updated_at' => null,
        'position' => null,
        'mdata_service_id' => null,
        'with_commercials' => null,
        'hdy_channel_code' => null,
        'hdy_channel_name' => null,
        'hdy_type_code' => null,
        'hdy_report_targeted' => 1,
        'report_targeted' => 0,
        'network' => null,
        'division' => 'dt1',
    ];
});
