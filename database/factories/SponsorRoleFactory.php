<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Smart2\CommandModel\Eloquent\SponsorRole::class, function (Faker $faker) {
    static $sponsor_id;

    return [
        'sponsor_id' => $sponsor_id,
        'permissions' => function () {
            $today = Carbon::today()->format('Y-m-d H:i:s');
            $after100years = Carbon::today()->addYears(100)->format('Y-m-d H:i:s');

            $permissions = [];

            foreach (\Config::get('permission.list') as $key => $val) {
                if (preg_match('/^smart2::/', $key)) {
                    $permissions[$key] = [
                        'contract' => [
                            'start' => $today,
                            'end' => $after100years,
                        ],
                    ];
                } else {
                    $permissions[$key] = [];
                }
            }

            return $permissions;
        },
    ];
});
